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

// Sorting and paging
$sortbyDir  = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
$sortAppend = '&sortdir=' . urlencode($sortbyDir);

$whatsleft  = $this->total - $this->filters['start'] - $this->filters['limit'];
$prev_start = $this->filters['start'] - $this->filters['limit'];
$prev_start = $prev_start < 0 ? 0 : $prev_start;
$next_start = $this->filters['start'] + $this->filters['limit'];

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
$approachingQuota = $this->config->get('approachingQuota', 85);
$approachingQuota = intval($approachingQuota) > 0 ? $approachingQuota : 85;
$warning = ($inuse > $approachingQuota) ? 1 : 0;

$showStats = false;

$i = 1;

$url = Route::url($this->project->link('publications'));
?>
<form action="<?php echo Route::url($this->project->link('publications')); ?>" method="post" id="plg-form" >
	<div id="plg-header">
		<h3 class="publications"><?php echo $this->title; ?></h3>
	</div>
	<?php if ($this->project->access('content')) { ?>
	<ul id="page_options" class="pluginOptions">
		<li>
			<a class="icon-add btn" href="<?php echo Route::url($this->project->link('publications') . '&action=start'); ?>">
				<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION'); ?>
			</a>
		</li>
	</ul>
	<?php } ?>
	<div class="container">
		<div class="list-menu">
		<p class="msg-total"><?php echo ucfirst(Lang::txt('COM_PROJECTS_SHOWING')); ?> <?php if ($this->total <= count($this->rows)) { echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_ALL'); }?> <span class="prominent"> <?php echo count($this->rows); ?></span> <?php if ($this->total > count($this->rows)) { echo Lang::txt('COM_PROJECTS_OUT_OF') . ' ' . $this->total; } ?> <?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATIONS_S'); ?></p>
		</div>
	<?php
	if (count($this->rows) > 0 ) {
	?>
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
		foreach ($this->rows as $row) {
				// What's the publication status?
				$status = \Components\Publications\Helpers\Html::getPubStateProperty($row, 'status', 0);
				$class 	= \Components\Publications\Helpers\Html::getPubStateProperty($row, 'class');
				$date 	= \Components\Publications\Helpers\Html::getPubStateProperty($row, 'date');

				// Normalize type title
				$cat_name = \Components\Publications\Helpers\Html::writePubCategory($row->cat_alias, $row->cat_name);

				$abstract = $row->abstract ? stripslashes($row->abstract) : '';

				if ($row->state == 1)
				{
					$showStats = true;
				}

				$trClass = $i % 2 == 0 ? ' even' : ' odd';
				$i++;
			?>
			<tr class="mini faded mline<?php echo $trClass; ?>" id="tr_<?php echo $row->id; ?>">
				<td class="pub-image"><img src="<?php echo Route::url('index.php?option=com_publications&id=' . $row->id . '&v=' . $row->version_id) . '/Image:thumb'; ?>" alt="" /></td>
				<td><a href="<?php echo Route::url($this->project->link('publications') . '&pid=' . $row->id ); ?>" <?php if ($abstract) { echo 'title="' . $abstract . '"'; } ?>><?php echo $row->title; ?></a> v.<?php echo $row->version_label; ?></td>
				<td><?php echo $row->id; ?></td>
				<td class="restype"><?php echo $row->base; ?></td>
				<td class="showstatus">
					<span class="<?php echo $class; ?> major_status"><?php echo $status; ?></span>
					<span class="mini faded block"><?php echo $date; ?></span>
				</td>
				<td>
				<?php if ($row->dev_version_label && $row->dev_version_label != $row->version_label)
				{ echo '<a href="'. Route::url($this->project->link('publications') . '&pid=' . $row->id . '&version=dev')
				. '">&raquo; '. Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEW_VERSION_DRAFT')
				. ' <strong>' . $row->dev_version_label.'</strong></a> '
				. Lang::txt('PLG_PROJECTS_PUBLICATIONS_IN_PROGRESS');
				if ($this->pubconfig->get('curation', 0) && $this->project->access('content'))
				{
					echo ' <span class="block"><a href="' . Route::url($this->project->link('publications') . '&pid=' . $row->id . '&action=continue&version=dev') . '" class="btn mini icon-next">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTINUE')  . '</a></span>';
				}

				} elseif ($row->state == 3 && $this->pubconfig->get('curation', 0) && $this->project->access('content'))
				{
					echo ' <span><a href="' . Route::url($this->project->link('publications') . '&pid=' . $row->id . '&action=continue&version=dev') . '" class="btn mini icon-next">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTINUE')  . '</a></span>';
				} elseif ($row->state == 7) { echo ' <span><a href="' . Route::url($this->project->link('publications') . '&pid=' . $row->id . '&action=continue&version=' . $row->version_number) . '" class="btn mini icon-next btn-action">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_MAKE_CHANGES')  . '</a></span>'; } ?></td>

				<td class="centeralign mini faded"><?php if ($row->versions > 0) { ?><a href="<?php echo Route::url($this->project->link('publications') . '&pid=' . $row->id . '&action=versions'); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_VERSIONS'); ?>"><?php } ?><?php echo $row->versions; ?><?php if ($row->versions > 0) { ?></a><?php } ?></td>
				<td class="autowidth">
					<a href="<?php echo Route::url($this->project->link('publications') . '&pid=' . $row->id); ?>" class="manageit" title="<?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_MANAGE_VERSION')); ?>">&nbsp;</a>

					<a href="<?php echo Route::url('index.php?option=com_publications&id=' . $row->id . '&v=' . $row->version_number); ?>" class="public-page" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_PUB_PAGE'); ?>">&nbsp;</a></td>
			</tr>
			<?php
		}
	?>
			</tbody>
		</table>
		<?php
		// Pagination
		$pageNav = new \JPagination(
			$this->total,
			$this->filters['start'],
			$this->filters['limit']
		);
		$pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
		$pageNav->setAdditionalUrlParam('sortdir', $this->filters['sortdir']);

		$pagenavhtml = $pageNav->getListFooter();
		?>
		<fieldset>
			<?php echo $pagenavhtml; ?>
		</fieldset>
	<?php
	}
	else {
		echo ('<p class="noresults">'.Lang::txt('PLG_PROJECTS_PUBLICATIONS_NO_PUBS_FOUND').' <span class="addnew"><a href="' . $url . '/?action=start"  >' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION') . '</a></span></p>');

			// Show intro banner with publication steps
			$this->view('intro')
			     ->set('option', $this->option)
			     ->set('project', $this->project)
			     ->set('choices', $this->choices)
			     ->set('pubconfig', $this->pubconfig)
			     ->set('goto', '&alias=' . $this->project->alias)
			     ->display();
	} ?>
	</form>
</div>
<?php if (count($this->rows) > 0 ) { ?>
<p class="extras">
	<span class="leftfloat">
	<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE'); ?>
	<a href="<?php echo $url . '/?action=diskspace'; ?>" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_TOOLTIP'); ?>"><span id="indicator-wrapper" <?php if ($warning) { echo 'class="quota-warning"'; } ?>><span id="indicator-area" class="used:<?php echo $inuse; ?>">&nbsp;</span><span id="indicator-value"><span><?php echo $inuse.'% '.Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_USED'); ?></span></span></span></a>
		 <span class="show-quota"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_QUOTA') . ': ' . \Hubzero\Utility\Number::formatBytes($this->quota); ?></span>
	</span>
</p>
<?php if ($showStats) { ?>
<p class="viewallstats mini"><a href="<?php echo $url . '?action=stats'; ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_USAGE_STATS'); ?> &raquo;</a></p>
<?php } ?>
<?php } ?>