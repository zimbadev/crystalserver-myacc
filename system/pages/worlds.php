<?php global $db, $config, $twig, $status;
/**
 * Worlds (multiworld system)
 *
 * @package   MyAAC
 * @author    OpenTibiaBR
 * @copyright 2024 MyAAC
 * @link      https://github.com/opentibiabr/myaac
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Worlds';

// world selector submitted via POST -> pretty url redirect
if ($w = $_POST['world'] ?? null) {
  header('Location: ' . getLink('worlds') . '/' . urlencode($w));
  exit;
}

$world = null;
if ($worldName = $_GET['world'] ?? null) {
  $world = $db->query("SELECT * FROM `worlds` WHERE `name` = " . $db->quote(urldecode($worldName)))->fetch(PDO::FETCH_ASSOC) ?: null;
}

$allWorlds = $db->query("SELECT * FROM `worlds` ORDER BY `id` ASC")->fetchAll(PDO::FETCH_ASSOC);

// Build the online players list for the selected world (mirrors system/pages/online.php).
$players_data = [];
if ($world) {
  if ($config['account_country']) {
    require SYSTEM . 'countries.conf.php';
  }

  $promotion = $db->hasColumn('players', 'promotion') ? '`promotion`,' : '';

  $outfit_addons = false;
  $outfit = '';
  if ($config['online_outfit']) {
    $outfit = ', lookbody, lookfeet, lookhead, looklegs, looktype';
    if ($db->hasColumn('players', 'lookaddons')) {
      $outfit .= ', lookaddons';
      $outfit_addons = true;
    }
  }

  $skull_type = $db->hasColumn('players', 'skull_type') ? 'skull_type' : 'skull';
  $skull_time = $db->hasColumn('players', 'skull_time') ? 'skull_time' : 'skulltime';

  $worldId = (int)$world['id'];
  if ($db->hasTable('players_online')) { // tfs 1.0+
    $playersOnline = $db->query('SELECT `accounts`.`country`, `players`.`name`, `players`.`level`, `players`.`vocation`' . $outfit . ', `' . $skull_time . '` as `skulltime`, `' . $skull_type . '` as `skull` FROM `accounts`, `players`, `players_online` WHERE `players`.`id` = `players_online`.`player_id` AND `accounts`.`id` = `players`.`account_id` AND `players`.`world_id` = ' . $worldId . ' ORDER BY `players`.`name`');
  } else {
    $playersOnline = $db->query('SELECT `accounts`.`country`, `players`.`name`, `players`.`level`, `players`.`vocation`' . $outfit . ', ' . $promotion . ' `' . $skull_time . '` as `skulltime`, `' . $skull_type . '` as `skull` FROM `accounts`, `players` WHERE `players`.`online` > 0 AND `accounts`.`id` = `players`.`account_id` AND `players`.`world_id` = ' . $worldId . ' ORDER BY `players`.`name`');
  }

  foreach ($playersOnline as $player) {
    $skull = '';
    if ($config['online_skulls'] && $player['skulltime'] > 0) {
      if ($player['skull'] == 3) {
        $skull = ' <img style="border: 0;" src="images/white_skull.gif"/>';
      } elseif ($player['skull'] == 4) {
        $skull = ' <img style="border: 0;" src="images/red_skull.gif"/>';
      } elseif ($player['skull'] == 5) {
        $skull = ' <img style="border: 0;" src="images/black_skull.gif"/>';
      }
    }

    if (isset($player['promotion']) && (int)$player['promotion'] > 0) {
      $player['vocation'] += ($player['promotion'] * $config['vocations_amount']);
    }

    $players_data[] = array(
      'name' => getPlayerLink($player['name']),
      'skull' => $skull,
      'player' => $player,
      'level' => $player['level'],
      'vocation' => $config['vocations'][$player['vocation']] ?? '',
      'country_image' => $config['account_country'] ? getFlagImage($player['country']) : null,
      'outfit' => $config['online_outfit'] ? $config['outfit_images_url'] . '?id=' . $player['looktype'] . ($outfit_addons ? '&addons=' . $player['lookaddons'] : '') . '&head=' . $player['lookhead'] . '&body=' . $player['lookbody'] . '&legs=' . $player['looklegs'] . '&feet=' . $player['lookfeet'] : null,
    );
  }
}

// Overall maximum players record.
$overallMax = '0 players';
if ($db->hasTable('server_config')) {
  $rec = $db->query("SELECT `value` FROM `server_config` WHERE `config` = " . $db->quote('players_record'))->fetch(PDO::FETCH_ASSOC);
  if ($rec) {
    $overallMax = number_format((int)$rec['value']) . ' players';
  }
}

$twig->display('worlds.html.twig', [
  '_worlds' => $allWorlds,
  'worlds' => $allWorlds,
  'world' => $world,
  'players' => $players_data,
  'overall_maximum' => $overallMax,
  // list view receives the full per-world status array; detail view receives the selected world's status
  'status' => $world ? ($status[$world['id']] ?? null) : $status,
]);

// character search box when viewing a specific world
if ($world) {
  $twig->display('character.search.form.html.twig');
}
