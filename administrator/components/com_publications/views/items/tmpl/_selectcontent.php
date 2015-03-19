<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$pub = $this->pub;
$database = JFactory::getDBO();

if (!$pub->_attachments)
{
	return '<p class="notice">' . Lang::txt('COM_PUBLICATIONS_NO_CONTENT') . '</p>';
}
$html 	= '';
$prime  = $pub->_attachments[1];
$second = $pub->_attachments[2];

if ($this->useBlocks && isset($pub->_curationModel))
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
			$html .= '<li>('.$type.') ';
			$html .= $att->title ? $att->title : $att->path;
			$html .= $att->title != $att->path ? '<br /><span class="ctitle">' . $att->path.'</span>' : '';
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
			$html .= '<li>('.$type.') ';
			$html .= $att->title ? $att->title : $att->path;
			$html .= $att->title != $att->path ? '<br /><span class="ctitle">' . $att->path.'</span>' : '';
			$html .= '</li>'."\n";
		}
		$html .= '</ul>';
	}
	else
	{
		$html .= '<p class="notice">' . Lang::txt('COM_PUBLICATIONS_NO_CONTENT') . '</p>';
	}
}

echo $html;

?>