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

$authIDs = array();
$html = '';
$i = 1;
$option = $this->option;

if ($this->authNames != NULL)
{
	$html = '<ul id="author-list">'."\n";
	foreach ($this->authNames as $authname)
	{
		$authIDs[] = $authname->id;
		$name = $authname->name;

		$org = ($authname->organization)
			? htmlentities($authname->organization,ENT_COMPAT,'UTF-8') : '';
		$credit = ($authname->credit)
			? htmlentities($authname->credit,ENT_COMPAT,'UTF-8') : '';
		$userid = $authname->user_id ? $authname->user_id : 'unregistered';

		$html .= "\t".'<li id="author_'.$authname->id.'" class="pick reorder">'
			. '<span class="ordernum">' . $i . '</span>. ' . $name . ' (' . $userid . ')';
		$html .= $org ? ' - <span class="org">' . $org . '</span>' : '';
		$html .= ' <a class="editauthor" href="index.php?option=' . $option . '&controller=items&task=editauthor&author=' . $authname->id .'" >' . JText::_('COM_PUBLICATIONS_EDIT') . '</a> ';
		$html .= ' <a class="editauthor" href="index.php?option=' . $option . '&controller=items&task=deleteauthor&aid=' . $authname->id .'"  > ' . JText::_('COM_PUBLICATIONS_DELETE') . '</a> ';
		if ($credit)
		{
			$html .= '<br />' . JText::_('COM_PUBLICATIONS_CREDIT') . ': ' . $credit;
		}
		$html .= '</li>' . "\n";
		$i++;
	}
	$html.= '</ul>';
}
else
{
	$html.= '<p class="notice">' . JText::_('COM_PUBLICATIONS_NO_AUTHORS') . '</p>';
}
if (count($this->authNames) > 1) {
	$html.= '<input type="hidden" value="" name="list" id="neworder" />';
	$html.= '<p class="tip">' . JText::_('COM_PUBLICATIONS_AUTHORS_REORDER_TIP') . '</p>';
	$html.= '<input type="button" onclick="submitbutton(\'saveorder\');" class="btn" value="Save Order" id="saveorder" />';
}

echo $html;

?>