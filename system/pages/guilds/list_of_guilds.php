<?php
/**
 * List of guilds
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @author    whiteblXK
 * @copyright 2023 MyAAC
 */
defined('MYAAC') or die('Direct access not allowed!');

// multiworld: optional world filter for the guild list
$worlds = $db->query("SELECT * FROM `worlds` ORDER BY `id` ASC")->fetchAll(PDO::FETCH_ASSOC);
$selectedWorld = null;
if ($wparam = $_POST['world'] ?? $_GET['world'] ?? null) {
    $wparam = urldecode($wparam);
    foreach ($worlds as $w) {
        if ($w['name'] === $wparam || (string)$w['id'] === (string)$wparam) { $selectedWorld = $w; break; }
    }
}
$guildHasWorld = $db->hasColumn('guilds', 'world_id');

$guilds_list = new OTS_Guilds_List();
$guilds_list->orderBy("name");
if ($selectedWorld && $guildHasWorld) {
    $filter = new OTS_SQLFilter();
    $filter->compareField('world_id', (int)$selectedWorld['id']);
    $guilds_list->setFilter($filter);
}

$guilds = array();
if(count($guilds_list) > 0)
{
    foreach ($guilds_list as $guild) {
        $guild_logo = $guild->getCustomField('logo_name');
        if (empty($guild_logo) || !file_exists('images/guilds/' . $guild_logo))
            $guild_logo = "default.gif";

        $description = $guild->getCustomField('description');
        $description_with_lines = str_replace(array("\r\n", "\n", "\r"), '<br />', $description, $count);
        if ($count < $config['guild_description_lines_limit'])
            $description = nl2br($description);

        $guildName = $guild->getName();
        $worldName = $guildHasWorld ? getWorldName((int)$guild->getCustomField('world_id')) : null;
        $guilds[] = array('name' => $guildName, 'logo' => $guild_logo, 'link' => getGuildLink($guildName, false), 'description' => $description, 'world_name' => $worldName);
    }
};

$twig->display('guilds.list.html.twig', array(
    'guilds' => $guilds,
    'logged' => isset($logged) ? $logged : false,
    'isAdmin' => admin(),
    'worlds' => $worlds,
    'world' => $selectedWorld,
    'guildHasWorld' => $guildHasWorld,
));
