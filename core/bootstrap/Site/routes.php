<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\App;
use Hubzero\Facades\Config;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Log;
use Hubzero\Facades\Request;

/*
|--------------------------------------------------------------------------
| SEF Build
|--------------------------------------------------------------------------
|
| Set some basic information based on Menu ItemId
|
*/
$router->rules('build')->append('content', function ($uri) {
    if (!App::has('menu.manager')) {
        return $uri;
    }

    // Set URI defaults
    $menu = App::get('menu.manager')->menu('site');

    // Get the itemid form the URI
    $itemid = $uri->getUriVar('Itemid');

    if (is_null($itemid)) {
        if ($option = $uri->getUriVar('option')) {
            $item  = $menu->getItem($uri->getUriVar('Itemid'));
            if (isset($item) && $item->component == $option) {
                $uri->setUriVar('Itemid', $item->id);
            }
        } else {
            if ($option = App::get('router')->get('option')) {
                $uri->setUriVar('option', $option);
            }

            if ($itemid = App::get('router')->get('Itemid')) {
                $uri->setUriVar('Itemid', $itemid);
            }
        }
    } else {
        if (!$uri->getUriVar('option')) {
            if ($item = $menu->getItem($itemid)) {
                $uri->setUriVar('option', $item->component);
            }
        }
    }
    return $uri;
});

/*
| Content
|
| Handle section, category, alias routing of com_content pages
*/
$router->rules('build')->append('content', function ($uri) {
    $route = $uri->getPath();
    $query = $uri->getQuery(true);

    if (
        !isset($query['option'])
        || (isset($query['option']) && $query['option'] == 'com_content'
            && isset($query['task']) && $query['task'] == 'view')
    ) {
        $segments = array();

        // Don't parse calls to other com_content views
        if (isset($query['view']) && $query['view'] == 'article' && !empty($query['id'])) {
            $db = App::get('db');
            $db->setQuery("SELECT `path` FROM `#__menu` "
                . "WHERE link='index.php?option=com_content&view=article&id={$query['id']}' AND published=1");
            if ($menuitem = $db->loadResult()) {
                $segments = explode('/', $menuitem);
            } else {
                $q  = "SELECT cat.`path`, con.`alias` AS con_alias, cat.`alias` AS cat_alias FROM `#__content` AS con";
                $q .= " LEFT JOIN `#__categories` AS cat ON con.catid = cat.id";
                $q .= " WHERE con.state=1 AND con.`id` = '{$query['id']}'";
                $db->setQuery($q);
                if ($result = $db->loadObject()) {
                    if ($result->cat_alias == 'uncategorised') {
                        $segments[] = $result->con_alias;
                    } else {
                        $segments   = explode('/', $result->path);
                        $segments[] = $result->con_alias;
                    }
                }
            }
        }

        unset($query['task']);
        unset($query['view']);
        unset($query['id']);

        if (empty($segments)) {
            return $uri;
        }

        $result = implode('/', $segments);
        $result = str_replace(':', '-', $result);

        $route .= ($result != '') ? '/' . $result : '';

        // Unset unneeded query information
        unset($query['Itemid']);
        unset($query['option']);

        // Set query again in the URI
        $uri->setQuery($query);
        $uri->setPath($route);
    }

    return $uri;
});

