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

$no_html = JRequest::getInt( 'no_html', 0 );
if (!$no_html) { 
?>

<?php 
	//special groups hub menu at top
	if($this->group->get('type') == 3) { ?>
	<div id="nanoHUB_toolbar">
		<ul>
			<li><a href="#">Main Menu</a>
				<ul>
					<?php foreach($this->menu as $menu) { ?>
						<li><a href="<?php echo $menu['alias']; ?>">&raquo; <?php echo $menu['name']; ?></a></li>
					<?php } ?>
				</ul>
			</li>
		</ul>
	</div>
<?php } ?>

	<div id="page_container" <?php if($this->group->get('type') == 3) { echo 'class="hasToolbar"'; } ?>>
		<div id="page_container_inner">

			<div id="page_sidebar">
				<div id="page_sidebar_inner">
					<?php
						//default logo
						$default_logo = DS.'components'.DS.$this->option.DS.'assets'.DS.'img'.DS.'group_default_logo.png';
						
						//logo link - links to group overview page
						$link = JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn')); 

						//path to group uploaded logo
						$path = '/site/groups/'.$this->group->get('gidNumber').DS.$this->group->get('logo');

						//if logo exists and file is uploaded use that logo instead of default
						$src = ($this->group->get('logo') != '' && is_file(JPATH_ROOT.$path)) ? $path : $default_logo;
					?>
					<a id="page_identity" href="<?php echo $link; ?>">
						<img src="<?php echo $src; ?>" />
					</a>

					<ul id="page_menu">
						<?php 
							foreach($this->hub_group_plugins as $plugin) {
								echo JHTML::_(
									'view_html.displayMenu',
									$this->user,
									$this->authorized,
									$this->option,
									$this->group,
									$this->pages,
									$this->tab,
									$this->group_plugin_access[$plugin['name']],
									$plugin['name'],
									$plugin['title']
								);
							} 
						?>
					</ul><!-- //end page menu -->

				</div><!-- //end page sidebar inner -->
			</div><!-- //end page sidebar -->

			<div id="page_main">
				<div id="page_header">
					<h2><?php echo $this->group->get('description'); ?></h2>
				</div><!-- // end page header -->
				<div id="page_notifications">
					<?php
						foreach($this->notifications as $notification) {
							echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
						}
					?>
				</div>
				<div id="page_content" class="group_<?php echo $this->tab; ?>">
					<?php
			 			} 
			
						echo JHTML::_(
							'view_html.displayContent',
							$this->user,
							$this->group,
							$this->tab,
							$this->sections,
							$this->hub_group_plugins,
							$this->group_plugin_access
						);
						
						if (!$no_html) { 
					?>
				</div>
			</div> <!-- //close page main -->

		</div> <!-- //close page container inner -->
	</div> <!-- //close page container -->
<?php } ?>