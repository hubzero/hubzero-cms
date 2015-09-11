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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$dateFormat = 'M d, Y';

$baseManage = 'publications/submit';
$baseView   = 'publications';

$base = trim(preg_replace('/\/administrator/', '', Request::base()), '/');

$mconfig = Component::params('com_members');
$pPath   = trim($mconfig->get('webpath'), DS);
$profileThumb = NULL;

$append = '?from=' . $this->user->get('email');
$lastMonth = date('M Y', strtotime("-1 month"));

$message  = 'Here is the monthly update on your recent publications usage' . "\n";
$message .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'."\n\n";

for ($a = 0; $a < count($this->pubstats); $a++)
{
	// Check against limit
	if ($a >= $this->limit)
	{
		break;
	}

	$stat = $this->pubstats[$a];

	$sefManage = $baseManage . DS . $stat->publication_id . $append;
	$sefView   = $baseView . DS . $stat->publication_id . $append;

	$message .= 'Publication #' . $stat->publication_id . ' "' . stripslashes($stat->title) . '"' . "\n";
	$message .= 'View publication:          ' . $base . DS . trim($sefView, DS) . "\n";
	$message .= 'Manage publication:        ' . $base . DS . trim($sefManage, DS) . "\n\n";

	$message .= 'Usage in the past month... ' . "\n";
	$message .= 'Page views:                ' . $stat->monthly_views. "\n";
	$message .= 'Downloads:                 ' . $stat->monthly_primary. "\n";
	$message .= 'Total downloads to date:   ' . $stat->total_primary. "\n";

	$message .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'."\n\n";
}

$message = str_replace('<br />', '', $message);
$message = preg_replace('/\n{3,}/', "\n\n", $message);

echo $message;
