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
	define("t","\t");
	define("n","\n");
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
	
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
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
	
	public function select($option, $modules) 
	{
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			submitform( pressbutton );
		}
		</script>
		<form action="index.php" method="post" name="adminForm">
			<p><strong>Warning!</strong> This can be a resource intensive process and should not be performed frequently.</p>
			<fieldset class="adminform">
				<table class="admintable">
					<tbody>
						<tr>
							<td class="key"><label for="module">Module:</label></td>
							<td>
								<select name="module" id="module">
									<option value="">Select...</option>
									<?php
									foreach ($modules as $module) 
									{
										echo '<option value="'.$module->id.'">'.stripslashes($module->title).'</option>'.n;
									}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td class="key"><label for="column">Column:</label></td>
							<td>
								<select name="column" id="column">
									<option value="0">One</option>
									<option value="1">Two</option>
									<option value="2">Three</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="key"><label for="position">Position:</label></td>
							<td>
								<select name="position" id="position">
									<option value="first">First</option>
									<option value="last">Last</option>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>

			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="push" />
		</form>
		<?php
	}

	public function controlpanel($option, $availmods, $usermods, $uid)
	{
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.getElementById('adminForm');
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			submitform( pressbutton );
		}
		</script>
		<?php
		$html  = '<form action="index.php?option='.$option.'" method="post" name="adminForm" id="cpnlc">'.n;
		$html .= t.'<input type="hidden" name="task" value="save" />'.n;
		$html .= t.'<input type="hidden" name="uid" id="uid" value="'. $uid .'" />'.n;
		$html .= t.'<input type="hidden" name="serials" id="serials" value="'. $usermods[0].';'.$usermods[1].';'.$usermods[2] .'" />'.n;
		$html .= t.'<h3>'.JText::_('MODULES').'</h3>'.n;
		$html .= t.'<p>Click on a module name from the list to add it to your page.</p>'.n;
		$html .= t.'<div id="available">'.n;
		$html .= MyhubHtml::moduleList( $availmods );
		$html .= t.'</div>'.n;
		$html .= t.'<div class="clear"></div>'.n;
		$html .= '</form>'.n;
		
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

	public function moduleContainer( $module, $params, $rendered, $container, $extras, $database, $option, $act='' ) 
	{
		$html  = '';
		
		if ($container) {
			$html .= '<div class="draggable" id="mod_'.$module->id.'"';
			//$html .= ($module->module == 'mod_mysessions') ? ' class="emphasis"' : '';
			$html .= '>'.n;
		}
		
		if ($extras) {
			//$html .= '<div class="draggable" id="lid'.$module->id.'">'.n;
			$html .= t.'<div class="cwrap">'.n;
			//if ($module->module != 'mod_mysessions') {
				// Add the 'close' button
				if ($act == 'customize') {
					$html .= '<a class="close" href="'.JRoute::_('index.php?option='.$option).'#" onclick="HUB.Myhub.removeModule(this);return false;" title="'.JText::_('REMOVE_MODULE').'">[ X ]</a>';
				}
				// Add the module title
				$html .= t.t.'<h3 class="handle">'.$module->title.'</h3>'.n;
				$html .= t.t.'<div class="body">'.n;
				
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
			//}
		}

		// Is it a custom module (i.e., HTML)?
		if ($module->module == 'mod_custom') { 
			$html .= $module->content;
		} else {
			$rparams['style'] = 'none';
			//$module = JModuleHelper::getModule( $module->module );
			$module->user = false;
			$html .= '<p>Module content here.</p>'; //MyhubHtml::renderModule($module, $rparams);
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
	
	public function renderModule($module, $attribs = array())
	{
		static $chrome;
		global $mainframe, $option;
		
		$scope = $mainframe->scope; //record the scope
		$mainframe->scope = $module->module;  //set scope to component name
		
		// Handle legacy globals if enabled
		if ($mainframe->getCfg('legacy'))
		{
			// Include legacy globals
			global $my, $database, $acl, $mosConfig_absolute_path;

			// Get the task variable for local scope
			$task = JRequest::getString('task');

			// For backwards compatibility extract the config vars as globals
			$registry =& JFactory::getConfig();
			foreach (get_object_vars($registry->toObject()) as $k => $v) {
				$name = 'mosConfig_'.$k;
				$$name = $v;
			}
			$contentConfig = &JComponentHelper::getParams( 'com_content' );
			foreach (get_object_vars($contentConfig->toObject()) as $k => $v)
			{
				$name = 'mosConfig_'.$k;
				$$name = $v;
			}
			$usersConfig = &JComponentHelper::getParams( 'com_users' );
			foreach (get_object_vars($usersConfig->toObject()) as $k => $v)
			{
				$name = 'mosConfig_'.$k;
				$$name = $v;
			}
		}

		// Get module parameters
		$params = new JParameter( $module->params );

		// Get module path
		$module->module = preg_replace('/[^A-Z0-9_\.-]/i', '', $module->module);
		$path = JPATH_ROOT.DS.'modules'.DS.$module->module.DS.$module->module.'.php';

		// Load the module
		if (!$module->user && file_exists( $path ) && empty($module->content))
		{
			$lang =& JFactory::getLanguage();
			$lang->load($module->module);

			$content = '';
			ob_start();
			require $path;
			$module->content = ob_get_contents().$content;
			ob_end_clean();
		}

		// Load the module chrome functions
		if (!$chrome) {
			$chrome = array();
		}

		require_once (JPATH_ROOT.DS.'templates'.DS.'system'.DS.'html'.DS.'modules.php');
		$chromePath = JPATH_ROOT.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'modules.php';
		if (!isset( $chrome[$chromePath]))
		{
			if (file_exists($chromePath)) {
				require_once ($chromePath);
			}
			$chrome[$chromePath] = true;
		}

		//make sure a style is set
		if(!isset($attribs['style'])) {
			$attribs['style'] = 'none';
		}

		//dynamically add outline style
		if(JRequest::getBool('tp')) {
			$attribs['style'] .= ' outline';
		}

		foreach(explode(' ', $attribs['style']) as $style)
		{
			$chromeMethod = 'modChrome_'.$style;

			// Apply chrome and render module
			if (function_exists($chromeMethod))
			{
				$module->style = $attribs['style'];

				ob_start();
				$chromeMethod($module, $params, $attribs);
				$module->content = ob_get_contents();
				ob_end_clean();
			}
		}
		
		$mainframe->scope = $scope; //revert the scope
		
		return $module->content;
	}

	//-----------
	
	public function writeOptions( $option, $act )
	{
		//if ($act == 'customize') {
			//$html = t.t.'<p><a id="personalize" href="index.php?option='.$option.a.'task=save" title="'.JText::_('FINISH_PERSONALIZE_TITLE').'">'.JText::_('Finish Customizing').'</a></p>'.n;
		//} else {
			$html = t.t.'<p><a id="personalize" href="index.php?option='.$option.a.'task=customize" title="'.JText::_('PERSONALIZE_TITLE').'">'.JText::_('Customize').'</a></p>'.n;
		//}

		return $html;
	}
}
?>