<?php
/**
 * Records
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2023 MyAAC
 */
defined('MYAAC') or die('Direct access not allowed!');

$title = "Players Online Records";

// multiworld: optional world filter for the records (server_record is world scoped)
$worlds = $db->query("SELECT * FROM `worlds` ORDER BY `id` ASC")->fetchAll(PDO::FETCH_ASSOC);
$selectedWorld = null;
if ($wparam = $_POST['world'] ?? $_GET['world'] ?? null) {
	$wparam = urldecode($wparam);
	foreach ($worlds as $w) {
		if ($w['name'] === $wparam || (string)$w['id'] === (string)$wparam) { $selectedWorld = $w; break; }
	}
}
$recordHasWorld = $db->hasColumn('server_record', 'world_id');
$w_sql = ($selectedWorld && $recordHasWorld) ? ' WHERE `world_id` = ' . (int)$selectedWorld['id'] . ' ' : '';
$headerName = $selectedWorld['name'] ?? $config['lua']['serverName'];

echo '
<b><div style="text-align:center">Players online records on '.$headerName.'</div></b>';

if (count($worlds) > 1) {
	echo '<form method="post" action="" style="text-align:center;margin:6px 0;">World:
		<select name="world" onchange="this.form.submit()">
			<option value="">All Worlds</option>';
	foreach ($worlds as $w) {
		echo '<option value="' . htmlspecialchars($w['name']) . '"' . ($selectedWorld && $selectedWorld['id'] == $w['id'] ? ' selected' : '') . '>' . htmlspecialchars($w['name']) . '</option>';
	}
	echo '</select></form>';
}

echo '
<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%>
	<TR BGCOLOR="'.$config['vdarkborder'].'">
		<TD class="white"><b><div style="text-align:center">Players</div></b></TD>
		<TD class="white"><b><div style="text-align:center">Date</div></b></TD>
	</TR>';

	$i = 0;
	$records_query = $db->query('SELECT * FROM `server_record`' . $w_sql . ' ORDER BY `record` DESC LIMIT 50;');
	foreach($records_query as $data)
	{
		echo '<TR BGCOLOR=' . getStyle(++$i) . '>
			<TD><div style="text-align:center">' . $data['record'] . '</div></TD>
			<TD><div style="text-align:center">' . date("d/m/Y, G:i:s", $data['timestamp']) . '</div></TD>
		</TR>';
	}

echo '</TABLE>';
?>
