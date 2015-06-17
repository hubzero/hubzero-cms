<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_JEXEC') or die;

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
	 * @param   object   $article  A JTableContent object
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

		if ($article instanceof \Hubzero\Base\Object)
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

		if (!$content) return;

		// Get the detector manager
		$service = new \Hubzero\Spam\Checker();

		foreach (Event::trigger('antispam.onAntispamDetector') as $detector)
		{
			if (!$detector) continue;

			$service->registerDetector($detector);
		}

		// Check content
		$data = array(
			'name'     => User::get('name'),
			'email'    => User::get('email'),
			'username' => User::get('username'),
			'id'       => User::get('id'),
			'text'     => $content
		);
		$result = $service->check($data);

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
}
