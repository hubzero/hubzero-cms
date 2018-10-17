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

$pub = $this->pub;
$database = App::get('db');

if (!$pub->_attachments)
{
	return '<p class="notice">' . Lang::txt('COM_PUBLICATIONS_NO_CONTENT') . '</p>';
}
$html   = '';
$prime  = $pub->_attachments[1];
$second = $pub->_attachments[2];

if (isset($pub->_curationModel))
{
	$prime    = $pub->_curationModel->getElements(1);
	$second   = $pub->_curationModel->getElements(2);
	$gallery  = $pub->_curationModel->getElements(3);

	// Get attachment type model
	$attModel = new \Components\Publications\Models\Attachments($database);

	// Draw list of primary elements
	$html .= '<h5>' . Lang::txt('COM_PUBLICATIONS_PRIMARY_CONTENT') . '</h5>';
	$list  = $attModel->listItems(
		$prime,
		$pub,
		'administrator'
	);
	$html .= $list ? $list : '<p class="notice">' . Lang::txt('COM_PUBLICATIONS_NO_CONTENT') . '</p>';

	// Draw list of secondary elements
	$html .= '<h5>' . Lang::txt('COM_PUBLICATIONS_SUPPORTING_CONTENT') . '</h5>';
	$list  = $attModel->listItems(
		$second,
		$pub,
		'administrator'
	);
	$html .= $list ? $list : '<p class="notice">' . Lang::txt('COM_PUBLICATIONS_NO_CONTENT') . '</p>';

	// Draw list of gallery elements
	$html .= '<h5>' . Lang::txt('COM_PUBLICATIONS_GALLERY') . '</h5>';
	$list  = $attModel->listItems(
		$gallery,
		$pub,
		'administrator'
	);
	$html .= $list ? $list : '<p class="notice">' . Lang::txt('COM_PUBLICATIONS_NO_CONTENT') . '</p>';
}
else
{
	$html .= '<h5>' . Lang::txt('COM_PUBLICATIONS_PRIMARY_CONTENT') . '</h5>';
	if ($prime)
	{
		$html .= '<ul class="content-list">';
		foreach ($prime as $att)
		{
			$type = $att->type;
			if ($att->type == 'file')
			{
				$ext  = explode('.', $att->path);
				$type = strtoupper(end($ext));
			}
			$title = $att->title ? $att->title : $att->path;
			$html .= '<li>(' . $type . ') ';
			$html .= $att->title ? $att->title : $att->path;
			$html .= $att->title != $att->path ? '<br /><span class="ctitle">' . $att->path . '</span>' : '';
			$html .= '</li>'."\n";
		}
		$html .= '</ul>';
	}
	else
	{
		$html .= '<p class="notice">' . Lang::txt('COM_PUBLICATIONS_NO_CONTENT') . '</p>';
	}
	$html .= '<h5>' . Lang::txt('COM_PUBLICATIONS_SUPPORTING_CONTENT') . '</h5>';
	if ($second)
	{
		$html .= '<ul class="content-list">';
		foreach ($second as $att)
		{
			$type = $att->type;
			if ($att->type == 'file')
			{
				$ext  = explode('.', $att->path);
				$type = strtoupper(end($ext));
			}
			$title = $att->title ? $att->title : $att->path;
			$html .= '<li>(' . $type . ') ';
			$html .= $att->title ? $att->title : $att->path;
			$html .= $att->title != $att->path ? '<br /><span class="ctitle">' . $att->path.'</span>' : '';
			$html .= '</li>' . "\n";
		}
		$html .= '</ul>';
	}
	else
	{
		$html .= '<p class="notice">' . Lang::txt('COM_PUBLICATIONS_NO_CONTENT') . '</p>';
	}
}

echo $html;
