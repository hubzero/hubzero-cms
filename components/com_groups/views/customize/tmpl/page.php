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

$base_link = 'index.php?option=com_groups&gid='.$this->group->get('cn').'&task=managepages';

//default some form vars
$form_btn = "Add Page";
$form_title = "Add a New Group Page";
$new = 1;

//default group page vars
$id = '';
$gid = '';
$title = '';
$content = '';
$url = '';
$order = '';
$active = '';

//if we are in edit mode
if($this->page) {
	$form_btn = "Update Page";
	$form_title = "Update the Group Page";
	$new = '';
	
	$id = $this->page['id'];
	$gid = $this->page['gid'];
	$title = $this->page['title'];
	$content = $this->page['content'];
	$url = $this->page['url'];
	$order = $this->page['porder'];
	$active = $this->page['active'];
}

//set var for asset browser
$lid = $this->group->get('gidNumber');
?>

<div id="content-header" class="full">
	<h2><?php echo $form_title; ?></h2>
</div>
<div id="content-header-extra">
	<p class="manage"><a href="<?php echo JRoute::_($base_link); ?>">Back to Manage Pages</a></p>
</div>


	<?php
		foreach($this->notifications as $notification) {
			echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
		}
	?>

<form action="<?php echo JRoute::_($base_link); ?>" method="POST" id="hubForm">
	<div class="explaination">
		<div id="asset_browser">
			<p><strong><?php echo JText::_('Upload files or images:'); ?></strong></p>
			<iframe width="100%" height="300" name="filer" id="filer" src="index.php?option=com_groups&amp;no_html=1&amp;task=media&amp;listdir=<?php echo $lid; ?>"></iframe>
		</div><!-- / .asset_browser -->
	</div>
	<fieldset>
		<h3><?php echo $form_title; ?></h3>
		<label>Page Title: <span class="required">Required</span>
			<input type="text" name="page[title]" value="<?php echo $title; ?>" />
		</label>
		<label>Page Content: <span class="optional">Optional</span>
			<?php
				ximport('Hubzero_Wiki_Editor');
				$editor =& Hubzero_Wiki_Editor::getInstance();
				echo $editor->display('page[content]', 'page[content]', stripslashes($content), '', '50', '15');
			?>
			<span class="hint"><a href="<?php echo JRoute::_('index.php?option=com_topics&scope=&pagename=Help:WikiFormatting'); ?>">Wiki formatting</a> is allowed.</span>
		</label>
		<input type="hidden" name="page[id]" value="<?php echo $id; ?>" />
		<input type="hidden" name="page[gid]" value="<?php echo $gid; ?>" />
		<input type="hidden" name="page[url]" value="<?php echo $url; ?>" />
		<input type="hidden" name="page[porder]" value="<?php echo $order; ?>" />
		<input type="hidden" name="page[active]" value="<?php echo $active; ?>" />
		<input type="hidden" name="page[new]" value="<?php echo $new; ?>" />
		<input type="hidden" name="sub_task" value="save_page" />
	</fieldset>
	<p class="submit"><input type="submit" name="page_submit" value="<?php echo $form_btn; ?>" /></p>
</form>