/*
| Component
|
| Build the route by component name
*/
/*
| Which menu item speaks for a component
|
| The parse side settles two items claiming one address by the kind of menu
| each is in. This is the same question asked backwards - given a component and
| no Itemid, which of its menu items should a link point at - and it has to be
| settled the same way or a hub's own page for a component would answer at its
| address while every link built for that component went somewhere else.
|
| Which is what happened. There was no lookup at all: without an Itemid the
| builder used the component's name, so a hub whose Resources page sat at
| /library still emitted /resources/browse from inside /library, losing the
| Itemid and with it that page's modules and template style.
|
| Only an item whose link is the component and nothing else counts. One
| pointing at a particular view is a page about something narrower, and a link
| that asked for the component should not land there.
*/
$speaksFor = function ($option) {
    static $best = array();

    if (array_key_exists($option, $best)) {
        return $best[$option];
    }

    $best[$option] = null;

    if (!App::has('menu.manager')) {
        return null;
    }

    $rank = function ($item) {
        $kind = isset($item->menuKind) && $item->menuKind ? $item->menuKind : 'display';

        switch ($kind) {
            case 'component':
                return 2;
            case 'routing':
                return 1;
            default:
                return 0;
        }
    };

    $found     = null;
    $foundRank = null;
    $foundBare = false;

    foreach (App::get('menu.manager')->menu('site')->getMenu() as $item) {
        if (!is_object($item) || $item->component != $option || $item->type == 'alias') {
            continue;
        }

        $itemRank = $rank($item);

        // The component and nothing else - or the generated entry, whatever it
        // says. A display item naming a view is a page about something
        // narrower, and a link that asked for the component should not land
        // there; but a component menu's entry is that component's page by
        // definition, and on a hub carried over from the old default menu it
        // often names the component's own landing view.
        $bare = (is_array($item->query) && count($item->query) == 1);

        if (!$bare && $itemRank != 2) {
            continue;
        }

        // Between two of the same rank the plain one wins, so a hub that has
        // both keeps the one that says only the component.
        if (!$found || $itemRank < $foundRank || ($itemRank == $foundRank && $bare && !$foundBare)) {
            $found     = $item;
            $foundRank = $itemRank;
            $foundBare = $bare;
        }
    }

    $best[$option] = $found;

    return $found;
};

$router->rules('build')->append('component', function ($uri) use ($speaksFor) {
    $route = $uri->getPath();
    $query = $uri->getQuery(true);
    $tmp   = '';

    if (!isset($query['option']) && !isset($query['Itemid'])) {
        return $uri;
    }

    if (!isset($query['option'])) {
        $query['option'] = 'com_content';
    }

    $query['option'] = App::get('component')->canonical($query['option']);

    if ($router = App::get('component')->router($query['option'], 'site')) {
        $query = $router->preprocess($query);
        $parts = $router->build($query);
        $parts = array_filter($parts, function ($v) {
            return !is_array($v);
        });
        $tmp   = implode('/', $parts);
    }

    $built = false;

    if (isset($query['Itemid']) && !empty($query['Itemid']) && App::has('menu.manager')) {
        $menu = App::get('menu.manager')->menu('site');
        $item = $menu->getItem($query['Itemid']);
        if (is_object($item) && $query['option'] == $item->component) {
            if (!$item->home || $item->language != '*') {
                $tmp = !empty($tmp) ? $item->route . '/' . $tmp : $item->route;
            }

            $built = true;
        }
    }

    if (!$built && isset($query['option'])) {
        // No Itemid was asked for, so find the item that speaks for this
        // component rather than assuming its address is its name.
        if ($item = $speaksFor($query['option'])) {
            if (!$item->home || $item->language != '*') {
                $tmp = !empty($tmp) ? $item->route . '/' . $tmp : $item->route;
            }

            $built = true;
        }
    }

    if (!$built) {
        $tmp = isset($query['option']) ? substr($query['option'], 4) . '/' . $tmp : $tmp;
    }

    $route .= $tmp ? '/' . $tmp : '';

    if (isset($item) && $query['option'] == $item->component) {
        unset($query['Itemid']);
    }
    unset($query['option']);

    //Set query again in the URI
    $uri->setQuery($query);
    $uri->setPath($route);

    return $uri;
});

/*
| SEF Rewrite
|
| Remove the base URI path. This will strip everything up to the base
*/
$router->rules('build')->append('rewrite', function ($uri) {
    // Get the path data
    $route = $uri->getPath();

    if (App::get('config')->get('sef_suffix') && !(substr($route, -9) == 'index.php' || substr($route, -1) == '/')) {
        if ($format = $uri->getUriVar('format', 'html')) {
            $route .= '.' . $format;

            $uri->delUriVar('format');
        }
    }

    if (App::get('config')->get('sef_rewrite')) {
        if ($route == 'index.php') {
            $route = '';
        } else {
            $route = str_replace('index.php/', '', $route);
        }
    }

    // Add basepath to the uri
    $base = App::get('request')->base(true);
    if (!App::isSite()) {
        $base = '/' . ltrim(substr(ltrim($base, '/'), strlen(App::get('client')->name)), '/');
    }
    $uri->setPath($base . '/' . $route);

    return $uri;
});

