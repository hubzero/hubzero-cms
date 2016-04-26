<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
