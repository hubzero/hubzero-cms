<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Administrator\Providers;

use Hubzero\Base\ServiceProvider;
use Hubzero\Html\Editor;

/**
 * Editor service provider
 */
class EditorServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['editor'] = function($app)
		{
			$global = $app['config']->get('editor');

			$editor = \User::getParam('editor', $global);

			if (!$app['plugin']->isEnabled('editors', $editor))
			{
				$editor = $global;
				if (!$app['plugin']->isEnabled('editors', $editor))
				{
					$editor = 'none';
				}
			}

			$app['config']->set('editor', $editor);

			return new Editor($editor);
		};
	}
}
