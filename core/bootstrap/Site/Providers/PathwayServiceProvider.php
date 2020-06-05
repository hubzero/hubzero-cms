<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Site\Providers;

use Hubzero\Pathway\Trail;
use Hubzero\Base\ServiceProvider;

/**
 * Breadcrumbs service provider
 */
class PathwayServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['pathway'] = function($app)
		{
			$trail = new Trail();

			if ($app->has('menu'))
			{
				$menu = $app['menu'];

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
										// If this is an internal link, ensure the Itemid is set.
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
									/*$router = App::get('router');
									if ($router->getMode() == $router::MODE_SEF)
									{*/
										$url = 'index.php?Itemid=' . $link->id;
									/*}
									else {
										$url .= $link->link . '&Itemid=' . $link->id;
									}*/
									break;
							}

							$trail->append($menus[$menupath]->title, $url);
						}
					}
				}
			}

			return $trail;
		};
	}
}
