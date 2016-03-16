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

$this->css()
     ->js();

$i = 1;
$limit = intval($this->_params->get('icons_limit')) ? $this->_params->get('icons_limit') : 0;

$popup = '<ol class="sharelinks">';
$title = Lang::txt('PLG_PUBLICATION_SHARE_VIEWING', Config::get('sitename'), stripslashes($this->publication->title));
$metadata  = '<div class="share">'."\n";
$metadata .= "\t".Lang::txt('PLG_PUBLICATION_SHARE').': ';

// Available options
$sharing = array('facebook', 'twitter', 'google', 'pinterest', 'linkedin', 'delicious', 'reddit');

foreach ($sharing as $shared)
{
	if ($this->_params->get('share_' . $shared, 1) == 1)
	{
		// Show activity
		$link = $this->view('_item')
			->set('option', $this->option)
			->set('publication', $this->publication)
			->set('name', $shared)
			->loadTemplate();

		$metadata .= (!$limit || $i <= $limit) ? $link : '';
		$popup 	  .= '<li class="';
		$popup 	  .= ($i % 2) ? 'odd' : 'even';
		$popup    .= '">'. $link . '</li>';
		$i++;
	}
}

// Pop up more
if ($limit > 0 && $i > $limit)
{
	$metadata .= '...';
}
$popup .= '</ol>';

// Show pop-up?
if ($limit > 0)
{
	$metadata .= '<dl class="shareinfo">'."\n";
	$metadata .= "\t".'<dd>'."\n";
	$metadata .= "\t\t".'<p>'."\n";
	$metadata .= "\t\t\t".Lang::txt('PLG_PUBLICATION_SHARE_RESOURCE')."\n";
	$metadata .= "\t\t".'</p>'."\n";
	$metadata .= "\t\t".'<div>'."\n";
	$metadata .= $popup;
	$metadata .= "\t\t".'</div>'."\n";
	$metadata .= "\t".'</dd>'."\n";
	$metadata .= '</dl>'."\n";
}
$metadata .= '</div>'."\n";

echo $metadata;
