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

// No direct access
defined('_HZEXEC_') or die();
?>

<?php 
$boards = array();
foreach ($this->items as $item) {
	if ($item["type"] === "static") {
		require $this->getLayoutPath('_billboards');
	} elseif ($item["type"] === "dynamic") {
		switch ($item["content"])
		{
			case 'publications':
				$item_pubs = $this->_getPublications($item);
				require $this->getLayoutPath('_publications');
			break;

			case 'groups':
				$item_groups = $this->_getGroups($item);
				require $this->getLayoutPath('_groups');
			break;

			case 'partners':
				$item_partners = $this->_getPartners($item);
				require $this->getLayoutPath('_partners');
			break;

			default:
				echo 'Showcase Module Error: Unknown dynamic type "' . $item["content"] . '".  Possible values include "publications".';
			break;
		}
	} else {
		echo 'Showcase Module Error: Unknown type "' . $item["type"] . '".  Possible values include "static" or "dynamic".';
	}
}
?>