/*
| SEF Groups
|
| Remove the base URI path. This will strip everything up to the bas
*/
$router->rules('build')->append('groups', function ($uri) {
    if (!empty($_SERVER['REWROTE_FROM'])) {
        if (stripos($uri->toString(), $_SERVER['REWROTE_TO']->getPath()) !== false) {
            $uri->setPath(str_replace($_SERVER['REWROTE_TO']->getPath(), '', $uri->getPath()));
            $uri->setHost($_SERVER['REWROTE_FROM']->getHost());
            $uri->setScheme($_SERVER['REWROTE_FROM']->getScheme());
        }
    }

    return $uri;
});

/*
| Start
|
| Turn limitstart into start
*/
$router->rules('build')->append('limit', function ($uri) {
    if ($uri->hasUriVar('limitstart')) {
        $uri->setUriVar('start', (int) $uri->getUriVar('limitstart'));
        $uri->delUriVar('limitstart');
    }

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
    // Get the path
    $path = $uri->getPath();

    // Remove the base URI path.
    $path = substr_replace($path == null ? '' : $path, '', 0, strlen(App::get('request')->base(true)));

    // Remove prefix
    $path = str_replace('index.php', '', $path);

    // Set the route
    $uri->setPath(trim($path, '/'));
});

/*
| Start
|
| Turn start into limitstart
*/
$router->rules('parse')->append('limit', function ($uri) {
    $limitstart = $uri->getUriVar('start');
    if (!is_null($limitstart)) {
        $uri->setUriVar('limitstart', $limitstart);
        $uri->delUriVar('start');
        App::get('router')->forget('start');
    }
    // Make sure the values are sane
    if (
        intval($uri->getUriVar('limitstart')) > 9223372036854775807
        || intval($uri->getUriVar('limit')) > 9223372036854775807
    ) {
        App::abort(404, Lang::txt('Pagination value beyond the bounds of supported integer values.'));
    }
    if (intval($uri->getUriVar('limitstart')) < 0 || intval($uri->getUriVar('limit')) < 0) {
        App::abort(404, Lang::txt('Invalid pagination value.'));
    }
});

