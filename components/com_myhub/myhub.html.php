<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if (!defined("n")) {
	define('n',"\n");
	define('t',"\t");
	define('r',"\r");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}

class MyhubHtml
{
	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------
	
	public function warning( $msg, $tag='p' )
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'.n;
	}

	//-----------

	public function div($txt, $cls='', $id='')
	{
		$html  = '<div';
		$html .= ($cls) ? ' class="'.$cls.'"' : '';
		$html .= ($id) ? ' id="'.$id.'"' : '';
		$html .= '>';
		$html .= ($txt != '') ? n.$txt.n : '';
		$html .= '</div><!-- / ';
		if ($id) {
			$html .= '#'.$id;
		}
		if ($cls) {
			$html .= '.'.$cls;
		}
		$html .= ' -->'.n;
		return $html;
	}

	//-----------
	
	public function writeTitle( $sname, $name='', $full='' ) 
	{
		if ($name) {
			$pageTitle = ': ' . $name;
		} else {
			$pageTitle = '';
		}

		$document =& JFactory::getDocument();
		$document->setTitle( JText::_('MY').' '.$sname.$pageTitle );
		
		return MyhubHtml::div( MyhubHtml::hed(2, JText::_('MY').' '.$sname), $full, 'content-header' ).n;
	}

	//-----------
	
	public function hed($level, $txt)
	{
		return '<h'.$level.'>'.$txt.'</h'.$level.'>';
	}

	//-----------

	public function controlpanel($option, $availmods, $usermods, $uid)
	{
		$html  = '<form action="'.JRoute::_('index.php?option='.$option).'" method="post" name="mysettings" id="cpnlc">'.n;
		$html .= t.'<input type="hidden" name="uid" id="uid" value="'. $uid .'" />'.n;
		$html .= t.'<input type="hidden" name="serials" id="serials" value="'. $usermods[0].';'.$usermods[1].';'.$usermods[2] .'" />'.n;
		$html .= t.'<h3>'.JText::_('MODULES').'</h3>'.n;
		$html .= t.'<p>Click on a module name from the list to add it to your page.</p>'.n;
		$html .= t.'<div id="available">'.n;
		$html .= MyhubHtml::moduleList( $availmods );
		$html .= t.'</div>'.n;
		$html .= t.'<div class="clear"></div>'.n;
		$html .= '</form>'.n;
		$html .= '<p class="undo"><a href="'.JRoute::_('index.php?option='.$option).'?act=customize&task=restore">'.JText::_('RESTORE_SETTINGS').'</a></p>'.n;
		
		return $html;
	}
	
	//-----------

	public function moduleList( $modules ) 
	{
		if ($modules) {
			$html  = t.t.'<ul>'.n;
			foreach ($modules as $module)
			{
				 $html .= t.t.t.'<li><label for="_add_m_'.$module->id.'">'.$module->title.'</label> <input type="button" value="'.JText::_('BUTTON_ADD').'" id="_add_m_'.$module->id.'" onclick="HUB.Myhub.addModule(\''.$module->id.'\');return false;" /></li>'.n;
			}
			$html .= t.t.'</ul>'.n;
		} else {
			$html  = MyhubHtml::warning( JText::_('NO_MODULES') ).n;
		}
		
		return $html;
	}
	
	//-----------

	public function moduleContainer( $module, $params, $rendered, $container, $extras, $database, $option, $config, $act='' ) 
	{
		$sttc = explode(',',$config->get('static'));
		if (!is_array($sttc)) {
			$sttc = array();
		}
		$sttc = array_map('trim',$sttc);
		
		$html  = '';
		
		if ($container) {
			$html .= '<div class="draggable" id="mod_'.$module->id.'"';
			//$html .= ($module->module == 'mod_mysessions') ? ' class="emphasis"' : '';
			$html .= '>'.n;
		}
		
		if ($extras) {
			//$html .= '<div class="draggable" id="lid'.$module->id.'">'.n;
			$html .= t.'<div class="cwrap';
			if (!in_array($module->module, $sttc)) {
				$html .= '">'.n;
				// Add the 'close' button
				if ($act == 'customize') {
					$html .= '<a class="close" href="'.JRoute::_('index.php?option='.$option).'#" onclick="HUB.Myhub.removeModule(this);return false;" title="'.JText::_('REMOVE_MODULE').'">[ X ]</a>';
				}
			} else {
				$html .= ' emphasis">'.n;
			}
			// Add the module title
			$html .= t.t.'<h3 class="handle">'.$module->title.'</h3>'.n;
			$html .= t.t.'<div class="body">'.n;
			if (!in_array($module->module, $sttc)) {
				if ($rendered != '') {
					$html .= t.t.t.'<p class="modcontrols">';
					// Add the 'edit' button
					if ($act == 'customize') {
						$html .= '<a class="edimodl" id="e_'.$module->id.'" href="'.JRoute::_('index.php?option='.$option).'#" title="'.JText::_('EDIT_TITLE').'" onclick="return HUB.Myhub.editModule(this, \'f_'.$module->id.'\');">'.JText::_('EDIT').'</a>';
					} else {
						$html .= '<a class="edimodl" id="e_'.$module->id.'" href="'.JRoute::_('index.php?option='.$option).'?act=customize" title="'.JText::_('EDIT_TITLE').'">'.JText::_('EDIT').'</a>';
					}
					$html .= '</p>'.n;
					$html .= t.t.t.'<form class="fparams" id="f_'.$module->id.'" onsubmit="return HUB.Myhub.saveModule(this,'.$module->id.');">'.n;
					$html .= $rendered;
					$html .= t.t.t.t.'<input type="submit" name="submit" value="'.JText::_('BUTTON_SAVE').'" />'.n;
					$html .= t.t.t.'</form>'.n;
				}
			}
		}

		// Is it a custom module (i.e., HTML)?
		if ($module->module == 'mod_custom') { 
			$html .= $module->content;
		} else {
			$rparams['style'] = 'none';
			//$module = JModuleHelper::getModule( $module->module );
			$module->user = false;
			$html .= JModuleHelper::renderModule($module, $rparams);
		}
		
		if ($extras) {
			$html .= t.t.'</div><!-- / .body -->'.n;
			$html .= t.'</div><!-- / .cwrap -->'.n;
		}
		
		if ($container) {
			$html .= '</div><!-- / .draggable #mod_'.$module->id.' -->'.n.n;
		}
		
		return $html;
	}

	//-----------
	
	public function writeOptions( $option, $act )
	{
		$html  = '<div id="content-header-extra">'.n;
		$html .= t.'<ul id="useroptions">'.n;
		if ($act == 'customize') {
			$html .= t.t.'<li class="last"><a id="personalize" href="'.JRoute::_('index.php?option='.$option).'" title="'.JText::_('FINISH_PERSONALIZE_TITLE').'">'.JText::_('FINISH_PERSONALIZE').'</a></li>'.n;
		} else {
			$html .= t.t.'<li class="last"><a id="personalize" href="'.JRoute::_('index.php?option='.$option).'?act=customize" title="'.JText::_('PERSONALIZE_TITLE').'">'.JText::_('PERSONALIZE').'</a></li>'.n;
		}
		//$html .= ' <li><a href="/password/change" title="Change your password">Change Password</a></li>'.n;
		//$html .= ' <li class="last"><a href="/myaccount/" title="View your account details">My Account</a></li>'.n;
		$html .= t.'</ul>'.n;
		$html .= '</div><!-- / #content-header-extra -->'.n;

		return $html;
	}
}
?>