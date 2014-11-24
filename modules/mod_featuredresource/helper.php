<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Module class for displaying a random featured resource
 */
class modFeaturedresource extends \Hubzero\Module\Module
{
	/**
	 * Container for properties
	 *
	 * @var array
	 */
	public $id = 0;

	/**
	 * Generate module contents
	 *
	 * @return     void
	 */
	public function run()
	{
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');

		$database = JFactory::getDBO();

		//Get the admin configured settings
		$filters = array(
			'limit'      => 1,
			'start'      => 0,
			'type'       => trim($this->params->get('type')),
			'sortby'     => 'random',
			'minranking' => trim($this->params->get('minranking')),
			'tag'        => trim($this->params->get('tag')),
			'access'     => 'public',
			// Only published tools
			'toolState'  => 7
		);

		$row = null;

		// No - so we need to randomly choose one
		// Initiate a resource object
		$rr = new ResourcesResource($database);

		// Get records
		$rows = $rr->getRecords($filters, false);
		if (count($rows) > 0)
		{
			$row = $rows[0];
		}

		// Did we get any results?
		if ($row)
		{
			$this->cls = trim($this->params->get('moduleclass_sfx'));
			$this->txt_length = trim($this->params->get('txt_length'));

			$config = JComponentHelper::getParams('com_resources');

			// Resource
			$id = $row->id;

			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'html.php');

			$path = DS . trim($config->get('uploadpath', '/site/resources'), DS);
			$path = ResourcesHtml::build_path($row->created, $row->id, $path);

			if ($row->type == 7)
			{
				include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'version.php');

				$tv = new ToolVersion($database);

				$versionid = $tv->getVersionIdFromResource($id, 'current');

				$picture = $this->getToolImage($path, $versionid);
			}
			else
			{
				$picture = $this->getImage($path);
			}

			$thumb = $path . DS . $picture;

			if (!is_file(JPATH_ROOT . $thumb))
			{
				$thumb = DS . trim($config->get('defaultpic'));
			}

			$row->typetitle = trim(stripslashes($row->typetitle));
			if (substr($row->typetitle, -1, 1) == 's' && substr($row->typetitle, -3, 3) != 'ies')
			{
				$row->typetitle = substr($row->typetitle, 0, strlen($row->typetitle) - 1);
			}

			$this->id    = $id;
			$this->thumb = $thumb;
			$this->row   = $row;

			require(JModuleHelper::getLayoutPath($this->module->module));
		}
	}

	/**
	 * Display module contents
	 *
	 * @return     void
	 */
	public function display()
	{
		$juser = JFactory::getUser();

		if (!$juser->get('guest') && intval($this->params->get('cache', 0)))
		{
			$cache = JFactory::getCache('callback');
			$cache->setCaching(1);
			$cache->setLifeTime(intval($this->params->get('cache_time', 900)));
			$cache->call(array($this, 'run'));
			echo '<!-- cached ' . JFactory::getDate() . ' -->';
			return;
		}

		$this->run();
	}

	/**
	 * Get a resource image
	 *
	 * @param      string $path Path to get resource image from
	 * @return     string
	 */
	private function getImage($path)
	{
		$d = @dir(JPATH_ROOT . $path);

		$images = array();

		if ($d)
		{
			while (false !== ($entry = $d->read()))
			{
				$img_file = $entry;
				if (is_file(JPATH_ROOT . $path . DS . $img_file)
				 && substr($entry, 0, 1) != '.'
				 && strtolower($entry) !== 'index.html')
				{
					if (preg_match("#bmp|gif|jpg|png#i", $img_file))
					{
						$images[] = $img_file;
					}
				}
			}

			$d->close();
		}

		$b = 0;
		if ($images)
		{
			foreach ($images as $ima)
			{
				$bits = explode('.', $ima);
				$type = array_pop($bits);
				$img  = implode('.', $bits);

				if ($img == 'thumb')
				{
					return $ima;
				}
			}
		}
	}

	/**
	 * Get a screenshot of a tool
	 *
	 * @param      string  $path      Path to look for screenshots in
	 * @param      integer $versionid Tool version
	 * @return     string
	 */
	private function getToolImage($path, $versionid=0)
	{
		// Get contribtool parameters
		$tconfig = JComponentHelper::getParams('com_tools');
		$allowversions = $tconfig->get('screenshot_edit');

		if ($versionid && $allowversions)
		{
			// Add version directory
			//$path .= DS.$versionid;
		}

		return $this->getImage($path);
	}
}