/*
| Match by menu
|
| Match the first segment of the URI by component name. If a match is
| found, the component's router will be loaded to continue parsing any
| further segments.
*/
$router->rules('parse')->append('menu', function ($uri) use ($speaksFor) {
    $menu  = App::get('menu');
    $route = $uri->getPath();

    // Remove the suffix
    if (App::get('config')->get('sef_suffix')) {
        if ($suffix = pathinfo($route, PATHINFO_EXTENSION)) {
            $route = str_replace('.' . $suffix, '', $route);
        }
    }

    // Get the variables from the uri
    $query = $uri->getQuery(true);

    // Handle an empty URL (special case)
    if (empty($route) && Request::getCmd('option', '', 'post') == '') {
        // If route is empty AND option is set in the query, assume it's non-sef url, and parse appropriately
        if (isset($query['option'])) {
            // A non-sef url used to get no active menu item at all - not even
            // when it named one. index.php?option=com_x&Itemid=12 carried the
            // Itemid as a query var and then never activated it, so the page
            // had no template style, no per-page modules and no place in a
            // breadcrumb, while /x - the same page - had all three.
            //
            // So honour the Itemid when one is given, and otherwise ask the
            // same question the builder asks: which menu item speaks for this
            // component. That is only ever an item whose link is the component
            // and nothing else, so a url naming a particular view still gets
            // nothing rather than being handed a page about something else.
            if (!empty($query['Itemid']) && $menu->getItem($query['Itemid'])) {
                $menu->setActive($query['Itemid']);
            } elseif ($item = $speaksFor($query['option'])) {
                $uri->setUriVar('Itemid', $item->id);
                $menu->setActive($item->id);
            }

            return true;
        }

        $item = $menu->getDefault(App::get('language')->getTag());

        // if user not allowed to see default menu item then avoid notices
        if (is_object($item)) {
            // Set the information in the request
            $vars = $item->query;

            // Get the itemid
            $vars['Itemid'] = $item->id;

            // Set the active menu item
            $menu->setActive($vars['Itemid']);

            foreach ($vars as $key => $var) {
                $uri->setUriVar($key, $var);
            }
        }

        return true;
    }

    // Need to reverse the array (highest sublevels first)
    $items = array_reverse($menu->getMenu());

    $found           = false;
    $foundRank       = null;
    $route_lowercase = strtolower($route);
    $lang_tag        = App::get('language')->getTag();

    // When two items answer at the same address, which one wins used to be
    // whichever sat earlier in the menu tree - and lft is reassigned wholesale
    // by any rebuild, so installing a component could hand the address to the
    // other one without anybody touching either.
    //
    // So: a menu the hub made and shows beats one it made and does not show,
    // and both beat the entry generated for the component. The generated entry
    // is a floor, not a claim - it is there so that every component has some
    // address, and it steps aside the moment the hub says otherwise. Take the
    // hub's item away again and the generated one answers once more.
    $rank = function ($item) {
        $kind = isset($item->menuKind) && $item->menuKind ? $item->menuKind : 'display';

        switch ($kind) {
            case 'component':
                return 2;
            case 'routing':
                return 1;
            default:
                return 0;
        }
    };

    foreach ($items as $item) {
        //sqlsrv  change
        if (isset($item->language)) {
            $item->language = trim($item->language);
        }

        // Keep searching for better matches with higher depth
        $depth  = substr_count(trim($item->route, '/'), '/') + 1;

        // Get the length of the route
        $length = strlen($item->route);

        if (
            $length > 0 && strpos($route_lowercase . '/', $item->route . '/') === 0
            && $item->type != 'alias'
            && (!App::get('language.filter') || $item->language == '*' || $item->language == $lang_tag)
        ) {
            // Handle external url menu items differently
            if ($item->type == 'url') {
                // If menu route exactly matches url route, redirect (if necessary) to menu link
                if (trim($item->route, '/') == trim($route, '/')) {
                    if (
                        trim($item->route, '/') != trim($item->link, '/')
                        && trim(
                            App::get('request')->base(true) . '/' . $item->route,
                            '/'
                        ) != trim($item->link, '/')
                        // Added because it would cause redirect loop for installs not in top-level webroot
                        && trim(
                            App::get('request')->base(true) . '/index.php/' . $item->route,
                            '/'
                        ) != trim($item->link, '/')
                    ) { // Added because it would cause redirect loop for installs not in top-level webroot
                        App::redirect($item->link);
                    }
                }

                // Pass local URLs through, but record Itemid (we want the content parser to handle this)
                if (strpos($item->route, '://') === false) {
                    $vars['Itemid'] = $item->id;
                    break;
                }
            }

            // We have exact item for this language
            if ($item->language == $lang_tag) {
                $found      = $item;
                // Track depth so we can replace with a better match later
                $foundDepth = $depth;
                break;
            }

            // Or let's remember an item for all languages. A longer address is
            // always the better match; between two of the same length it is
            // the kind of menu that decides, and only if those tie does the
            // old behaviour - later in the walk, so earlier in the tree - get
            // to choose, so nothing that was unambiguous before moves.
            $itemRank = $rank($item);

            if (!$found) {
                $better = true;
            } elseif ($depth != $foundDepth) {
                $better = ($depth > $foundDepth);
            } elseif ($itemRank != $foundRank) {
                $better = ($itemRank < $foundRank);
            } else {
                $better = true;
            }

            if ($better) {
                $found      = $item;
                $foundDepth = $depth;
                $foundRank  = $itemRank;
            }
        }
    }

    // No menu item found.
    // Carry on...
    if (!$found) {
        return;
    }

    $route = substr($route, strlen($found->route));
    if ($route) {
        $route = substr($route, 1);
    }

    $uri->setUriVar('Itemid', $found->id);
    $uri->setUriVar('option', $found->component);
    $uri->setPath($route);
    foreach ($found->query as $key => $val) {
        $uri->setUriVar($key, $val);
    }

    $menu->setActive($uri->getUriVar('Itemid'));

    // No more segments.
    // No more processing needed.
    if (!$route) {
        return $uri;
    }
});

