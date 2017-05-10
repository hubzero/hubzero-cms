<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

// No direct access
defined('_HZEXEC_') or die();

// Sorting and paging
$sortbyDir  = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
$sortAppend = '&sortdir=' . urlencode($sortbyDir);

// Check used space against quota (percentage)
$inuse = round(($this->dirsize * 100 ) / $this->quota);
if ($inuse < 1)
{
	$inuse = round((($this->dirsize * 100 ) / $this->quota), 1);
	if ($inuse < 0.1)
	{
		$inuse = 0.0;
	}
}
$inuse = ($inuse > 100) ? 100 : $inuse;
$approachingQuota = $this->project->config('approachingQuota', 85);
$approachingQuota = intval($approachingQuota) > 0 ? $approachingQuota : 85;
$warning = ($inuse > $approachingQuota) ? 1 : 0;

$showStats = false;

$i = 1;

?>
<div id="plg-header">
	<h3 class="publications"><?php echo $this->title; ?></h3>
</div>

<?php if ($this->project->access('content')) { ?>
	<ul id="page_options" class="pluginOptions">
		<li>
			<a class="icon-add btn" href="<?php echo Route::url($this->project->link('publications') . '&action=start'); ?>">
				<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION'); ?>
			</a>
		</li>
	</ul>
<?php } ?>

<?php
if (count($this->rows) > 0)
{
	?>
	<form action="<?php echo Route::url($this->project->link('publications')); ?>" method="post" id="plg-form" >
		<div class="container">
			<div class="list-menu">
				<p class="msg-total"><?php echo ucfirst(Lang::txt('COM_PROJECTS_SHOWING')); ?> <?php if ($this->total <= count($this->rows)) { echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_ALL'); }?> <span class="prominent"> <?php echo count($this->rows); ?></span> <?php if ($this->total > count($this->rows)) { echo Lang::txt('COM_PROJECTS_OUT_OF') . ' ' . $this->total; } ?> <?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATIONS_S'); ?></p>
			</div>
			<table id="filelist" class="listing">
				<thead>
					<tr>
						<th></th>
						<th class="thtype<?php if ($this->filters['sortby'] == 'title') { echo ' activesort'; } ?>"><a href="<?php echo Route::url($this->project->link('publications') . $sortAppend . '&sortby=title'); ?>" class="re_sort" title="<?php echo Lang::txt('COM_PROJECTS_SORT_BY') . ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_TITLE'); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_TITLE'); ?></a></th>
						<th class="thtype<?php if ($this->filters['sortby'] == 'id') { echo ' activesort'; } ?>"><a href="<?php echo Route::url($this->project->link('publications') . $sortAppend . '&sortby=id'); ?>" class="re_sort" title="<?php echo Lang::txt('COM_PROJECTS_SORT_BY') . ' ' . Lang::txt('ID'); ?>"><?php echo Lang::txt('ID'); ?></a></th>
						<th class="thtype<?php if ($this->filters['sortby'] == 'type') { echo ' activesort'; } ?>"><a href="<?php echo Route::url($this->project->link('publications') . $sortAppend . '&sortby=type'); ?>" class="re_sort" title="<?php echo Lang::txt('COM_PROJECTS_SORT_BY') . ' ' .  Lang::txt('PLG_PROJECTS_PUBLICATIONS_TYPE'); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTENT_TYPE'); ?></a></th>
						<th<?php if ($this->filters['sortby'] == 'status') { echo ' class="activesort"'; } ?> colspan="2"><a href="<?php echo Route::url($this->project->link('publications') . $sortAppend . '&sortby=status'); ?>" class="re_sort" title="<?php echo Lang::txt('COM_PROJECTS_SORT_BY') . ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_STATUS'); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_STATUS'); ?></a></th>
						<th class="condensed centeralign"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_RELEASES'); ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($this->rows as $row)
					{
						if ($row->isPublished())
						{
							$showStats = true;
						}
						$this->view('_row')
						     ->set('project', $this->project)
						     ->set('pub', $this->pub)
						     ->set('row', $row)
						     ->set('i', $i)
						     ->display();
						$i++;
					}
					?>
				</tbody>
			</table>
			<?php
			// Pagination
			$pageNav = new \Hubzero\Pagination\Paginator(
				$this->total,
				$this->filters['start'],
				$this->filters['limit']
			);
			$pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
			$pageNav->setAdditionalUrlParam('sortdir', $this->filters['sortdir']);

			$pagenavhtml = $pageNav->render();
			?>
			<fieldset>
				<input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
				<input type="hidden" name="sortdir" value="<?php echo $this->escape($this->filters['sortdir']); ?>" />
				<?php echo $pagenavhtml; ?>
			</fieldset>
		</form>
	</div>
	<?php
}
else
{
	echo ('<p class="noresults">'.Lang::txt('PLG_PROJECTS_PUBLICATIONS_NO_PUBS_FOUND').' <span class="addnew"><a href="' . Route::url($this->project->link('publications') . '&action=start') . '"  >' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION') . '</a></span></p>');

	// Show intro banner with publication steps
	$this->view('intro')
	     ->set('option', $this->option)
	     ->set('project', $this->project)
	     ->set('pub', $this->pub)
	     ->display();
}
?>

<?php if (count($this->rows) > 0 ) { ?>
	<p class="extras">
		<span class="leftfloat">
		<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE'); ?>
		<a href="<?php echo Route::url($this->project->link('publications') . '&action=diskspace'); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_TOOLTIP'); ?>"><span id="indicator-wrapper" <?php if ($warning) { echo 'class="quota-warning"'; } ?>><span id="indicator-area" class="used:<?php echo $inuse; ?>">&nbsp;</span><span id="indicator-value"><span><?php echo $inuse . '% ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_USED'); ?></span></span></span></a>
			 <span class="show-quota"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_QUOTA') . ': ' . \Hubzero\Utility\Number::formatBytes($this->quota); ?></span>
		</span>
	</p>
	<?php if ($showStats) { ?>
		<p class="viewallstats mini"><a href="<?php echo Route::url($this->project->link('publications') . '&action=stats'); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_USAGE_STATS'); ?> &raquo;</a></p>
	<?php } ?>
<?php } ?>