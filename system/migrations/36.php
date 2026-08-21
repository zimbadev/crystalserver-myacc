<?php
// Multiworld system: make `myaac_config` world aware and register the "Worlds" menu entry.
// The `worlds` table itself belongs to the game server - it creates and seeds it on startup.
global $db;

$db->exec("SET FOREIGN_KEY_CHECKS=0;");

// myaac_config becomes world aware.
$db->exec("ALTER TABLE `" . TABLE_PREFIX . "config` ADD `world_id` INT(3) UNSIGNED NOT NULL DEFAULT 1 AFTER `value`;");
$db->exec("ALTER TABLE `" . TABLE_PREFIX . "config` DROP INDEX `name`;");
$db->exec("ALTER TABLE `" . TABLE_PREFIX . "config` ADD UNIQUE `unique_name_world` (`name`, `world_id`);");
$db->exec("ALTER TABLE `" . TABLE_PREFIX . "config` ADD FOREIGN KEY (`world_id`) REFERENCES `worlds` (`id`) ON DELETE CASCADE;");

// Add the "Worlds" entry right after "Characters" in the Community category (tibiacom template).
$db->exec("UPDATE `" . TABLE_PREFIX . "menu` SET `ordering` = `ordering` + 1 WHERE `template` = 'tibiacom' AND `category` = 3 AND `ordering` >= 1;");
$db->exec("INSERT INTO `" . TABLE_PREFIX . "menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Worlds', 'worlds', 3, 1);");

$db->exec("SET FOREIGN_KEY_CHECKS=1;");
