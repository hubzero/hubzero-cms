<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Bootstrap\Site\Providers;

use Hubzero\Mail\Message;
use Hubzero\Base\ServiceProvider;

/**
 * Mail service provider
 */
class MailerServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['mailer'] = $this->app->factory(function($app)
		{
			return new Message();
		});
	}

	/**
	 * Add the transporters to the message
	 *
	 * @return  void
	 */
	public function boot()
	{
		if ($this->app->has('dispatcher'))
		{
			$this->app['dispatcher']->trigger('mail.onMailersRegister');
		}
	}
}
