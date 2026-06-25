<?php
// Multiworld system: ensure `worlds` table exists (created by the game server normally),
// add `world_id` to `myaac_config` and register the "Worlds" menu entry.
global $db;

$db->exec("SET FOREIGN_KEY_CHECKS=0;");

// Foundation table (the game server creates it via its own migration; we ensure it for dev/standalone).
$db->exec("
	CREATE TABLE IF NOT EXISTS `worlds` (
		`id` int(3) UNSIGNED NOT NULL AUTO_INCREMENT,
		`name` varchar(80) NOT NULL,
		`type` enum('no-pvp','pvp','retro-pvp','pvp-enforced','retro-pvp-enforced') NOT NULL,
		`motd` varchar(255) NOT NULL DEFAULT '',
		`location` enum('Europe','North America','South America','Oceania') NOT NULL,
		`ip` varchar(15) NOT NULL,
		`port` int(5) UNSIGNED NOT NULL,
		`port_status` int(6) UNSIGNED NOT NULL,
		`creation` int(11) NOT NULL DEFAULT 0,
		CONSTRAINT `worlds_pk` PRIMARY KEY (`id`),
		CONSTRAINT `worlds_unique` UNIQUE (`name`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

// Seed a default world if the table is still empty (keeps the site usable before the server runs).
if ((int)$db->query("SELECT COUNT(*) FROM `worlds`")->fetchColumn() === 0) {
	$db->exec("
		INSERT INTO `worlds` (`name`, `type`, `motd`, `location`, `ip`, `port`, `port_status`, `creation`)
		VALUES ('CrystalServer', 'pvp', 'Welcome!', 'South America', '127.0.0.1', 7172, 97172, UNIX_TIMESTAMP());
	");
}

// myaac_config becomes world aware.
$db->exec("ALTER TABLE `" . TABLE_PREFIX . "config` ADD `world_id` INT(3) UNSIGNED NOT NULL DEFAULT 1 AFTER `value`;");
$db->exec("ALTER TABLE `" . TABLE_PREFIX . "config` DROP INDEX `name`;");
$db->exec("ALTER TABLE `" . TABLE_PREFIX . "config` ADD UNIQUE `unique_name_world` (`name`, `world_id`);");
$db->exec("ALTER TABLE `" . TABLE_PREFIX . "config` ADD FOREIGN KEY (`world_id`) REFERENCES `worlds` (`id`) ON DELETE CASCADE;");

// Add the "Worlds" entry right after "Characters" in the Community category (tibiacom template).
$db->exec("UPDATE `" . TABLE_PREFIX . "menu` SET `ordering` = `ordering` + 1 WHERE `template` = 'tibiacom' AND `category` = 3 AND `ordering` >= 1;");
$db->exec("INSERT INTO `" . TABLE_PREFIX . "menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Worlds', 'worlds', 3, 1);");

$db->exec("SET FOREIGN_KEY_CHECKS=1;");
