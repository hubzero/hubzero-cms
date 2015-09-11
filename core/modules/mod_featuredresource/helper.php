<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 * All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Featuredresource;

use Hubzero\Module\Module;
use Component;
use User;

/**
 * Module class for displaying a random featured resource
 */
class Helper extends Module
{
	/**
	 * Container for properties
	 *
	 * @var  array
	 */
	public $id = 0;

	/**
	 * Generate module contents
	 *
	 * @return  void
	 */
	public function run()
	{
		include_once(Component::path('com_resources') . DS . 'tables' . DS . 'resource.php');

		$database = \App::get('db');

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
		$rr = new \Components\Resources\Tables\Resource($database);

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

			$config = Component::params('com_resources');

			// Resource
			$id = $row->id;

			include_once(Component::path('com_resources') . DS . 'helpers' . DS . 'html.php');

			$path = DS . trim($config->get('uploadpath', '/site/resources'), DS);
			$path = \Components\Resources\Helpers\Html::build_path($row->created, $row->id, $path);

			if ($row->type == 7)
			{
				include_once(Component::path('com_tools') . DS . 'tables' . DS . 'version.php');

				$tv = new \Components\Tools\Tables\Version($database);

				$versionid = $tv->getVersionIdFromResource($id, 'current');

				$picture = $this->getToolImage($path, $versionid);
			}
			else
			{
				$picture = $this->getImage($path);
			}

			$thumb = $path . DS . $picture;

			if (!is_file(PATH_APP . $thumb))
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

			require $this->getLayoutPath();
		}
	}

	/**
	 * Display module contents
	 *
	 * @return     void
	 */
	public function display()
	{
		if ($content = $this->getCacheContent())
		{
			echo $content;
			return;
		}

		$this->run();
	}

	/**
	 * Get a resource image
	 *
	 * @param   string  $path  Path to get resource image from
	 * @return  string
	 */
	private function getImage($path)
	{
		$d = @dir(PATH_APP . $path);

		$images = array();

		if ($d)
		{
			while (false !== ($entry = $d->read()))
			{
				$img_file = $entry;
				if (is_file(PATH_APP . $path . DS . $img_file)
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
	 * @param   string   $path       Path to look for screenshots in
	 * @param   integer  $versionid  Tool version
	 * @return  string
	 */
	private function getToolImage($path, $versionid=0)
	{
		// Get contribtool parameters
		$tconfig = Component::params('com_tools');
		$allowversions = $tconfig->get('screenshot_edit');

		if ($versionid && $allowversions)
		{
			// Add version directory
			//$path .= DS.$versionid;
		}

		return $this->getImage($path);
	}
}