/*
| Match by content
|
| Match the content by article and category aliases.
*/
$router->rules('parse')->append('content', function ($uri) {
    if ($uri->getUriVar('option') && $uri->getUriVar('option') != 'com_content') {
        return;
    }

    $vars  = array();

    //$view  = 'article';
    $menu  = App::get('menu.manager')->menu('site');
    $item  = $menu->getActive();
    $db    = App::get('db');
    $segments = explode('/', $uri->getPath());
    $count = count($segments);

    // Item is numeric, assume user knows the article ID, and is trying to access directly
    if ($count == 1 && is_numeric($segments[0])) {
        $vars['option'] = 'com_content';
        $vars['id']     = $segments[0];
        $vars['view']   = 'article';

        if (isset($item)) {
            $item->query['view'] = 'article';
        }
    } elseif ($count == 1) {
        // Count 1 - we're either looking for an article alias that matches and is in the uncategorised category,
        // or,
        // and category series that are all the same (ex: about/about/about - supported for legacy reasons)
        // First, do query
        $query  = "SELECT con.`id`, cat.`alias`, cat.`path` FROM `#__content` AS con";
        $query .= " LEFT JOIN `#__categories` AS cat ON con.catid = cat.id";
        $query .= " WHERE con.state=1 AND con.`alias` = " . $db->quote(strtolower($segments[0]));
        $db->setQuery($query);
        $result = $db->loadObject();

        if (empty($result)) {
            return;
        }

        // Now, check for uncategorised article with provided alias
        if ($result->alias == 'uncategorised') {
            // Success, that's it
            $segments = array();

            $vars['option'] = 'com_content';
            $vars['id']     = $result->id;
            $vars['view']   = 'article';

            if (isset($item)) {
                $item->query['view'] = 'article';
            }
        } else {
            // It wasn't uncategorised, so now try and see if its in a category scheme of all the same aliases
            $path  = explode('/', $result->path);
            $found = true;

            foreach ($path as $p) {
                if ($p != $segments[0]) {
                    $found = false;
                    continue;
                }
            }

            if ($found) {
                // Success, that's it
                $segments = array();

                $vars['option'] = 'com_content';
                $vars['id']     = $result->id;
                $vars['view']   = 'article';

                if (isset($item)) {
                    $item->query['view'] = 'article';
                }
            }
        }
    } elseif ($count > 1) {
        // Build the path
        $path = array();
        for ($i = 0; $i < ($count - 1); $i++) {
            $path[] = $segments[$i];
        }

        $path = implode('/', $path);

        // Now, do query (path is all but last segment, and last segment is article alias)
        $query  = "SELECT con.`id` FROM `#__content` AS con";
        $query .= " LEFT JOIN `#__categories` AS cat ON con.catid = cat.id";
        $query .= " WHERE con.state=1 AND con.`alias` = " . $db->quote(strtolower($segments[$count - 1]));
        $query .= " AND cat.`path` = " . $db->quote(strtolower($path));
        $db->setQuery($query);

        if ($result = $db->loadResult()) {
            // Success, that's it
            $segments = array();

            $vars['option'] = 'com_content';
            $vars['id']     = $result;
            $vars['view']   = 'article';

            if (isset($item)) {
                $item->query['view'] = 'article';
            }
        }
    }

    if (!empty($vars)) {
        foreach ($vars as $key => $var) {
            $uri->setUriVar($key, $var);
        }

        return true;
    }
});

/*
| Match by component
|
| Match the first segment of the URI by component name. If a match is
| found, the component's router will be loaded to continue parsing any
| further segments.
*/
$router->rules('parse')->append('component', function ($uri) {
    $component = $uri->getUriVar('option');
    if (is_array($component)) {
        $component = implode('', $component);
    }
    $segments  = explode('/', $uri->getPath());

    if (!$component) {
        if (count($segments) > 1 && $segments[0] == 'component') {
            $prefix = array_shift($segments);
        }
        $component = array_shift($segments);
    }

    if (!$component) {
        // No component name found.
        // Nothing else we can do here.
        return;
    }

    // First segment is potentially a component name.
    $uri->setUriVar('option', App::get('component')->canonical($component));

    if (!count($segments)) {
        // No other segments found so no need
        // to attempt to load component router
        return;
    }

    if ($router = App::get('component')->router($component, 'site')) {
        if ($vars = $router->parse($segments)) {
            foreach ($vars as $key => $var) {
                $uri->setUriVar($key, $var);
            }
        }

        return true;
    }
});

