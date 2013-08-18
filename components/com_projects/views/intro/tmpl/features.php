<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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
$html  = '';
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<div id="content-header-extra">
    <ul id="useroptions">
    	<li><a class="add" href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=start'); ?>"><?php echo JText::_('COM_PROJECTS_START_NEW'); ?></a></li>	
		<li><a class="browse" href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=browse'); ?>"><?php echo JText::_('COM_PROJECTS_BROWSE_PUBLIC_PROJECTS'); ?></a></li>		
	</ul>
</div><!-- / #content-header-extra -->
<div class="clear"></div>
<div id="feature-section">
	<div class="feature">
		<a name="feature-blog"></a>
		<div id="feature-blog">
			<div class="four columns first">
				<h3><?php echo JText::_('COM_PROJECTS_FEATURES_BLOG'); ?></h3>
				<p class="ima">&nbsp;</p>
			</div><!-- / .four columns first -->
			<div class="four columns second third">
				<p class="f-about"><?php echo JText::_('COM_PROJECTS_FEATURES_BLOG_ABOUT'); ?></p>
				<div class="two columns first">
					<p class="sub"><?php echo JText::_('COM_PROJECTS_FEATURES_BLOG_ABOUT_LEARN'); ?></p>
					<ul class="f-updates">
						<li class="team"><?php echo JText::_('COM_PROJECTS_FEATURES_BLOG_LEARN_TEAM'); ?></li>
						<li class="blog"><?php echo JText::_('COM_PROJECTS_FEATURES_BLOG_LEARN_BLOG'); ?></li>
						<li class="todo"><?php echo JText::_('COM_PROJECTS_FEATURES_BLOG_LEARN_TODO'); ?></li>
						<li class="notes"><?php echo JText::_('COM_PROJECTS_FEATURES_BLOG_LEARN_NOTES'); ?></li>
						<li class="files"><?php echo JText::_('COM_PROJECTS_FEATURES_BLOG_LEARN_FILES'); ?></li>
						<?php if($this->publishing) { ?>
						<li class="publications"><?php echo JText::_('COM_PROJECTS_FEATURES_BLOG_LEARN_PUB'); ?></li>
						<?php } ?>
					</ul>
				</div>
				<div class="two columns second">
					<p class="sub"><?php echo JText::_('COM_PROJECTS_FEATURES_PLANNED'); ?></p>
					<ul>
						<li><?php echo JText::_('COM_PROJECTS_FEATURES_BLOG_PLANNED_ONE'); ?></li>
						<li><?php echo JText::_('COM_PROJECTS_FEATURES_BLOG_PLANNED_TWO'); ?></li>
						<li><?php echo JText::_('COM_PROJECTS_FEATURES_BLOG_PLANNED_THREE'); ?></li>
						<li><?php echo JText::_('COM_PROJECTS_FEATURES_BLOG_PLANNED_FOUR'); ?></li>
					</ul>
					<p class="sub"><?php echo JText::_('COM_PROJECTS_FEATURES_WANT_FEATURE'); ?> 
						<span class="suggest"><a href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'task=add'.a.'category=general'.a.'id=1').'/?tag=projects,projects:microblog,com_projects'; ?>" ><?php echo JText::_('COM_PROJECTS_FEATURES_SUGGEST_FEATURE'); ?></a></span>
					</p>
					<p><a href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'category=general'.a.'id=1').'/?tags=projects,projects:microblog,com_projects'; ?>">&rarr; <?php echo JText::_('COM_PROJECTS_FEATURES_SEE_SUGGESTIONS'); ?></a></p>
				</div>
			</div><!-- / .four columns second -->
			<div class="four columns fourth">
				<div class="clear"></div>
			</div><!-- / .four columns last -->
			<div class="clear"></div>
		</div>
	</div>
	<div class="feature">
		<a name="feature-todo"></a>
		<div id="feature-todo">
			<div class="four columns first">
				<h3><?php echo JText::_('COM_PROJECTS_FEATURES_TODO'); ?></h3>
				<p class="ima">&nbsp;</p>
			</div><!-- / .four columns first -->
			<div class="four columns second third">			
				<div class="two columns first">
					<p class="f-about"><?php echo JText::_('COM_PROJECTS_FEATURES_TODO_ABOUT'); ?></p>
				</div>
				<div class="two columns second">
					<p class="sub"><?php echo JText::_('COM_PROJECTS_FEATURES_PLANNED'); ?></p>
					<ul>
						<li><?php echo JText::_('COM_PROJECTS_FEATURES_TODO_PLANNED_ONE'); ?></li>
						<li><?php echo JText::_('COM_PROJECTS_FEATURES_TODO_PLANNED_TWO'); ?></li>
						<li><?php echo JText::_('COM_PROJECTS_FEATURES_TODO_PLANNED_THREE'); ?></li>
					</ul>
					<p class="sub"><?php echo JText::_('COM_PROJECTS_FEATURES_WANT_FEATURE'); ?> 
						<span class="suggest"><a href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'task=add'.a.'category=general'.a.'id=1').'/?tag=projects,projects:todo,com_projects'; ?>" ><?php echo JText::_('COM_PROJECTS_FEATURES_SUGGEST_FEATURE'); ?></a></span>
					</p>
					<p><a href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'category=general'.a.'id=1').'/?tags=projects,projects:todo,com_projects'; ?>">&rarr; <?php echo JText::_('COM_PROJECTS_FEATURES_SEE_SUGGESTIONS'); ?></a></p>
				</div>
			</div><!-- / .four columns second -->
			<div class="four columns fourth">
				<div class="clear"></div>
			</div><!-- / .four columns last -->
			<div class="clear"></div>
		</div>
	</div>
	<div class="feature">
		<a name="feature-notes"></a>
		<div id="feature-notes">
			<div class="four columns first">
				<h3><?php echo JText::_('COM_PROJECTS_FEATURES_NOTES'); ?></h3>
				<p class="ima">&nbsp;</p>
			</div><!-- / .four columns first -->
			<div class="four columns second third">
				<div class="two columns first">
					<p class="f-about"><?php echo JText::_('COM_PROJECTS_FEATURES_NOTES_ABOUT'); ?></p>
				</div>
				<div class="two columns second">
					<p class="sub"><?php echo JText::_('COM_PROJECTS_FEATURES_PLANNED'); ?></p>
					<ul>
						<li><?php echo JText::_('COM_PROJECTS_FEATURES_NOTES_PLANNED_ONE'); ?></li>
						<li><?php echo JText::_('COM_PROJECTS_FEATURES_NOTES_PLANNED_TWO'); ?></li>
					</ul>
					<p class="sub"><?php echo JText::_('COM_PROJECTS_FEATURES_WANT_FEATURE'); ?> 
						<span class="suggest"><a href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'task=add'.a.'category=general'.a.'id=1').'/?tag=projects,projects:notes,com_projects'; ?>" ><?php echo JText::_('COM_PROJECTS_FEATURES_SUGGEST_FEATURE'); ?></a></span>
					</p>
					<p><a href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'category=general'.a.'id=1').'/?tags=projects,projects:notes,com_projects'; ?>">&rarr; <?php echo JText::_('COM_PROJECTS_FEATURES_SEE_SUGGESTIONS'); ?></a></p>
				</div>
			</div><!-- / .four columns second -->
			<div class="four columns fourth">
				<div class="clear"></div>
			</div><!-- / .four columns last -->
			<div class="clear"></div>
		</div>
	</div>
	<div class="feature">
		<a name="feature-team"></a>
		<div id="feature-team">
			<div class="four columns first">
				<h3><?php echo JText::_('COM_PROJECTS_FEATURES_TEAM'); ?></h3>
				<p class="ima">&nbsp;</p>
			</div><!-- / .four columns first -->
			<div class="four columns second third">
				<div class="two columns first">
					<p class="f-about"><?php echo JText::_('COM_PROJECTS_FEATURES_TEAM_ABOUT'); ?></p>
				</div>
				<div class="two columns second">
					<p class="sub"><?php echo JText::_('COM_PROJECTS_FEATURES_PLANNED'); ?></p>
					<ul>
						<li><?php echo JText::_('COM_PROJECTS_FEATURES_TEAM_PLANNED_ONE'); ?></li>
					</ul>
					<p class="sub"><?php echo JText::_('COM_PROJECTS_FEATURES_WANT_FEATURE_REQUEST'); ?> 
						<span class="suggest"><a href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'task=add'.a.'category=general'.a.'id=1').'/?tag=projects,projects:team,com_projects'; ?>" ><?php echo JText::_('COM_PROJECTS_FEATURES_SUGGEST_FEATURE'); ?></a></span>
					</p>
					<p><a href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'category=general'.a.'id=1').'/?tags=projects,projects:team,com_projects'; ?>">&rarr; <?php echo JText::_('COM_PROJECTS_FEATURES_SEE_SUGGESTIONS'); ?></a></p>
				</div>
			</div><!-- / .four columns second -->
			<div class="four columns fourth">
				<div class="clear"></div>
			</div><!-- / .four columns last -->
			<div class="clear"></div>
		</div>
	</div>
	<div class="feature">
		<a name="feature-files"></a>
		<div id="feature-files">
			<div class="four columns first">
				<h3><?php echo JText::_('COM_PROJECTS_FEATURES_FILES'); ?></h3>
				<p class="ima">&nbsp;</p>
			</div><!-- / .four columns first -->
			<div class="four columns second third">
				<div class="two columns first">
					<p class="f-about"><?php echo JText::_('COM_PROJECTS_FEATURES_FILES_ABOUT_START'); ?> <a href="http://git-scm.com/" rel="external"><?php echo JText::_('COM_PROJECTS_FEATURES_FILES_ABOUT_GIT'); ?></a> <?php echo JText::_('COM_PROJECTS_FEATURES_FILES_ABOUT_END'); ?></p>
				</div>
				<div class="two columns second">
					<p class="sub"><?php echo JText::_('COM_PROJECTS_FEATURES_PLANNED'); ?></p>
					<ul>
						<li><?php echo JText::_('COM_PROJECTS_FEATURES_FILES_PLANNED_ONE'); ?></li>
						<li><?php echo JText::_('COM_PROJECTS_FEATURES_FILES_PLANNED_TWO'); ?></li>
						<li><?php echo JText::_('COM_PROJECTS_FEATURES_FILES_PLANNED_THREE'); ?></li>
					</ul>
					<p class="sub"><?php echo JText::_('COM_PROJECTS_FEATURES_WANT_FEATURE'); ?> 
						<span class="suggest"><a href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'task=add'.a.'category=general'.a.'id=1').'/?tag=projects,projects:files,com_projects'; ?>" ><?php echo JText::_('COM_PROJECTS_FEATURES_SUGGEST_FEATURE'); ?></a></span>
					</p>
					<p><a href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'category=general'.a.'id=1').'/?tags=projects,projects:files,com_projects'; ?>">&rarr; <?php echo JText::_('COM_PROJECTS_FEATURES_SEE_SUGGESTIONS'); ?></a></p>
				</div>
			</div><!-- / .four columns second -->
			<div class="four columns fourth">
				<div class="clear"></div>
			</div><!-- / .four columns last -->
			<div class="clear"></div>
		</div>
	</div>
	
	<div class="feature">
		<a name="feature-publications"></a>
		<div id="feature-publications" <?php if(!$this->publishing) { echo 'class="in-the-works"'; } ?> >
			<div class="four columns first">
				<h3><?php echo JText::_('COM_PROJECTS_FEATURES_PUBLICATIONS'); ?><?php if(!$this->publishing) { echo '*'; } ?></h3>
				<?php if(!$this->publishing) { ?>
				<p class="wip"><?php echo JText::_('COM_PROJECTS_FEATURES_IN_THE_WORKS'); ?></p>
				<?php } ?>
				<p class="ima">&nbsp;</p>
			</div><!-- / .four columns first -->
			<div class="four columns second third">
				<div class="two columns first">
					<p class="f-about"><?php echo $this->publishing ? JText::_('COM_PROJECTS_FEATURES_PUBLICATIONS_ABOUT') : JText::_('COM_PROJECTS_FEATURES_PUBLICATIONS_ABOUT_WIP'); ?> </p>
				</div>
				<div class="two columns second">
						<p class="sub"><?php echo JText::_('COM_PROJECTS_FEATURES_WANT_FEATURE_REQUEST'); ?> 
							<span class="suggest"><a href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'task=add'.a.'category=general'.a.'id=1').'/?tag=projects,projects:publications,com_projects'; ?>" ><?php echo JText::_('COM_PROJECTS_FEATURES_SUGGEST_FEATURE'); ?></a></span>
						</p>
						<p><a href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'category=general'.a.'id=1').'/?tags=projects,projects:publications,com_projects'; ?>">&rarr; <?php echo JText::_('COM_PROJECTS_FEATURES_SEE_SUGGESTIONS'); ?></a></p>
				</div>
			</div><!-- / .four columns second -->
			<div class="four columns fourth">
				<div class="clear"></div>
			</div><!-- / .four columns last -->
			<div class="clear"></div>
		</div>
	</div>
	
	<div class="feature">
		<a name="feature-app"></a>
		<div id="feature-app">
			<div class="four columns first">
				<h3><?php echo JText::_('COM_PROJECTS_FEATURES_APPS'); ?>*</h3>
				<p class="wip"><?php echo JText::_('COM_PROJECTS_FEATURES_IN_THE_WORKS'); ?></p>
				<p class="ima">&nbsp;</p>
			</div><!-- / .four columns first -->
			<div class="four columns second third">
				<div class="two columns first">
					<p class="f-about"><?php echo JText::_('COM_PROJECTS_FEATURES_APPS_ABOUT_START'); ?> <a href="http://git-scm.com/" rel="external"><?php echo JText::_('COM_PROJECTS_FEATURES_APPS_ABOUT_GIT'); ?></a> <?php echo JText::_('COM_PROJECTS_FEATURES_APPS_ABOUT_END'); ?></p>
				</div>
				<div class="two columns second">
						<p class="sub"><?php echo JText::_('COM_PROJECTS_FEATURES_WANT_FEATURE_REQUEST'); ?> 
							<span class="suggest"><a href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'task=add'.a.'category=general'.a.'id=1').'/?tag=projects,projects:apps,com_projects'; ?>" ><?php echo JText::_('COM_PROJECTS_FEATURES_SUGGEST_FEATURE'); ?></a></span>
						</p>
						<p><a href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'category=general'.a.'id=1').'/?tags=projects,projects:apps,com_projects'; ?>">&rarr; <?php echo JText::_('COM_PROJECTS_FEATURES_SEE_SUGGESTIONS'); ?></a></p>
				</div>
			</div><!-- / .four columns second -->
			<div class="four columns fourth">
				<div class="clear"></div>
			</div><!-- / .four columns last -->
			<div class="clear"></div>
		</div>
	</div>
	
	<div class="feature">
		<a name="feature-activity"></a>
		<div id="feature-activity">
			<div class="four columns first">
				<h3><?php echo JText::_('COM_PROJECTS_FEATURES_ACTIVITY'); ?>*</h3>
				<p class="wip"><?php echo JText::_('COM_PROJECTS_FEATURES_IN_THE_WORKS'); ?></p>
				<p class="ima">&nbsp;</p>
			</div><!-- / .four columns first -->
			<div class="four columns second third">
				<div class="two columns first">
					<p class="f-about"><?php echo JText::_('COM_PROJECTS_FEATURES_ACTIVITY_ABOUT'); ?> </p>
				</div>
				<div class="two columns second">
						<p class="sub"><?php echo JText::_('COM_PROJECTS_FEATURES_WANT_FEATURE_REQUEST'); ?> 
							<span class="suggest"><a href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'task=add'.a.'category=general'.a.'id=1').'/?tag=projects,projects:activity,com_projects'; ?>" ><?php echo JText::_('COM_PROJECTS_FEATURES_SUGGEST_FEATURE'); ?></a></span>
						</p>
						<p><a href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'category=general'.a.'id=1').'/?tags=projects,projects:activity,com_projects'; ?>">&rarr; <?php echo JText::_('COM_PROJECTS_FEATURES_SEE_SUGGESTIONS'); ?></a></p>
				</div>
			</div><!-- / .four columns second -->
			<div class="four columns fourth">
				<div class="clear"></div>
			</div><!-- / .four columns last -->
			<div class="clear"></div>
		</div>
	</div>
	<div class="feature">
		<a name="feature-more"></a>
		<div id="feature-more">
			<div class="four columns first">
				<h3><?php echo JText::_('COM_PROJECTS_FEATURES_MORE'); ?>*</h3>
				<p class="wip"><?php echo JText::_('COM_PROJECTS_FEATURES_IN_THE_WORKS'); ?></p>
				<p class="ima">&nbsp;</p>
			</div><!-- / .four columns first -->
			<div class="four columns second third">
				<div class="two columns first">
					<p class="f-about"><?php echo JText::_('COM_PROJECTS_FEATURES_MORE_ABOUT'); ?> </p>
				</div>
				<div class="two columns second">
				<p class="sub"><?php echo JText::_('COM_PROJECTS_FEATURES_WANT_FEATURE_REQUEST'); ?> 
					<span class="suggest"><a href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'task=add'.a.'category=general'.a.'id=1').'/?tag=projects,projects:add-ons,com_projects'; ?>" ><?php echo JText::_('COM_PROJECTS_FEATURES_SUGGEST_FEATURE'); ?></a></span>
				</p>
				<p><a href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'category=general'.a.'id=1').'/?tags=projects,projects:add-ons,com_projects'; ?>">&rarr; <?php echo JText::_('COM_PROJECTS_FEATURES_SEE_SUGGESTIONS'); ?></a></p>
				</div>
			</div><!-- / .four columns second -->
			<div class="four columns fourth">
				<div class="clear"></div>
			</div><!-- / .four columns last -->
			<div class="clear"></div>
		</div>
	</div>
</div>
