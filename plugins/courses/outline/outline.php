<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Courses Plugin class for the outline
 */
class plgCoursesOutline extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return data on a course view (this will be some form of HTML)
	 *
	 * @param   object   $course    Current course
	 * @param   object   $offering  Name of the component
	 * @param   boolean  $describe  Return plugin description only?
	 * @return  object
	 */
	public function onCourse($course, $offering, $describe=false)
	{
		$response = with(new \Hubzero\Base\Object)
			->set('name', $this->_name)
			->set('title', JText::_('PLG_COURSES_' . strtoupper($this->_name)))
			->set('default_access', $this->params->get('plugin_access', 'members'))
			->set('display_menu_tab', true)
			->set('icon', 'f0ae');

		if ($describe)
		{
			return $response;
		}

		if (!($active = JRequest::getVar('active')))
		{
			JRequest::setVar('active', ($active = $this->_name));
		}

		// Check to see if user is member and plugin access requires members
		$sparams = new JRegistry($course->offering()->section()->get('params'));
		if (!$course->offering()->section()->access('view') && !$sparams->get('preview', 0))
		{
			$response->set('html', '<p class="info">' . JText::sprintf('COURSES_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>');
			return $response;
		}

		// Determine if we need to return any HTML (meaning this is the active plugin)
		if ($response->get('name') == $active)
		{
			$this->css();

			// Course and action
			$this->course = $course;
			$action = strtolower(JRequest::getWord('action', ''));

			$this->view = $this->view('default', 'outline');
			$this->view->option     = JRequest::getCmd('option', 'com_courses');
			$this->view->controller = JRequest::getWord('controller', 'course');
			$this->view->course     = $course;
			$this->view->offering   = $offering;
			$this->view->config     = $course->config();
			$this->view->juser      = JFactory::getUser();

			switch ($action)
			{
				case 'build':
					$this->_build();
				break;

				default:
					\Hubzero\Document\Assets::addPluginScript('courses', $this->_name);
					//\Hubzero\Document\Assets::addSystemScript('jquery.masonry');
					$this->_display();
				break;
			}

			$response->set('html', $this->view->loadTemplate());
		}

		// Return the output
		return $response;
	}

	/**
	 * Set the layout to the default outline view
	 *
	 * @return  void
	 */
	private function _display()
	{
		if (($unit = JRequest::getVar('unit', '')))
		{
			$this->view->setLayout('unit');
		}
		if (($group = JRequest::getVar('group', '')))
		{
			$this->view->setLayout('lecture');
		}

		if (isset($unit))
		{
			$this->view->unit = $unit;
		}
		if (isset($group))
		{
			$this->view->group = $group;
		}
	}

	/**
	 * Show the builder interface
	 *
	 * @return  string
	 */
	private function _build()
	{
		if (!$this->course->access('manage'))
		{
			JError::raiseError(401, JText::_('Not Authorized'));
			return;
		}

		// If we have a scope set, we're loading a specific outline piece (ex: a unit)
		if ($scope = JRequest::getWord('scope', false))
		{
			// Setup view
			$this->view->setLayout("edit{$scope}");

			\Hubzero\Document\Assets::addPluginStylesheet('courses', $this->_name, 'build.css');
			\Hubzero\Document\Assets::addPluginStylesheet('courses', $this->_name, $scope.'.css');
			\Hubzero\Document\Assets::addPluginScript('courses', $this->_name, $scope);

			// Add file uploader JS
			\Hubzero\Document\Assets::addSystemScript('jquery.iframe-transport');
			\Hubzero\Document\Assets::addSystemScript('jquery.fileupload');

			$this->view->title         = "Edit {$scope}";
			$this->view->scope         = $scope;
			$this->view->scope_id      = JRequest::getInt('scope_id');

			return;
		}

		\Hubzero\Document\Assets::addSystemStylesheet('jquery.ui.css');

		// Add outline builder style and script
		\Hubzero\Document\Assets::addPluginStylesheet('courses', $this->_name, 'build.css');
		\Hubzero\Document\Assets::addPluginScript('courses', $this->_name, 'build');

		// Add Content box plugin
		\Hubzero\Document\Assets::addSystemScript('contentbox');
		\Hubzero\Document\Assets::addSystemStylesheet('contentbox.css');

		// Add underscore
		\Hubzero\Document\Assets::addSystemScript('underscore-min');
		\Hubzero\Document\Assets::addSystemScript('jquery.hoverIntent');

		// Add 'uniform' js and css
		\Hubzero\Document\Assets::addSystemStylesheet('uniform.css');
		\Hubzero\Document\Assets::addSystemScript('jquery.uniform');

		// Add file uploader JS
		\Hubzero\Document\Assets::addSystemScript('jquery.iframe-transport');
		\Hubzero\Document\Assets::addSystemScript('jquery.fileupload');

		// Use datetime picker, rather than just datepicker
		\Hubzero\Document\Assets::addSystemScript('jquery.timepicker');

		// Setup view
		$this->view->setLayout('build');

		$this->view->title = 'Build Outline';
	}

	/**
	 * Set redirect and message
	 *
	 * @param   string  $url   URL to redirect to
	 * @param   string  $msg   Message to send
	 * @param   string  $type  Message type (message, error, warning, info)
	 * @return  void
	 */
	public function setRedirect($url, $msg=null, $type='message')
	{
		if ($msg !== null)
		{
			$this->addPluginMessage($msg, $type);
		}
		$this->redirect($url);
	}
}
