<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
	 ->js();

$url = 'index.php?option=' . $this->option . '&alias=' . $this->project->get('alias') . '&active=notes';

// Com_wiki adds /projects - strip it out
$this->content = str_replace('projects/projects/', 'projects/', $this->content);

$this->content = str_replace('projects/pr-' . $this->project->get('alias'), 'projects/' . $this->project->get('alias'), $this->content);

// Get the page
$page = $this->page;

$showSidePanel = ($this->task == 'view' or $this->task == 'page' or $this->task == 'wiki') && $page->get('id') ? true : false;

// Get all project notes
$notes = $this->note->getNotes();

// Get page parent notes (for breadcrumbs)
$parentNotes = array(); //$this->note->getParentNotes($this->scope);
if ($page->path && trim($page->path))
{
	$parentNotes = explode('/', trim($page->path));
}

// Get parent scope (to add subpages)
$parentScope = $this->scope . DS . $page->get('pagename');

// Build breadcrumbs
$bcrumb = '';
/*if ($parentNotes && count($parentNotes) > 0)
{
	foreach ($parentNotes as $parent)
	{
		$bcrumb .= ' &raquo; <span class="subheader"><a href="'.Route::url($url . '&scope=' . $parent->scope . '&pagename=' . $parent->pagename) . '">' . $parent->title . '</a></span>';
	}
}*/
if ($this->task == 'new')
{
	$bcrumb .= ' &raquo; <span class="subheader">'.Lang::txt('COM_PROJECTS_NOTES_TASK_NEW').'</span>';
}
elseif ($page->get('id'))
{
	$bcrumb .= ' &raquo; <span class="subheader"><a href="'.Route::url($page->link()).'">'. $page->get('title') . '</a></span>';

}

$tasks = array( 'edit', 'history', 'comments', 'delete', 'compare', 'addcomment', 'renamepage' );
if ($this->task != 'view' && in_array($this->task, $tasks))
{
	$bcrumb .= ' &raquo; <span class="subheader">' . Lang::txt('COM_PROJECTS_NOTES_TASK_' . strtoupper($this->task)) . '</span> ';
}

// Is note public?
$publicStamp = $this->params->get('enable_publinks')
	? $this->note->getPublicStamp($page->get('id')) : null;

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
				<?php
				$base = $page->link();
				$chr  = strstr($base, '?') ? '&' : '?';
				?>
				<a href="<?php echo Route::url($base . $chr . 'action=new'); ?>" class="icon-add add btn" title="<?php echo Lang::txt('COM_PROJECTS_NOTES_ADD_SUBPAGE'); ?>"><?php echo Lang::txt('COM_PROJECTS_NOTES_ADD_SUBPAGE'); ?></a>
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
