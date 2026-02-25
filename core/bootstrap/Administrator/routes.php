<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/*
|--------------------------------------------------------------------------
| SEF Build
|--------------------------------------------------------------------------
|
| Remove the base URI path. This will strip everything up to the bas
|
*/

/*
| SEF Rewrite
|
| Remove the base URI path. This will strip everything up to the base
*/
$router->rules('build')->append('base', function ($uri) {
    // Get the path data
    $route = $uri->getPath();

    $base = \Hubzero\Facades\App::get('request')->base(true);
    if (
        substr($base, -strlen(\Hubzero\Facades\App::get('client')->name)) != \Hubzero\Facades\App::get('client')->name
        && substr($base, -strlen(\Hubzero\Facades\App::get('client')->url)) != \Hubzero\Facades\App::get('client')->url
    ) {
        $base .= '/' . \Hubzero\Facades\App::get('client')->name;
    }

    // Add basepath to the uri
    $uri->setPath($base . '/' . $route);

    return $uri;
});

/*
|--------------------------------------------------------------------------
| Parse Rules
|--------------------------------------------------------------------------
|
| Rules to parse and route an incoming URL to a component
|
*/

/*
| Prepare URI
|
| Remove the base URI path. This will strip everything up to the bas
*/
$router->rules('parse')->append('prep', function ($uri) {
    \Hubzero\Facades\App::get('router')->forget('option');

    // Get the path
    $path = $uri->getPath();

    // Remove the base URI path.
    $path = substr_replace($path, '', 0, strlen(\Hubzero\Facades\App::get('request')->base(true)));

    // Remove prefix
    $path = str_replace('index.php', '', $path);

    // Set the route
    $uri->setPath(trim($path, '/'));

    return null;
});

/*
| Match by component
|
| Match the first segment of the URI by component name. If a match is
| found, the component's router will be loaded to continue parsing any
| further segments.
*/
$router->rules('parse')->append('component', function ($uri) {
    $option = $uri->getUriVar('option');
    if (is_array($option)) {
        $option = implode('', $option);
    }

    if (\Hubzero\Facades\User::isGuest() || !\Hubzero\Facades\User::authorise('core.login.admin')) {
        $option = 'com_login';
    }

    if (empty($option)) {
        if (strtoupper(\Hubzero\Facades\App::get('request')->method()) == 'POST') {
            $option = \Hubzero\Facades\App::get('request')->getCmd('option', '', 'post');
        }
    }

    if (empty($option)) {
        $option = 'com_cpanel';
    }

    $uri->setUriVar('option', $option);

    return true;
});
