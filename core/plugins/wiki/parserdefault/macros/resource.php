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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Wiki macro class that will insert a linked title to a resource
 */
class ResourceMacro extends WikiMacro
{
	/**
	 * Allow macro in partial parsing?
	 *
	 * @var  string
	 */
	public $allowPartial = true;

	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return  array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'This macro will insert a linked title to a resource. It can be passed wither an ID or alias.';
		$txt['html'] = '<p>This macro will insert a linked title to a resource. It can be passed wither an ID or alias.</p>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return  string
	 */
	public function render()
	{
		$this->linkLog = array();

		$et = $this->args;

		if (!$et)
		{
			return '';
		}

		$p = explode(',', $et);
		$resource = array_shift($p);

		$nolink = false;
		$scrnshts = false;
		$num = 1;
		$p = explode(' ', end($p));
		foreach ($p as $a)
		{
			$a = trim($a);

			if (substr($a,0,11) == 'screenshots')
			{
				$bits = explode('=', $a);
				$num = intval(end($bits));
				$scrnshts = true;
			}
			elseif ($a == 'nolink')
			{
				$nolink = true;
			}
		}

		// Is it numeric?
		if (is_numeric($resource))
		{
			// Yes, then get resource by ID
			$id = intval($resource);
			$sql = "SELECT id, title, alias FROM `#__resources` WHERE id=" . $this->_db->quote($id);
		}
		else
		{
			// No, get resource by alias
			$sql = "SELECT id, title, alias FROM `#__resources` WHERE alias=" . $this->_db->quote(trim($resource));
		}

		// Perform query
		$this->_db->setQuery($sql);
		$r = $this->_db->loadRow();

		// Did we get a result from the database?
		if ($r)
		{
			if ($scrnshts && $r[2])
			{
				return $this->screenshots($r[2], $num);
			}

			// Build and return the link
			if ($r[2])
			{
				$link = 'index.php?option=com_resources&amp;alias=' . $r[2];
			}
			else
			{
				$link = 'index.php?option=com_resources&amp;id=' . $id;
			}

			$this->linkLog[] = array(
				'link'     => '[[Resource(' . $et . ')]]',
				'url'      => $link,
				'page_id'  => $this->pageid,
				'scope'    => 'resource',
				'scope_id' => $r[0]
			);

			if ($nolink)
			{
				return stripslashes($r[1]);
			}
			else
			{
				return '<a href="' . Route::url($link) . '">' . stripslashes($r[1]) . '</a>';
			}
		}

		// Return error message
		return '(Resource(' . $et . ') failed)';
	}

	/**
	 * Get a list of screenshots
	 *
	 * @param      string  $alias Resource alias
	 * @param      integer $num   Number of screenshots to show
	 * @return     string
	 */
	public function screenshots($alias, $num=1)
	{
		$config = Component::params('com_resources');
		$path = DS . trim($config->get('toolpath', '/site/tools'), DS);

		$alias = strtolower($alias);
		$d = @dir(PATH_APP . $path . DS . $alias);
		$images = array();

		if ($d)
		{
			while (false !== ($entry = $d->read()))
			{
				$img_file = $entry;
				if (is_file(PATH_APP . $path . DS . $alias . DS . $img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html')
				{
					if (preg_match("#bmp|gif|jpg|png|swf#i", $img_file))
					{
						$images[] = $img_file;
					}
				}
			}
			$d->close();
		}
		sort($images);

		$html = '';

		if (count($images) > 0)
		{
			$k = 0;
			for ($i=0, $n=count($images); $i < $n; $i++)
			{
				$tn = $this->thumbnail($images[$i]);
				$type = explode('.', $images[$i]);

				if (is_file(PATH_APP . $path . DS . $alias . DS . $tn) && $k < $num)
				{
					$k++;

					$html .= '<a rel="lightbox" href="' . $path . DS . $alias . DS . $images[$i].'" title="Screenshot #' . $k . '">';
					$html .= '<img src="' . $path . DS . $alias . DS . $tn . '" alt="Screenshot #' . $k . '" /></a>';
				}
			}
		}

		return $html;
	}

	/**
	 * Generate a thumbnail name from a picture name
	 *
	 * @param   string  $pic  Picture name
	 * @return  string
	 */
	public function thumbnail($pic)
	{
		$pic = explode('.', $pic);
		$n = count($pic);
		$pic[$n-2] .= '-tn';
		$end = array_pop($pic);
		$pic[] = 'gif';
		$tn = implode('.', $pic);
		return $tn;
	}
}
