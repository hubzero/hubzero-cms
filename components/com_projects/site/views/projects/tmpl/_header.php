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

$privacyTxt = $this->project->private
	? Lang::txt('COM_PROJECTS_PRIVATE')
	: Lang::txt('COM_PROJECTS_PUBLIC');

if ($this->project->private)
{
	$privacy = '<span class="private">' . ucfirst($privacyTxt) . '</span>';
}
else
{
	$privacy = '<a href="' . Route::url('index.php?option=' . $this->option . '&alias=' . $this->project->alias . '&preview=1') . '" title="' . Lang::txt('COM_PROJECTS_PREVIEW_PUBLIC_PROFILE') . '">' . ucfirst($privacyTxt) . '</a>';
}

$start = ($this->showPrivacy == 2 && $this->project->owner) ? '<span class="h-privacy">' . $privacy . '</span> ' . strtolower(Lang::txt('COM_PROJECTS_PROJECT')) : ucfirst(Lang::txt('COM_PROJECTS_PROJECT'));

?>
<div id="content-header" <?php if (!$this->showPic) { echo 'class="nopic"'; } ?>>
<?php if ($this->showPic) { ?>
	<div class="pthumb"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->project->alias); ?>" title="<?php echo Lang::txt('COM_PROJECTS_VIEW_UPDATES'); ?>"><img src="<?php echo	Route::url('index.php?option=' . $this->option . '&alias=' . $this->project->alias . '&controller=media&media=thumb'); ?>" alt="<?php echo $this->project->title; ?>" /></a></div>
<?php } ?>
	<div class="ptitle">
		<h2><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->project->alias); ?>"><?php echo \Hubzero\Utility\String::truncate($this->project->title, 50); ?> <span>(<?php echo $this->project->alias; ?>)</span></a></h2>
		<?php if ($this->goBack)  { ?>
		<h3 class="returnln"><?php echo Lang::txt('COM_PROJECTS_RETURN_TO'); ?> <a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->project->alias); ?>"><?php echo Lang::txt('COM_PROJECTS_PROJECT_PAGE'); ?></a></h3>
		<?php } else { ?>
		<h3 <?php if ($this->showUnderline) { echo 'class="returnln"'; } ?>><?php echo $start .' '.Lang::txt('COM_PROJECTS_BY').' ';
		if ($this->project->owned_by_group)
		{
			$group = \Hubzero\User\Group::getInstance( $this->project->owned_by_group );
			if ($group)
			{
				echo ' '.Lang::txt('COM_PROJECTS_GROUP').' <a href="' . Route::url('index.php?option=com_groups&cn=' . $group->get('cn')) .'">' . $group->get('cn') . '</a>';
			}
			else
			{
				echo Lang::txt('COM_PROJECTS_UNKNOWN').' '.Lang::txt('COM_PROJECTS_GROUP');
			}
		}
		else
		{
			echo '<a href=="' . Route::url('index.php?option=com_members&id=' . $this->project->owned_by_user) .'">' . $this->project->fullname.'</a>';
		}
		?>
		<?php if ($this->showPrivacy == 1) { ?>
			<span class="privacy <?php if ($this->project->private) { echo 'private'; } ?>"><?php if (!$this->project->private) {  ?><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->project->alias . '&preview=1'); ?>"><?php } ?><?php echo $privacyTxt; ?><?php if (!$this->project->private) {  ?></a><?php } ?> <?php echo strtolower(Lang::txt('COM_PROJECTS_PROJECT')); ?>
			</span>
		<?php } ?>
		</h3>
		<?php } ?>
	</div>
</div><!-- / #content-header -->
