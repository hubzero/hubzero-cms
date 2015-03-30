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

if (!$this->model->isPublic())
{
	$privacy = '<span class="private">' . ucfirst(Lang::txt('COM_PROJECTS_PRIVATE')) . '</span>';
}
else
{
	$privacy = '<a href="' . Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&preview=1') .'" title="' . Lang::txt('COM_PROJECTS_PREVIEW_PUBLIC_PROFILE') . '">' . ucfirst(Lang::txt('COM_PROJECTS_PUBLIC')) . '</a>';
}

$start = ($this->publicView == false && $this->model->access('member')) ? '<span class="h-privacy">' . $privacy . '</span> ' . strtolower(Lang::txt('COM_PROJECTS_PROJECT')) : ucfirst(Lang::txt('COM_PROJECTS_PROJECT'));

?>
<div id="project-header" class="project-header">
	<div class="grid">
		<div class="col span10">
			<div class="pimage-container">
			<?php
			// Draw image
			$this->view('_image', 'projects')
			     ->set('model', $this->model)
			     ->set('option', $this->option)
			     ->display();
			?>
			</div>
			<div class="ptitle-container">
				<h2><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias')); ?>"><?php echo \Hubzero\Utility\String::truncate($this->escape($this->model->get('title')), 50); ?> <span>(<?php echo $this->model->get('alias'); ?>)</span></a></h2>
				<p>
				<?php echo $start .' ' . Lang::txt('COM_PROJECTS_BY').' ';
				if ($this->model->groupOwner())
				{
					if ($cn = $this->model->groupOwner('cn'))
					{
						echo ' ' . Lang::txt('COM_PROJECTS_GROUP')
							. ' <a href="/groups/' . $cn . '">' . $cn . '</a>';
					}
					else
					{
						echo Lang::txt('COM_PROJECTS_UNKNOWN') . ' ' . Lang::txt('COM_PROJECTS_GROUP');
					}
				}
				else
				{
					echo '<a href="/members/' . $this->model->owner('id') . '">' . $this->model->owner('name') . '</a>';
				}
				?>
				</p>
			</div>
		</div>
		<div class="col span2 omega">
			<?php
			// Member options
			if ($this->publicView == false)
			{
				$this->view('_options', 'projects')
				     ->set('model', $this->model)
				     ->set('option', $this->option)
				     ->display();
			}
?>
		</div>
		<div class="clear"></div>
	</div>
</div>
