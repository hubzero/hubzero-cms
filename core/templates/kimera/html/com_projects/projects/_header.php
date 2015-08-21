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

// No direct access
defined('_HZEXEC_') or die();

$privacyTxt = !$this->model->isPublic()
	? Lang::txt('COM_PROJECTS_PRIVATE')
	: Lang::txt('COM_PROJECTS_PUBLIC');

if (!$this->model->isPublic())
{
	$privacy = '<span class="private">' . ucfirst($privacyTxt) . '</span>';
}
else
{
	$privacy = '<a href="' . Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&preview=1') . '" title="' . Lang::txt('COM_PROJECTS_PREVIEW_PUBLIC_PROFILE') . '">' . ucfirst($privacyTxt) . '</a>';
}

$start = ($this->showPrivacy == 2 && $this->model->access('member')) ? '<span class="h-privacy">' . $privacy . '</span> ' . strtolower(Lang::txt('COM_PROJECTS_PROJECT')) : ucfirst(Lang::txt('COM_PROJECTS_PROJECT'));
?>
<header class="content-header<?php if (!$this->showPic) { echo ' nopic'; } ?>">
	<?php if ($this->showPic) { ?>
		<div class="pthumb"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias')); ?>" title="<?php echo Lang::txt('COM_PROJECTS_VIEW_UPDATES'); ?>"><img src="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&controller=media&media=thumb'); ?>" alt="<?php echo $this->escape($this->model->get('title')); ?>" /></a></div>
	<?php } ?>
	<div class="ptitle">
	<h2>
		<a href="<?php echo Route::url($this->model->link()); ?>"><?php echo \Hubzero\Utility\String::truncate($this->escape($this->model->get('title')), 50); ?></a>
	</h2>
	<?php // Member options
	if (!empty($this->showOptions))
	{
		$this->view('_options', 'projects')
		     ->set('model', $this->model)
		     ->set('option', $this->option)
		     ->display();
	}
	?>
	<?php if ($this->model->groupOwner())
	{
		echo '<p class="groupowner">';
		echo ucfirst(Lang::txt('COM_PROJECTS_PROJECT'));
		echo ' ' . Lang::txt('COM_PROJECTS_BY') . ' ';
		if ($cn = $this->model->groupOwner('cn'))
		{
			echo ' ' . Lang::txt('COM_PROJECTS_GROUP')
				. ' <a href="/groups/' . $cn . '">' . $cn . '</a>';
		}
		else
		{
			echo Lang::txt('COM_PROJECTS_UNKNOWN') . ' ' . Lang::txt('COM_PROJECTS_GROUP');
		}
		echo '</p>';

	 } ?>
	</div>
</header>
