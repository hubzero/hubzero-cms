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

/**
 * Answers plugin for tools
 */
class plgAnswersTools extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Modify or append to query filters
	 *
	 * @param   array  $filters
	 * @return  array
	 */
	public function onQuestionsPrepareFilters($filters)
	{
		if ($filters['area'] == 'assigned')
		{
			require_once Component::path('com_tools') . DS . 'tables' . DS . 'author.php';

			// What tools did this user contribute?
			$db = App::get('db');

			$TA = new Components\Tools\Tables\Author($db);
			$tools = $TA->getToolContributions(User::get('id'));
			$mytooltags = array();
			if ($tools)
			{
				foreach ($tools as $tool)
				{
					$mytooltags[] = 'tool' . $tool->toolname;
				}
			}

			$filters['tag'] .= ($filters['tag'] ? ',' : '') . implode(',', $mytooltags);

			return $filters;
		}
	}

	/**
	 * Return a list of users to be notified of changes
	 *
	 * @param   object  $row
	 * @return  array
	 */
	public function onQuestionNotify($row)
	{
		$tags = $row->tags('string');
		$tags = preg_split("/[,;]/", $tags);

		if (!count($tags))
		{
			return;
		}

		$recipients = array();

		require_once Component::path('com_tools') . DS . 'tables' . DS . 'author.php';
		require_once Component::path('com_tools') . DS . 'tables' . DS . 'version.php';

		$db = App::get('db');
		$TA = new Components\Tools\Tables\Author($db);
		$objV = new Components\Tools\Tables\Version($db);

		foreach ($tags as $tag)
		{
			if ($tag == '')
			{
				continue;
			}

			if (preg_match('/tool:/', $tag))
			{
				$toolname = preg_replace('/tool:/', '', $tag);
				if (trim($toolname))
				{
					$rev = $objV->getCurrentVersionProperty($toolname, 'revision');
					$authors = $TA->getToolAuthors('', 0, $toolname, $rev);
					if (count($authors) > 0)
					{
						foreach ($authors as $author)
						{
							$recipients[] = $author->uidNumber;
						}
					}
				}
			}
		}

		if (count($recipients))
		{
			return $recipients;
		}
	}
}
