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

if ($this->getError()) {
	$html = '<p class="error">'.$this->getError().'</p>';
} else {
	$sttc = explode(',', $this->config->get('static'));
	if (!is_array($sttc))
	{
		$sttc = array();
	}
	$sttc = array_map('trim', $sttc);

	$html  = '';

	if ($this->container) 
	{
		$html .= '<div class="draggable" id="mod_'.$this->module->id.'">'."\n";
	}

	if ($this->extras) 
	{
		$html .= "\t".'<div class="cwrap';
		if (!in_array($this->module->module, $sttc)) 
		{
			$html .= '">'."\n";
			// Add the 'close' button
			//if ($this->act == 'customize') 
			//{
				//$html .= '<a class="close" href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->juser->get('id').'&active=dashboard').'#" title="'.JText::_('PLG_MEMBERS_DASHBOARD_REMOVE_MODULE').'">[ X ]</a>';
			//}
		} 
		else 
		{
			$html .= ' emphasis">'."\n";
		}
		// Add the module title
		$html .= "\t\t" . '<h3 class="handle">' . $this->escape($this->module->title) . '</h3>' . "\n";
		$html .= "\t\t" . '<div class="body">' . "\n";
		if (!in_array($this->module->module, $sttc)) 
		{
			if ($this->rendered != '') 
			{
				$html .= "\t\t\t".'<p class="modcontrols">';
				// Add the 'edit' button
				//if ($this->act == 'customize') {
					$html .= '<a class="edimodl" id="e_'.$this->module->id.'" href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->juser->get('id').'&active=dashboard#').'" title="'.JText::_('PLG_MEMBERS_DASHBOARD_EDIT_TITLE').'" onclick="return HUB.Myhub.editModule(this, \'f_'.$this->module->id.'\');">'.JText::_('PLG_MEMBERS_DASHBOARD_EDIT').'</a>';
				//} else {
				//	$html .= '<a class="edimodl" id="e_'.$this->module->id.'" href="'.JRoute::_('index.php?option='.$this->option.'&task=view&id='.$this->juser->get('id').'&active=dashboard&act=customize').'" title="'.JText::_('PLG_MEMBERS_DASHBOARD_EDIT_TITLE').'">'.JText::_('PLG_MEMBERS_DASHBOARD_EDIT').'</a>';
				//}
				$html .= '</p>'."\n";
				$html .= "\t\t\t".'<form class="fparams" id="f_'.$this->module->id.'" onsubmit="return HUB.Myhub.saveModule(this,'.$this->module->id.');">'."\n";
				$html .= $this->rendered;
				$html .= "\t\t\t\t".'<input type="submit" name="submit" value="'.JText::_('PLG_MEMBERS_DASHBOARD_BUTTON_SAVE').'" />'."\n";
				$html .= "\t\t\t".'</form>'."\n";
			}
		}
	}
if ($this->admin)
{
	$html .= '<p>[ module content ]</p>';
}
else 
{
	// Is it a custom module (i.e., HTML)?
	if ($this->module->module == 'mod_custom') 
	{
		$html .= $this->module->content;
	} 
	else 
	{
		$rparams = array();
		$rparams['style'] = 'none';
		$this->module->user = false;
		jimport('joomla.application.module.helper');
		$html .= JModuleHelper::renderModule($this->module, $rparams);
		if (!$this->container) 
		{
			$document = JFactory::getDocument();
			foreach ($document->_styleSheets as $strSrc => $strAttr)
			{
				if (strstr($strSrc, $this->module->module))
				{
					$html .= '<link rel="stylesheet" href="'.$strSrc.'" type="'.$strAttr['mime'].'"';
					if (!is_null($strAttr['media']))
					{
						$html .= ' media="'.$strAttr['media'].'" ';
					}
					if ($temp = JArrayHelper::toString($strAttr['attribs'])) 
					{
						$html .= ' ' . $temp;
					}
					$html .= ' />';
				}
			}
			foreach ($document->_scripts as $strSrc => $strType) 
			{
				if (strstr($strSrc, $this->module->module))
				{
					$html .= '<script src="'.$strSrc.'"></script>';
				}
			}
		}
	}
}
	if ($this->extras) 
	{
		$html .= "\t\t".'</div><!-- / .body -->'."\n";
		$html .= "\t".'</div><!-- / .cwrap -->'."\n";
	}

	if ($this->container) 
	{
		$html .= '</div><!-- / .draggable #mod_'.$this->module->id.' -->'."\n\n";
	}
}
echo $html;