<?php global $status;
require '../common.php';
require SYSTEM . 'init.php';
require SYSTEM . 'functions.php';
require SYSTEM . 'status.php';
require SYSTEM . 'login.php';

$worldId = $_GET['world'] ?? 1;
$_status = $status[$worldId] ?? reset($status);

if (!admin()) {
  die('Access denied.');
}

if (!$_status['online']) {
  die('Offline');
}
?>
<b>Server</b>: <?php echo $_status['server'] . ' ' . $_status['serverVersion']; ?><br/>
<b>Version</b>: <?php echo $_status['clientVersion']; ?><br/><br/>

<b>Monsters</b>: <?php echo $_status['monsters']; ?><br/>
<b>Map</b>: <?php echo $_status['mapName']; ?>, <b>author</b>: <?php echo $_status['mapAuthor']; ?>, <b>size</b>: <?php echo $_status['mapWidth'] . ' x ' . $_status['mapHeight']; ?><br/>
<b>MOTD</b>: <?php echo $_status['motd']; ?><br/><br/>

<b>Last check</b>: <?php echo date('H:i:s', $_status['lastCheck']); ?>
