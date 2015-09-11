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

use Hubzero\Base\Model;
use Hubzero\Utility\String;
use Components\Courses\Tables;
use Date;
use Lang;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'announcement.php');
require_once(__DIR__ . DS . 'base.php');

/**
 * Announcement model class for a course
 */
class Announcement extends Base
{
	/**
	 * JTable class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Courses\\Tables\\Announcement';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_courses.announcement.content';

	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'announcement';

	/**
	 * Returns a reference to this model
	 *
	 * This method must be invoked as:
	 *     $offering = \Components\Courses\Models\Announcement::getInstance($alias);
	 *
	 * @param   integer $oid ID (int)
	 * @return  object
	 */
	static function &getInstance($oid=0)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$oid]))
		{
			$instances[$oid] = new self($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string $as What data to return
	 * @return  string
	 */
	public function published($as='')
	{
		$dt = ($this->get('publish_up') && $this->get('publish_up') != '0000-00-00 00:00:00')
			? $this->get('publish_up')
			: $this->get('created');
		switch (strtolower($as))
		{
			case 'date':
				return Date::of($dt)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($dt)->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			break;

			default:
				return $dt;
			break;
		}
	}

	/**
	 * Get the content of the entry in various formats
	 *
	 * @param   string  $as      Format to return state in [text, number]
	 * @param   integer $shorten Number of characters to shorten text to
	 * @return  string
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
						'option'   => 'com_courses',
						'scope'    => 'courses',
						'pagename' => $this->get('id'),
						'pageid'   => 0,
						'filepath' => '',
						'domain'   => ''
					);

					$content = $this->get('content');
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						$this->_context,
						&$this,
						&$config
					));

					$this->set('content_parsed', $this->get('content'));
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
}

