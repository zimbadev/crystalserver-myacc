<?php

use Twig\Environment as Twig_Environment;
use Twig\Extension\DebugExtension as Twig_DebugExtension;
use Twig\Loader\FilesystemLoader as Twig_FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

$dev_mode = config('env') === 'dev';
$twig_loader = new Twig_FilesystemLoader(SYSTEM . 'templates');
$twig = new Twig_Environment($twig_loader, [
  'cache' => CACHE . 'twig/',
  'auto_reload' => $dev_mode,
  'debug' => $dev_mode,
]);

$twig_loader->addPath(PLUGINS);

$twig->addGlobal('logged', false);
$twig->addGlobal('account_logged', new OTS_Account());

if ($dev_mode) {
  $twig->addExtension(new Twig_DebugExtension());
}
unset($dev_mode);

function myaac_twig_getStyle($i) { return getStyle($i); }
$function = new TwigFunction('getStyle', 'myaac_twig_getStyle');
$twig->addFunction($function);

function myaac_twig_getLink($s) { return getLink($s); }
$function = new TwigFunction('getLink', 'myaac_twig_getLink');
$twig->addFunction($function);

function myaac_twig_getPlayerLink($s, $p) { return getPlayerLink($s, $p); }
$function = new TwigFunction('getPlayerLink', 'myaac_twig_getPlayerLink');
$twig->addFunction($function);

function myaac_twig_getGuildLink($s, $p) { return getGuildLink($s, $p); }
$function = new TwigFunction('getGuildLink', 'myaac_twig_getGuildLink');
$twig->addFunction($function);

function myaac_twig_hook($context, $hook, array $params = []) {
    global $hooks;

    if (is_string($hook)) {
      if (defined($hook)) {
        $hook = constant($hook);
      } else {
        // plugin/template has a hook that this version of myaac does not support
        // just silently return
        return;
      }
    }

    $params['context'] = $context;
    $hooks->trigger($hook, $params);
}

$function = new TwigFunction(
  'hook',
  'myaac_twig_hook',
  ['needs_context' => true]
);
$twig->addFunction($function);

function myaac_twig_config($key) { return config($key); }
$function = new TwigFunction('config', 'myaac_twig_config');
$twig->addFunction($function);

function myaac_twig_getCustomPage($name) { $success = false; return getCustomPage($name, $success); }
$function = new TwigFunction('getCustomPage', 'myaac_twig_getCustomPage');
$twig->addFunction($function);

function myaac_twig_getWorldType($type) { return getWorldType($type); }
$function = new TwigFunction('getWorldType', 'myaac_twig_getWorldType');
$twig->addFunction($function);

function myaac_twig_getWorldName($id) { return getWorldName($id); }
$function = new TwigFunction('getWorldName', 'myaac_twig_getWorldName');
$twig->addFunction($function);

function myaac_twig_urlencode($s) { return urlencode($s); }
$filter = new TwigFilter('urlencode', 'myaac_twig_urlencode');

$twig->addFilter($filter);
unset($function, $filter);