/*
| Lazily give a new component its address - in development only
|
| A component's address is made when the component is installed. A component
| that has just been written has no migration yet, so it has no address, so it
| has no Itemid - and without an Itemid it gets no per-page modules, no template
| style of its own and no place in a breadcrumb. The component appears to work
| and then behaves oddly, and nothing says why.
|
| This closes that gap while the migration is still being written. It runs only
| with debug on, because a production hub creating menu rows in response to
| whatever URL it is asked for is a different thing entirely, and because the
| right place to make a route is a migration that ships with the component.
|
| The entry lands after this request has already read the menu, so it is the
| next request that gets the Itemid. That is one refresh, and it is the honest
| behaviour: the alternative is re-reading the menu mid-request to hide it.
*/
if (Config::get('debug')) {
    $router->rules('parse')->append('lazyroute', function ($uri) {
        $option = $uri->getUriVar('option');

        if (is_array($option) || !$option) {
            return;
        }

        // Only the component's own front door. A deeper URL under it is the
        // component router's business and creates nothing.
        $segments = array_filter(explode('/', trim((string) $uri->getPath(), '/')));

        if (count($segments) != 1 || 'com_' . $segments[0] != $option) {
            return;
        }

        if (!App::has('db')) {
            return;
        }

        $db     = App::get('db');
        $routes = new \Hubzero\Menu\ComponentRoute($db);

        if (!$routes->routable($option) || $routes->exists($option)) {
            return;
        }

        // Installed and switched on, or there is nothing to route to
        $extension = $routes->extension($option);

        if (!$extension) {
            return;
        }

        $id      = (int) (is_object($extension) ? $extension->extension_id : $extension['extension_id']);
        $enabled = (int) (is_object($extension) ? $extension->enabled : $extension['enabled']);

        if (!$enabled) {
            return;
        }

        // Two requests arriving together would both find nothing and both
        // insert, and the menu's unique key on client, parent, alias and
        // language would refuse the second. The check and the insert go in one
        // transaction, and a deadlock is worth one retry.
        try {
            $db->transaction(function () use ($routes, $option, $id, $enabled) {
                if (!$routes->exists($option)) {
                    $routes->create($option, $id, $enabled);
                }
            }, 2);
        } catch (\Throwable $e) {
            // A hub that cannot write its own menu has a larger problem than a
            // missing Itemid, and this is not the request to report it on.
            return;
        }

        Log::debug(sprintf(
            'Lazily gave %s the address /%s. Write a migration: see AddComponentEntry.',
            $option,
            $segments[0]
        ));
    });
}

/*
| Match by redirection rule
|
| Match the first segment of the URI by component name. If a match is
| found, the component's router will be loaded to continue parsing any
| further segments.
*/
$router->rules('parse')->append('redirect', function ($uri) {
    // Use an alturi in case any previous rule altered
    // the $uri by adding/removing vars
    $alturi = new \Hubzero\Utility\Uri($uri->uri());

    $menu  = App::get('menu');

    $db = App::get('db');
    $db->setQuery(
        "SELECT *
		FROM `#__redirect_links`
		WHERE `published`=1
		AND (`old_url`=" . $db->quote($alturi->toString(array('scheme', 'host', 'port', 'path', 'query', 'fragment')))
        . "
		OR `old_url`=" . $db->quote($alturi->toString(array('path', 'query', 'fragment'))) . ")
		LIMIT 1"
    );

    if ($row = $db->loadObject()) {
        $myuri = new \Hubzero\Utility\Uri($row->new_url);

        $vars = $myuri->getQuery(true);

        foreach ($vars as $key => $var) {
            $uri->setUriVar($key, $var);
        }

        if (isset($vars['Itemid'])) {
            $menu->setActive($vars['Itemid']);
        }

        return true;
    }
});

/*
| Match by posted data
|
| Look for the option var in POST data
*/
$router->rules('parse')->append('post', function ($uri) {
    if (App::get('request')->method() == 'POST') {
        $component = App::get('request')->getCmd('option', '', 'post');
        $uri->setUriVar('option', $component);

        return true;
    }
});
