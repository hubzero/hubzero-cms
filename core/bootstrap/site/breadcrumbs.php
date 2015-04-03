<?php
$menu = \JApplication::getInstance('site')->getMenu();

if ($item = $menu->getActive())
{
	$menus = $menu->getMenu();
	$home  = $menu->getDefault();

	if (is_object($home) && ($item->id != $home->id))
	{
		foreach ($item->tree as $menupath)
		{
			$url = '';
			$link = $menu->getItem($menupath);

			switch ($link->type)
			{
				case 'separator':
					$url = null;
					break;

				case 'url':
					if ((strpos($link->link, 'index.php?') === 0) && (strpos($link->link, 'Itemid=') === false))
					{
						// If this is an internal Joomla link, ensure the Itemid is set.
						$url = $link->link . '&Itemid=' . $link->id;
					}
					else
					{
						$url = $link->link;
					}
					break;

				case 'alias':
					// If this is an alias use the item id stored in the parameters to make the link.
					$url = 'index.php?Itemid=' . $link->params->get('aliasoptions');
					break;

				default:
					$router = \JSite::getRouter();
					if ($router->getMode() == JROUTER_MODE_SEF)
					{
						$url = 'index.php?Itemid=' . $link->id;
					}
					else {
						$url .= $link->link . '&Itemid=' . $link->id;
					}
					break;
			}

			$trail->append($menus[$menupath]->title, $url);
		}
	}
}
