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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>

<?php
class View
{
	/**
	 * Get number of visible pages
	 *
	 *
	 */
	 	 
	/**
	 * Output menu
	 * 
	 * @param  [type] $pageArray [description]
	 * @return [type]            [description]
	 */
	public static function buildRecursivePageMenu($group, $pageArray, $pageids)
	{
		// get overview section access
		$access = \Hubzero\User\Group\Helper::getPluginAccess($group, 'overview');

		$out = '';

		// Only create sub-menu if at least one page visible to user
		if (sizeof($pageArray) > 0)
		{
			$out = '<ul class="cf">';
			foreach ($pageArray as $key => $page)
			{
				// dont show page links if there isnt an approved version

				if ($page->approvedVersion() === null)
				{
					continue;
				}

				// page access settings
				$pageAccess = ($page->get('privacy') == 'default') ? $access : $page->get('privacy');

				// is this the active page?
				$cls  = (\Components\Groups\Helpers\Pages::isPageActive($page)) ? 'active' : '';

				//page menu item
				if (($pageAccess == 'registered' && User::isGuest()) ||
				  ($pageAccess == 'members' && !in_array(User::get("id"), $group->get('members'))))
				{
					$out .= "<li class=\"protected\"><span class=\"page\">" . $page->get('title') . "</span></li>";
				}
				else
				{
					$out .= '<li class="' . $cls . '">';
					$out .= '<a class="page" title="' . $page->get('title') . '" href="' . $page->url() . '">' . $page->get('title') . '</a>';
				}

				// do we have child menu items				
				if (!is_array($page->get('children')))
				{
					$out .= '</li>';
				}
				else
				{
					$submenupages = $page->get('children');
					$submenupages = array_filter($submenupages, function($page) use ($pageids) {
						return in_array($page->get('id'), $pageids);
					});

					if (count($submenupages) > 0) {
						$out .= self::buildRecursivePageMenu($group, $submenupages, $pageids) . '</li>';
					} else {
						$out .= '</li>';
					}
				}
			}
			$out .= '</ul>';
		}
		return $out;
	}
}
?>

<?php
/**
 * Main code
 *
 */

// Get list of pages from group 
$db = App::get('db');
$db->setQuery(
	"SELECT p.id
	FROM `#__xgroups_pages` AS p
	INNER JOIN `#__xgroups_pages_categories` AS c ON c.id=p.category
	WHERE c.title=" . $db->quote('menu')
	);
$pageids = array_map(function($element){return $element->id;}, $db->loadObjectList());

if (count($pages) > 0)
{
	$menupages = $pages[0]->get('children');
	$menupages = array_filter($menupages, function($page) use ($pageids) {
		return in_array($page->get('id'), $pageids);
	});

	// append pages html
	// only pass in the children of the root node
	// basically skip the overview page here
	$item = View::buildRecursivePageMenu($this->group, $menupages, $pageids);
	
	echo $item;
}
?>
