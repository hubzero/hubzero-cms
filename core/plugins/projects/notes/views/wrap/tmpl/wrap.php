<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
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

$this->css()
	 ->js();

$url = 'index.php?option=' . $this->option . '&alias=' . $this->project->get('alias') . '&active=notes';

// Com_wiki adds /projects - strip it out
$this->content = str_replace('projects/projects/', 'projects/', $this->content);

$this->content = str_replace('projects/pr-' . $this->project->get('alias') , 'projects/' . $this->project->get('alias'), $this->content);

// Get the page
$page = $this->page;

$showSidePanel = ($this->task == 'view' or $this->task == 'page' or $this->task == 'wiki') && $page->get('id') ? true : false;

// Get all project notes
$notes = $this->note->getNotes();

// Get page parent notes (for breadcrumbs)
$parentNotes = $this->note->getParentNotes($this->scope);

// Get parent scope (to add subpages)
$parentScope = $this->scope . DS . $page->get('pagename');

// Build breadcrumbs
$bcrumb = '';
if ($parentNotes && count($parentNotes) > 0)
{
	foreach ($parentNotes as $parent)
	{
		$bcrumb .= ' &raquo; <span class="subheader"><a href="'.Route::url($url . '&scope=' . $parent->scope . '&pagename=' . $parent->pagename) . '">' . $parent->title . '</a></span>';
	}
}
if ($this->task == 'new')
{
	$bcrumb .= ' &raquo; <span class="subheader">'.Lang::txt('COM_PROJECTS_NOTES_TASK_NEW').'</span>';
}
elseif ($page->get('id'))
{
	$bcrumb .= ' &raquo; <span class="subheader"><a href="'.Route::url( $url . '&scope=' . $this->scope . '&pagename=' . $page->get('name')).'">'. $page->get('title') . '</a></span>';

}

$tasks = array( 'edit', 'history', 'comments', 'delete', 'compare', 'addcomment', 'renamepage' );
if ($this->task != 'view' && in_array($this->task, $tasks))
{
	$bcrumb .= ' &raquo; <span class="subheader">' . Lang::txt('COM_PROJECTS_NOTES_TASK_' . strtoupper($this->task)) . '</span> ';
}

// Is note public?
$publicStamp = $this->params->get('enable_publinks')
	? $this->note->getPublicStamp($page->get('id')) : NULL;

$listed = $publicStamp ? $publicStamp->listed : false;

?>
<div id="plg-header">
	<h3 class="notes"><?php if ($bcrumb) { ?><a href="<?php echo Route::url($url); ?>"><?php } ?><?php echo $this->title; ?><?php if ($bcrumb) { ?></a><?php } ?> <?php  echo $bcrumb; ?></h3>
</div>
<?php if ($showSidePanel && $this->project->access('content')) { ?>
<ul id="page_options" class="pluginOptions">
	<li>
		<a class="icon-add add btn"  href="<?php echo Route::url($url . '&action=new'); ?>" title="<?php echo Lang::txt('COM_PROJECTS_NOTES_ADD_NOTE'); ?>">
			<?php echo Lang::txt('COM_PROJECTS_NOTES_ADD_NOTE'); ?>
		</a>
		<?php if (count($parentNotes) < 2) { ?>
		<a href="<?php echo Route::url('index.php?option=' . $this->option . '&scope=' . $parentScope . '&action=new'); ?>" class="icon-add add btn" title="<?php echo Lang::txt('COM_PROJECTS_NOTES_ADD_SUBPAGE'); ?>"><?php echo Lang::txt('COM_PROJECTS_NOTES_ADD_SUBPAGE'); ?></a>
		<?php } ?>
	</li>
</ul>
<?php } ?>

<div id="notes-wrap" class="grid">
	<div class="col <?php echo $showSidePanel ? 'span9' : 'span12 omega'; ?>">
		<div id="notes-content" class="notes-content <?php echo $listed ? 'listed-note' : 'unlisted-note'; ?>">
		<?php echo $this->content; ?>
		<div id="scope" class="hidden"><?php echo $this->scope; ?></div>
		</div>
		<?php if ($this->params->get('enable_publinks') && $this->page->get('id') && $this->project->access('content')) {
			$this->view('sharelink')
			     ->set('option', $this->option)
			     ->set('page', $this->page)
			     ->set('task', $this->task)
			     ->set('project', $this->project)
			     ->set('publicStamp', $publicStamp)
			     ->display();
		} ?>
	</div>
	<?php if ($showSidePanel) { ?>
		<div class="col span3 omega">
			<div>
				<?php $this->view('list')
				     ->set('option', $this->option)
				     ->set('page', $this->page)
					 ->set('scope', $this->scope)
				     ->set('task', $this->task)
					 ->set('note', $this->note)
				     ->set('project', $this->project)
				     ->display();
				?>
			</div>
		</div>
	<?php } ?>
</div>
