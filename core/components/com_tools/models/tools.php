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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Models;

jimport('joomla.application.component.model');

/**
 * Tools Model
 */
class Tools extends \JModel
{
	/**
	 * Get application tools
	 *
	 * @return     array
	 */
	public function getApplicationTools()
	{
		$dh = @opendir('/opt/trac/tools');
		$result = array();

		if (!empty($dh))
		{
			while (($file = readdir($dh)) !== false)
			{
				if (is_dir('/opt/trac/tools/' . $file))
				{
					if (strncmp($file, '.', 1) != 0)
					{
						$result[] = $file;
					}
				}
			}

			closedir($dh);

			sort($result);

			if (count($result) > 0)
			{
				$aliases = implode("','", $result);

				$database = \App::get('db');

				$query = "SELECT v.id, v.instance, v.toolname, v.title, MAX(v.revision), v.toolaccess, v.codeaccess, v.state, t.state AS tool_state
							FROM #__tool as t, #__tool_version as v
							WHERE v.toolname IN ('" . $aliases . "') AND t.id=v.toolid
							AND (v.state='1' OR v.state='3')
							GROUP BY toolname
							ORDER BY v.toolname ASC";

				$database->setQuery($query);

				return $database->loadObjectList();
			}
		}

		return $result;
	}
}
