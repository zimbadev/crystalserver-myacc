<?php
require 'common.php';
require 'config.php';
require 'config.local.php';
require SYSTEM . 'functions.php';
require SYSTEM . 'init.php';
try {
    $tpl = $twig->load('account.create.html.twig');
    echo "Loaded OK\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
