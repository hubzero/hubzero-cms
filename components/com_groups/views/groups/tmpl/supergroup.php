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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//get site config
$config =& JFactory::getConfig(); 

$no_html = JRequest::getVar("no_html", 0);

//define the default group template
$default_path = 'components' . DS . 'com_groups' . DS . 'views' . DS . 'view' . DS . 'tmpl' . DS .'default.php';

//load in the special group template
$temp_path 	= 'site' . DS . 'groups' . DS . $this->group->get('gidNumber') . DS . 'template' . DS . 'default.php';
$css_path 	= 'site' . DS . 'groups' . DS . $this->group->get('gidNumber') . DS . 'template' . DS . 'default.css';
$js_path 	= 'site' . DS . 'groups' . DS . $this->group->get('gidNumber') . DS . 'template' . DS . 'default.js';

//start the output buffer
ob_start();

//if the template file exists use their custom template
if(is_file(JPATH_ROOT . DS . $temp_path)) {

	//partner template exists
	$tmpl = true;

	//get the document
	$doc =& JFactory::getDocument();

	//if the css file exists push to the page
	if(is_file(JPATH_ROOT . DS . $css_path)) {
		$doc->addStyleSheet( DS . $css_path );
	}

	//if the js file exists push to the page
	if(is_file(JPATH_ROOT . DS . $js_path)) {
		$doc->addScript( DS . $js_path );
	}

	//include the groups template
	include JPATH_ROOT . DS . $temp_path;
} 
else 
{
	//partner template doesnt exist
	$tmpl = false;

	//include the default template
	include JPATH_ROOT . DS . $default_path;
}

//get the buffer contents
$content = ob_get_contents();

//clean the buffer
ob_end_clean();

//display the content
echo $content;
?>
	
<?php 
	//return link for login and logout
	$return = JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn'));
	$return = base64_encode($return);
	
	$isManager = (in_array($this->juser->get("id"), $this->group->get("managers"))) ? true : false;
?>

<div class="clear"></div>

<?php if($tmpl && !$no_html) { ?>	
	<div id="special_management">
		<?php if(!$this->juser->get('guest')) : ?>
			<ul>
				<?php if($isManager) : ?>
					<li>Manage this group: </li>
					<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&task=edit'); ?>">Edit</a></li>
					<li>|</li>
					<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&task=customize'); ?>">Customize</a></li>
					<li>|</li>
					<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&task=pages'); ?>">Manage Group Pages</a></li>
					<li>|</li>
					<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&task=invite'); ?>">Invite Users</a></li>
					<li>|</li>
					<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&task=delete'); ?>">Delete</a></li>
				<?php else : ?>
					<li>Welcome <?php echo $this->juser->get('name'); ?>, </li>
				<?php endif; ?>
				<li><a href="/logout?return=<?php echo $return; ?>" class="logout">Logout</a>
			</ul>
		<?php else : ?>
			Want to edit this group? <a href="/login?return=<?php echo $return; ?>">Login now!</a>
		<?php endif; ?>
	</div><!-- /#special_management -->
<?php } ?>