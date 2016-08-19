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
		if (!App::isSite())
		{
			return;
		}

		if ($article instanceof \Hubzero\Base\Object || $article instanceof \Hubzero\Database\Relational)
		{
			$key = $this->_key($context);

			$content = ltrim($article->get($key));
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
			'user_agent' => Request::getVar('HTTP_USER_AGENT', null, 'server'),
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

		$request = App::get('request');

		$fallback  = 'option=' . $request->getCmd('option');
		$fallback .= '&controller=' . $request->getCmd('controller');
		$fallback .= '&task=' . $request->getCmd('task');

		$from = $request->getVar('REQUEST_URI', $fallback, 'server');
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
