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

namespace Modules\Syndicate;

use Hubzero\Module\Module;
use Hubzero\Utility\Arr;
use Document;

/**
 * Module helper class for syndicating a feed
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		// [!] Legacy comptibility
		$params = $this->params;

		$params->def('format', 'rss');

		$link = self::getLink($params);

		if (is_null($link))
		{
			return;
		}

		$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

		$text = htmlspecialchars($params->get('text'));

		require $this->getLayoutPath($params->get('layout', 'default'));
	}

	/**
	 * Get a link
	 *
	 * @param   object  $params  Registry
	 * @return  string
	 */
	static function getLink(&$params)
	{
		$data = Document::getHeadData();

		foreach ($data['links'] as $link => $value)
		{
			$value = Arr::toString($value);
			if (strpos($value, 'application/' . $params->get('format') . '+xml'))
			{
				return $link;
			}
		}
	}
}
