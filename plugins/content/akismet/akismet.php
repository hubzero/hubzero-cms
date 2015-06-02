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
 * Akismet antispam Content Plugin
 */
class plgContentAkismet extends JPlugin
{
	/**
	 * Finder before save content method
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param	string		The context of the content passed to the plugin (added in 1.6)
	 * @param	object		A JTableContent object
	 * @param	bool		If the content is just about to be created
	 * @since   2.5
	 */
	public function onContentBeforeSave($context, $article, $isNew)
	{
		if (JFactory::getApplication()->isAdmin()
		 || JFactory::getUser()->authorise('core.manage', JRequest::getCmd('option')))
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

		if (!$this->params->get('apiKey')) return;

		$ip       = JRequest::ip();
		$uid      = JFactory::getUser()->get('id');
		$username = JFactory::getUser()->get('username');
		$fallback = 'option=' . JRequest::getCmd('option') . '&controller=' . JRequest::getCmd('controller') . '&task=' . JRequest::getCmd('task');
		$from     = JRequest::getVar('REQUEST_URI', $fallback, 'server');
		$from     = $from ?: $fallback;
		$hash     = md5($content);

		$data = $this->onContentDetectSpam($content);

		if ($data['is_spam'])
		{
			JFactory::getSpamLogger()->info('spam ' . $this->_name . ' ' . $ip . ' ' . $uid . ' ' . $username . ' ' . $hash . ' ' . $from);
			if (!JFactory::getSession()->get('spam' . $hash))
			{
				$obj = new stdClass;
				$obj->failed = $content;
				JFactory::getSpamLogger()->info(json_encode($obj));
				JFactory::getSession()->set('spam' . $hash, 1);
			}
			return false;
		}

		JFactory::getSpamLogger()->info('ham ' . $this->_name . ' ' . $ip . ' ' . $uid . ' ' . $username . ' ' . $hash . ' ' . $from);
	}

	/**
	 * Check if the context provided the content field name as
	 * it may vary between models.
	 *
	 * @param   string $context A dot-notation string
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
	 * Event for checking content
	 *
	 * @param   string   $content  The context of the content passed to the plugin (added in 1.6)
	 * @return  array
	 */
	public function onContentDetectSpam($content)
	{
		$data = array(
			'service' => $this->_name,
			'is_spam' => false
		);

		if (!$this->params->get('apiKey')) return $data;

		include_once(__DIR__ . '/Service/Provider.php');

		$service = new \Hubzero\Antispam\Service(new \Plugins\Content\Akismet\Service\Provider);
		$service->set('apiKey', $this->params->get('apiKey'))
		        ->set('apiPort', $this->params->get('apiPort', 80))
		        ->set('akismetServer', $this->params->get('akismetServer', 'rest.akismet.com'))
		        ->set('akismetVersion', $this->params->get('akismetVersion', '1.1'));

		if ($service->isSpam($content))
		{
			$data['is_spam']  = true;
		}

		return $data;
	}
}
