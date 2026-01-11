<?php
/**
 * Bans
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2023 MyAAC
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Bans list';
$_page = 0;
$next_page = false;

if(!$config['bans_display_all'])
	echo 'Last ' . $config['bans_limit'] . ' banishments.<br/><br/>';

if($config['bans_display_all'])
{
	$_page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
	$offset = $_page * $config['bans_limit'] + 1;
}

$bans = $db->query('SELECT * FROM ' . $db->tableName('account_bans') . ' ORDER BY `banned_at` DESC');
if(!$bans->rowCount())
{
?>
<div class="TableContainer">
	<div class="CaptionContainer">
			<div class="CaptionInnerContainer">
				<span class="CaptionEdgeLeftTop" style="background-image:url(<?php echo $template_path; ?>/images/global/content/box-frame-edge.gif);"></span>
				<span class="CaptionEdgeRightTop" style="background-image:url(<?php echo $template_path; ?>/images/global/content/box-frame-edge.gif);"></span>
				<span class="CaptionBorderTop" style="background-image:url(<?php echo $template_path; ?>/images/global/content/table-headline-border.gif);"></span>
				<span class="CaptionVerticalLeft" style="background-image:url(<?php echo $template_path; ?>/images/global/content/box-frame-vertical.gif);"></span>
				<div class="Text">Banishments</div>
				<span class="CaptionVerticalRight" style="background-image:url(<?php echo $template_path; ?>/images/global/content/box-frame-vertical.gif);"></span>
				<span class="CaptionBorderBottom" style="background-image:url(<?php echo $template_path; ?>/images/global/content/table-headline-border.gif);"></span>
				<span class="CaptionEdgeLeftBottom" style="background-image:url(<?php echo $template_path; ?>/images/global/content/box-frame-edge.gif);"></span>
				<span class="CaptionEdgeRightBottom" style="background-image:url(<?php echo $template_path; ?>/images/global/content/box-frame-edge.gif);"></span>
			</div>
		</div><table class="Table1" cellpadding="0" cellspacing="0">

		<tbody><tr>
			<td>
				<div class="InnerTableContainer">
					<table style="width:100%;">
						<tbody><tr>
							<td><p>No players banned. </p>
							</td>
						</tr>
					</tbody></table>
				</div>
			</td>
		</tr>
	</tbody></table>
</div>
<?php
	return;
}
?>
<table border="0" cellspacing="1" cellpadding="4" width="100%">
	<tr align="center" bgcolor="<?php echo $config['vdarkborder']; ?>" class="white">
		<td><span style="color: white"><b>Nick</b></span></td>
		<td><span style="color: white"><b>Type</b></span></td>
		<td><span style="color: white"><b>Expires</b></span></td>
		<td><span style="color: white"><b>Reason</b></span></td>
		<td><span style="color: white"><b>Added by:</b></span></td>
	</tr>
<?php
$i = 0;
foreach($bans as $ban)
{
	$player_list = array();
	$samples = array_values($config['character_samples']);
	$quoted_samples = array_map(array($db, 'quote'), $samples);
	$samples_list = implode(',', $quoted_samples);
	
	$players_query = $db->query("SELECT `name` FROM `players` WHERE `account_id` = " . (int)$ban['account_id'] . " AND `name` NOT IN (" . $samples_list . ") AND `name` NOT LIKE '%Sample%' ORDER BY `level` DESC, `lastlogin` DESC")->fetchAll();
	
	if (count($players_query) > 0) {
		foreach ($players_query as $player_row) {
			$player_list[] = $player_row['name'];
		}
	} else {
		// fallback to any player if only samples exist
		$players_query = $db->query("SELECT `name` FROM `players` WHERE `account_id` = " . (int)$ban['account_id'] . " ORDER BY `level` DESC, `lastlogin` DESC")->fetchAll();
		foreach ($players_query as $player_row) {
			$player_list[] = $player_row['name'];
		}
	}

	if (empty($player_list)) {
		$player_list[] = 'Account ID: ' . $ban['account_id'];
	}

	foreach ($player_list as $player_name) {
		if($i++ > 100)
		{
			$next_page = true;
			break 2;
		}
?>
	<tr align="center" bgcolor="<?php echo getStyle($i); ?>">
		<td height="50" width="140"><?php echo strpos($player_name, 'Account ID:') === 0 ? $player_name : getPlayerLink($player_name); ?></td>
		<td>Account Banishment</td>
		<td>
<?php
			if($ban['expires_at'] == "-1" || $ban['expires_at'] == 0)
				echo 'Never';
			else
				echo date("H:i:s", $ban['expires_at']) . '<br/>' . date("d M Y", $ban['expires_at']);
?>
		</td>
		<td><?php echo getBanReason($ban['reason']); ?></td>
		<td>
<?php
			if($ban['banned_by'] == "0")
				echo 'Autoban';
			else {
				$admin_name = getPlayerNameByAccount($ban['banned_by']);
				echo !empty($admin_name) ? getPlayerLink($admin_name) : 'Player ID: ' . $ban['banned_by'];
			}

			echo '<br/>' . date("d.m.Y", $ban['banned_at']);
?>
		</td>
	</tr>
<?php
	}
}
?>
</table>
<table border="0" cellpadding="4" cellspacing="1" width="100%">
<?php
if($_page > 0)
	echo '<tr><td width="100%" align="right" valign="bottom"><a href="?subtopic=bans&page=' . ($_page - 1) . '" class="size_xxs">Previous Page</a></td></tr>';

if($next_page)
	echo '<tr><td width="100%" align="right" valign="bottom"><a href="?subtopic=bans&page=' . ($_page + 1) . '" class="size_xxs">Next Page</a></td></tr>';
?>
</table>
<?php
function getBanReason($reason)
{
	if(!is_numeric($reason))
		return $reason;

	switch($reason)
	{
		case 0:
			return "Offensive Name";
		case 1:
			return "Invalid Name Format";
		case 2:
			return "Unsuitable Name";
		case 3:
			return "Name Inciting Rule Violation";
		case 4:
			return "Offensive Statement";
		case 5:
			return "Spamming";
		case 6:
			return "Illegal Advertising";
		case 7:
			return "Off-Topic Public Statement";
		case 8:
			return "Non-English Public Statement";
		case 9:
			return "Inciting Rule Violation";
		case 10:
			return "Bug Abuse";
		case 11:
			return "Game Weakness Abuse";
		case 12:
			return "Using Unofficial Software to Play";
		case 13:
			return "Hacking";
		case 14:
			return "Multi-Clienting";
		case 15:
			return "Account Trading or Sharing";
		case 16:
			return "Threatening Gamemaster";
		case 17:
			return "Pretending to Have Influence on Rule Enforcement";
		case 18:
			return "False Report to Gamemaster";
		case 19:
			return "Destructive Behaviour";
		case 20:
			return "Excessive Unjustified Player Killing";
		case 21:
			return "Invalid Payment";
		case 22:
			return "Spoiling Auction";
	}

	return "Unknown Reason";
}

function getBanType($typeId)
{
	switch($typeId)
	{
		case 1:
			return "IP Banishment";
		case 2:
			return "Namelock";
		case 3:
			return "Banishment";
		case 4:
			return "Notation";
		case 5:
			return "Deletion";
	}

	return "Unknown Type";
}
