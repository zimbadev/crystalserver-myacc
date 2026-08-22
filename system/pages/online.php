<?php
/**
 * Online
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2023 MyAAC
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Who is online?';

// multiworld: resolve selected world (defaults to the first world)
$worlds = $db->query("SELECT * FROM `worlds` ORDER BY `id` ASC")->fetchAll(PDO::FETCH_ASSOC);
$selectedWorld = null;
if ($wname = $_POST['world'] ?? $_GET['world'] ?? null) {
	$wname = urldecode($wname);
	foreach ($worlds as $w) {
		if ($w['name'] === $wname) { $selectedWorld = $w; break; }
	}
}
if (!$selectedWorld && count($worlds) > 0) {
	$selectedWorld = $worlds[0];
}
$w_sql = $selectedWorld ? ' AND `players`.`world_id` = ' . (int)$selectedWorld['id'] . ' ' : '';

if($config['account_country'])
	require SYSTEM . 'countries.conf.php';

$promotion = '';
if($db->hasColumn('players', 'promotion'))
	$promotion = '`promotion`,';
$order = isset($_GET['order']) ? $_GET['order'] : 'name';
if(!in_array($order, array('country', 'name', 'level', 'vocation')))
	$order = $db->fieldName('name');
else if($order == 'country')
	$order = $db->tableName('accounts') . '.' . $db->fieldName('country');
else if($order == 'vocation')
	$order = $promotion . 'vocation ASC';

$skull_type = 'skull';
if($db->hasColumn('players', 'skull_type')) {
	$skull_type = 'skull_type';
}

$skull_time = 'skulltime';
if($db->hasColumn('players', 'skull_time')) {
	$skull_time = 'skull_time';
}

$outfit_addons = false;
$outfit = '';
if($config['online_outfit']) {
	$outfit = ', lookbody, lookfeet, lookhead, looklegs, looktype';
	if($db->hasColumn('players', 'lookaddons')) {
		$outfit .= ', lookaddons';
		$outfit_addons = true;
	}
}

if($config['online_vocations']) {
	$vocs = array();
	foreach($config['vocations'] as $id => $name) {
		$vocs[$id] = 0;
	}
}

if($db->hasTable('players_online')) // tfs 1.0
	$playersOnline = $db->query('SELECT `accounts`.`country`, `players`.`name`, `players`.`level`, `players`.`vocation`' . $outfit . ', `' . $skull_time . '` as `skulltime`, `' . $skull_type . '` as `skull` FROM `accounts`, `players`, `players_online` WHERE `players`.`id` = `players_online`.`player_id` AND `accounts`.`id` = `players`.`account_id` ' . $w_sql . ' ORDER BY ' . $order);
else
	$playersOnline = $db->query('SELECT `accounts`.`country`, `players`.`name`, `players`.`level`, `players`.`vocation`' . $outfit . ', ' . $promotion . ' `' . $skull_time . '` as `skulltime`, `' . $skull_type . '` as `skull` FROM `accounts`, `players` WHERE `players`.`online` > 0 AND `accounts`.`id` = `players`.`account_id` ' . $w_sql . ' ORDER BY ' . $order);

$players_data = array();
$explodeFlags = array();
$players = 0;
$data = '';

// Online invocations counter initializer
$voc_online_count = [
    'Sorcerer' => 0,
    'Druid' => 0,
    'Paladin' => 0,
    'Knight' => 0,
	'Monk' => 0,
];

foreach($playersOnline as $player){
	$skull = '';
	if($config['online_skulls'])
	{
		if($player['skulltime'] > 0)
		{
			if($player['skull'] == 3)
				$skull = ' <img style="border: 0;" src="images/white_skull.gif"/>';
			elseif($player['skull'] == 4)
				$skull = ' <img style="border: 0;" src="images/red_skull.gif"/>';
			elseif($player['skull'] == 5)
				$skull = ' <img style="border: 0;" src="images/black_skull.gif"/>';
		}
	}

	if(isset($player['promotion'])) {
		if((int)$player['promotion'] > 0)
			$player['vocation'] += ($player['promotion'] * $config['vocations_amount']);
	}

// Count online invocations based on IDs
    $vocId = $player['vocation'];
    if(in_array($vocId, [1, 5])) {
        $voc_online_count['Sorcerer']++;
    } elseif(in_array($vocId, [2, 6])) {
        $voc_online_count['Druid']++;
    } elseif(in_array($vocId, [3, 7])) {
        $voc_online_count['Paladin']++;
    } elseif(in_array($vocId, [4, 8])) {
        $voc_online_count['Knight']++;
	} elseif(in_array($vocId, [9, 10])) {
        $voc_online_count['Monk']++;	
    }

	$players_data[] = array(
		'name' => getPlayerLink($player['name']),
		'player' => $player,
		'level' => $player['level'],
		'vocation' => $config['vocations'][$player['vocation']],
		'country_image' => $config['account_country'] ? getFlagImage($player['country']) : null,
		'outfit' => $config['online_outfit'] ? $config['outfit_images_url'] . '?id=' . $player['looktype'] . ($outfit_addons ? '&addons=' . $player['lookaddons'] : '') . '&head=' . $player['lookhead'] . '&body=' . $player['lookbody'] . '&legs=' . $player['looklegs'] . '&feet=' . $player['lookfeet'] : null
	);

	if($config['online_vocations']) {
		$vocs[($player['vocation'] > $config['vocations_amount'] ? $player['vocation'] - $config['vocations_amount'] : $player['vocation'])]++;
	}
}

$record = '';
if($config['online_record']){
	$timestamp = false;
	if($db->hasTable('server_record')) {
		$query =
			$db->query(
				'SELECT `record`, `timestamp` FROM `server_record` WHERE `world_id` = ' . ($selectedWorld ? (int)$selectedWorld['id'] : (int)($config['lua']['worldId'] ?? 1)) .
				' ORDER BY `record` DESC LIMIT 1');
		$timestamp = true;
	}else if($db->hasTable('server_config')) { // tfs 1.0
		$query = $db->query('SELECT `timestamp`, `value` as `record` FROM `server_config` WHERE `config` = ' . $db->quote('players_record'));
	}else{
		$query = NULL;
	}

	if(isset($query) && $query->rowCount() > 0){
		$result = $query->fetch();
		$record = '' . $result['record'] . ' players<br><small>'.date('d/m/Y, H:i:s', strtotime($result['timestamp'])).'</small>';
	}
}

$twig->display('online.html.twig', array(
	'players' => $players_data,
	'record' => $record,
	'current_date' => date('d/m/Y'),
	'vocs' => isset($vocs) ? $vocs : [],
    'voc_online_count' => $voc_online_count,  // We pass the counters to the template
	'world' => $selectedWorld,
	'worlds' => $worlds,
	'status' => $selectedWorld ? ($status[$selectedWorld['id']] ?? []) : [],
));

//search bar
$twig->display('character.search.form.html.twig');
