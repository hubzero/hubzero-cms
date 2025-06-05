<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die;

/**
 * Antispam Content Plugin
 */
class plgContentAntispam extends \Hubzero\Plugin\Plugin
{
	/**
	 * Before save content method
	 *
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param   string   $context  The context of the content passed to the plugin (added in 1.6)
	 * @param   object   $article  Model
	 * @param   boolean  $isNew    If the content is just about to be created
	 * @return  void
	 * @since   2.5
	 */
	public function onContentBeforeSave($context, $article, $isNew)
	{
		// Only run for site
		if (!App::isSite())
		{
			return;
		}

		// Don't bother with super admins
		if (User::authorise('core.admin'))
		{
			return;
		}

		// Check if administrators are whitelisted
		if ($this->params->get('wl_groups') == 'admin' && User::authorise('core.manage'))
		{
			return;
		}
		// Check the users whitelist
		$wl_usernames = ($this->params->get('wl_usernames'));

		if ($wl_usernames)
		{
			$usernames = array_map('trim', explode(',', $wl_usernames));

			if (in_array(User::get('username'), $usernames))
			{
				// user whitelisted, do not check for spam
				return;
			}
		}

		if ($article instanceof \Hubzero\Base\Obj || $article instanceof \Hubzero\Database\Relational)
		{
			$key = $this->_key($context);

			$content = ltrim($article->get($key) == null ? '' : $article->get($key));
		}
		else if (is_object($article) || is_array($article))
		{
			return;
		}
		else
		{
			$content = $article;
		}

		$content = preg_replace('/^<!-- \{FORMAT:.*\} -->/i', '', $content);
		$content = trim($content);

		if (!$content)
		{
			return;
		}

		// Get the detector manager
		$service = new \Hubzero\Spam\Checker();

		foreach (Event::trigger('antispam.onAntispamDetector') as $detector)
		{
			if (!$detector)
			{
				continue;
			}

			$service->registerDetector($detector);
		}

		// Check content
		$data = array(
			'name'       => User::get('name'),
			'email'      => User::get('email'),
			'username'   => User::get('username'),
			'id'         => User::get('id'),
			'ip'         => Request::ip(),
			'user_agent' => Request::getString('HTTP_USER_AGENT', null, 'server'),
			'text'       => $content
		);

		$result = $service->check($data);

		// Log errors any of the service providers may have thrown
		if ($service->getError() && App::has('log'))
		{
			App::get('log')
				->logger('debug')
				->info(implode(' ', $service->getErrors()));
		}

		// If the content was detected as spam...
		if ($result->isSpam())
		{
			// Learn from it?
			if ($this->params->get('learn_spam', 1))
			{
				Event::trigger('antispam.onAntispamTrain', array(
					$content,
					true
				));
			}

			// If a message was set...
			if ($message = $this->params->get('message'))
			{
				Notify::error($message);
			}

			// Increment spam hits count...go to spam jail!
			\Hubzero\User\User::oneOrFail(User::get('id'))->reputation->incrementSpamCount();

			if ($this->params->get('log_spam'))
			{
				$this->log($result->isSpam(), $data);
			}

			return false;
		}

		// Content was not spam.
		// Learn from it?
		if ($this->params->get('learn_ham', 0))
		{
			Event::trigger('antispam.onAntispamTrain', array(
				$content,
				false
			));
		}
	}

	/**
	 * Check if the context provided the content field name as
	 * it may vary between models.
	 *
	 * @param   string  $context  A dot-notation string
	 * @return  string
	 */
	private function _key($context)
	{
		$parts = explode('.', $context);
		$key = 'content';
		if (isset($parts[2]))
		{
			$key = $parts[2];
		}
		return $key;
	}

	/**
	 * Log results of the check
	 *
	 * @param   string  $isSpam  Spam detection result
	 * @param   array   $data    Data being checked
	 * @return  void
	 */
	private function log($isSpam, $data)
	{
		if (!App::has('log'))
		{
			return;
		}

		$fallback  = 'option=' . Request::getCmd('option');
		$fallback .= '&controller=' . Request::getCmd('controller');
		$fallback .= '&task=' . Request::getCmd('task');

		$from = Request::getVar('REQUEST_URI', $fallback, 'server');
		$from = $from ?: $fallback;

		$info = array(
			($isSpam ? 'spam' : 'ham'),
			$data['ip'],
			$data['id'],
			$data['username'],
			md5($data['text']),
			$from
		);

		App::get('log')
			->logger('spam')
			->info(implode(' ', $info));
	}
}
