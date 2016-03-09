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

namespace Components\Courses\Models;

use Hubzero\Utility\String;
use Components\Courses\Tables;
use Filesystem;
use Request;
use Lang;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'page.php');
require_once(__DIR__ . DS . 'base.php');

/**
 * Courses model class for a course
 */
class Page extends Base
{
	/**
	 * JTable class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Courses\\Tables\\Page';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_courses.page.content';

	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'page';

	/**
	 * Get the state of the entry as either text or numerical value
	 *
	 * @param      string  $as      Format to return state in [text, number]
	 * @param      integer $shorten Number of characters to shorten text to
	 * @return     mixed String or Integer
	 */
	public function content($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('content_parsed', null);
				if ($content === null)
				{
					$config = array(
						'option'   => Request::getCmd('option', 'com_courses'),
						'scope'    => Request::getVar('gid', ''),
						'pagename' => $this->get('url'),
						'pageid'   => '',
						'filepath' => DS . ltrim($this->config()->get('uploadpath', '/site/courses'), DS) . DS . $this->get('course_id') . DS . 'pagefiles' . ($this->get('offering_id') ? DS . $this->get('offering_id') : ''),
						'domain'   => $this->get('course_id')
					);
					if ($this->get('offering_id'))
					{
						$config['scope'] = Course::getInstance($this->get('course_id'))->get('alias') . DS . Offering::getInstance($this->get('offering_id'))->get('alias') . DS . 'pages';
					}
					if ($this->get('section_id'))
					{
						$config['filepath'] = DS . trim($this->config()->get('uploadpath', '/site/courses'), DS) . DS . $this->get('course_id') . DS . 'sections' . DS . $this->get('section_id') . DS . 'pagefiles';
					}

					$content = $this->get('content');
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						$this->_context,
						&$this,
						&$config
					));

					$this->set('content_parsed', (string) $this->get('content'));
					$this->set('content', $content);

					return $this->content($as, $shorten);
				}
				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->content('parsed'));
			break;

			case 'raw':
			default:
				$content = $this->get('content');
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
				$content = html_entity_decode($content);
			break;
		}

		if ($shorten)
		{
			$content = String::truncate($content, $shorten, $options);
		}
		return $content;
	}

	/**
	 * Copy an entry and associated data
	 *
	 * @param   integer $course_id   New course to copy to
	 * @param   integer $offering_id New offering to copy to
	 * @param   integer $section_id  New section to copy to
	 * @return  boolean True on success, false on error
	 */
	public function copy($course_id=null, $offering_id=null, $section_id=null)
	{
		// Get some old info we may need
		//  - Unit ID
		//  - Offering ID
		$p_id = $this->get('id');
		$c_id = $this->get('course_id');
		$o_id = $this->get('offering_id');
		$s_id = $this->get('section_id');

		// Reset the ID. This will force store() to create a new record.
		$this->set('id', 0);
		// Are we copying to a new offering?
		if ($course_id || $offering_id)
		{
			if ($course_id)
			{
				$this->set('course_id', $course_id);
			}
			if ($offering_id)
			{
				$this->set('offering_id', $offering_id);
			}
		}
		else
		{
			// Copying to the same offering so we want to distinguish
			// this unit from the one we copied from
			$this->set('title', $this->get('title') . ' (copy)');
		}
		if (!$this->store())
		{
			return false;
		}

		// Copy assets
		$src  = DS . trim($this->config()->get('uploadpath', '/site/courses'), DS) . DS . $c_id;
		if ($s_id)
		{
			$src .= DS . 'sections' . DS . $s_id . DS . 'pagefiles';
		}
		else
		{
			$src .= DS . 'pagefiles' . ($o_id ? DS . $o_id : '');
		}

		if (file_exists(PATH_APP . $src))
		{
			$dest = DS . trim($this->config()->get('uploadpath', '/site/courses'), DS) . DS . $this->get('course_id');
			if ($this->get('section_id'))
			{
				$dest .= DS . 'sections' . DS . $this->get('section_id') . DS . 'pagefiles';
			}
			else
			{
				$dest .= DS . 'pagefiles' . ($this->get('offering_id') ? DS . $this->get('offering_id') : '');
			}

			if (!file_exists(PATH_APP . $dest))
			{
				if (!Filesystem::copyDirectory(PATH_APP . $src, PATH_APP . $dest))
				{
					$this->setError(Lang::txt('Failed to copy page files.'));
				}
			}
		}

		return true;
	}
}

