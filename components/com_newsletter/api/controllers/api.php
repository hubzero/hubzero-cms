<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

/**
 * API controller class for newsletters
 */
class NewsletterControllerApi extends \Hubzero\Component\ApiController
{
	/**
	 * Execute a request
	 *
	 * @return  void
	 */
	public function execute()
	{
		//needed joomla libraries
		//JLoader::import('joomla.environment.request');
		//JLoader::import('joomla.application.component.helper');

		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_newsletter' . DS . 'tables' . DS . 'newsletter.php');
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_newsletter' . DS . 'tables' . DS . 'template.php');
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_newsletter' . DS . 'tables' . DS . 'primary.php');
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_newsletter' . DS . 'tables' . DS . 'secondary.php');

		switch ($this->segments[0])
		{
			case 'current': $this->current(); break;
			case 'archive': $this->archive(); break;
			default:        $this->index();   break;
		}
	}

	/**
	 * Throw a 404 Not Found error
	 *
	 * @return  void
	 */
	private function not_found()
	{
		$response = $this->getResponse();
		$response->setErrorMessage(404,'Not Found');
	}

	/**
	 * Return data for newsletters
	 *
	 * @return  void
	 */
	private function index()
	{
		//get the userid
		$userid = JFactory::getApplication()->getAuthn('user_id');

		//if we dont have a user return nothing
		if ($userid == null)
		{
			return $this->not_found();
		}

		//get the request vars
		$limit = Request::getVar("limit", 5);

		//get newsletter object
		$database = JFactory::getDBO();
		$newsletterNewsletter = new \Components\Newsletter\Tables\Newsletter($database);

		//get newsletters
		$newsletters = $newsletterNewsletter->getNewsletters(null, true);

		//output
		$obj = new stdClass();
		$obj->newsletters = $newsletters;
		$this->setMessageType("application/json");
		$this->setMessage($obj);
	}

	/**
	 * Return data for the current newsletter
	 *
	 * @return  void
	 */
	private function current()
	{
		//return var
		$result = array();

		//get request vars
		$format = Request::getVar("format", "json");

		//instantiate newsletter campaign object
		$database = JFactory::getDBO();
		$newsletterNewsletter = new \Components\Newsletter\Tables\Newsletter($database);

		//get the current newsletter
		$newsletter = $newsletterNewsletter->getCurrentNewsletter();

		//build the newsletter based on campaign
		$newsletterHTML = $newsletterNewsletter->buildNewsletter($newsletter);
		$result['id'] = $newsletter->issue;
		$result['title'] = $newsletter->name;
		$result['content'] = $newsletterHTML;

		//encode sessions for return
		$obj = new stdClass();
		$obj->newsletter = $result;

		//set format and content
		$this->setMessageType($format);
		$this->setMessage($obj);
	}

	/**
	 * Return data for past newsletters
	 *
	 * @return  void
	 */
	private function archive()
	{
		//return var
		$result = array();

		//get request vars
		$format = Request::getVar("format", "json");

		//instantiate newsletter campaign object
		$database = JFactory::getDBO();
		$newsletterNewsletter = new \Components\Newsletter\Tables\Newsletter($database);

		//get newsletters
		$newsletters = $newsletterNewsletter->getNewsletters();

		//add newsletter details to return array
		foreach ($newsletters as $k => $newsletter)
		{
			$result[$k]['id'] = $newsletter->issue;
			$result[$k]['title'] = $newsletter->name;
			$result[$k]['content'] = $newsletterNewsletter->buildNewsletter($newsletter);
		}

		//encode sessions for return
		$obj = new stdClass();
		$obj->newsletters = $result;

		//set format and content
		$this->setMessageType($format);
		$this->setMessage($obj);
	}
}
