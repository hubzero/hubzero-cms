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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$this->stages = array(JText::_('COM_TOOLS_CONTRIBTOOL_STEP_DESCRIPTION'),JText::_('COM_TOOLS_CONTRIBTOOL_STEP_CONTRIBUTORS'),
JText::_('COM_TOOLS_CONTRIBTOOL_STEP_ATTACHMENTS'),JText::_('COM_TOOLS_CONTRIBTOOL_STEP_TAGS'), JText::_('COM_TOOLS_CONTRIBTOOL_STEP_FINALIZE'));
$key = $this->stage-1;

$html = "\t\t".'<div class="clear"></div>'."\n";
$html .= "\t\t".'<ol id="steps" style="border-bottom:1px solid #ccc;margin-bottom:0;padding-bottom:0.7em;">'."\n";
$html .= "\t\t".' <li>'.JText::_('COM_TOOLS_CONTRIBTOOL_EDIT_PAGE_FOR').' ';
if ($this->version=='dev') 
{
	$html .= JText::_('COM_TOOLS_CONTRIBTOOL_TIP_NEXT_TOOL_RELEASE');
}
else 
{
	$html .= JText::_('COM_TOOLS_CONTRIBTOOL_TIP_CURRENT_VERSION');
}
$html .= ':</li>'."\n";

	for ($i=0, $n=count( $this->stages ); $i < $n; $i++)
	{
		$html .= "\t\t".' <li';
		if ($i==$key) 
		{
			$html .= ' class="active"';
		}
		$html .= '>';

		if ($this->version=='dev' && $i!=$key && ($i+1)!= count( $this->stages ))
		{
			$html .='<a href="'.JRoute::_('index.php?option=' . $this->option . '&task=' . $this->controller . '&step='.($i+1).'&app=' . $this->row->alias).'">'.$this->stages[$i].'</a>';
		}
		else if ($this->version=='current' && $i!=$key && ($i+1)!= count( $this->stages ) && ($i==0 or $i==3 or $i==2))
		{
			$html .='<a href="'.JRoute::_('index.php?option=' . $this->option . '&task=' . $this->controller . '&step='.($i+1).'&app=' . $this->row->alias . '&editversion=current').'">'.$this->stages[$i].'</a>';
		}
		else 
		{
			$html .= $this->stages[$i];
		}

		$html .= '</li>'."\n";
	}

$html .= "\t\t".'</ol>'."\n";
$html .= "\t\t".'<p class="';
if ($this->version=='dev') 
{
	if ($this->vnum) 
	{
		$html .= 'devversion">'.ucfirst(JText::_('COM_TOOLS_VERSION')).' '.$this->vnum;
	}
	else 
	{
		$html .= 'devversion">'.ucfirst(JText::_('Next version'));
	}
	$html .= ' - '.JText::_('not published yet (changes take effect later)');
}
else if ($this->version=='current' ) 
{
	$html .= 'currentversion">'.ucfirst(JText::_('COM_TOOLS_VERSION')).' '.$this->vnum.' - '.JText::_('published now (changes take effect immediately)');
}
$html .= ($this->version=='dev' && $this->status['published']) ? ' <span style="margin-left:2em;"><a href="'.JRoute::_('index.php?option=' . $this->option . '&task=' . $this->controller . '&step='.$this->stage.'&app=' . $this->row->alias . '&editversion=current').'">'.JText::_('change current published version instead').'</a></span>' : '';
$html .= ($this->version=='current' && $this->status['published']) ? ' <span style="margin-left:2em;"><a href="'.JRoute::_('index.php?option=' . $this->option . '&task=' . $this->controller . '&step='.$this->stage.'&app=' . $this->row->alias).'">'.JText::_('change upcoming version instead').'</a></span>' : '' ;
$html .='</p>'."\n";

$html .= "\t\t".'<div class="clear"></div>'."\n";

echo $html;