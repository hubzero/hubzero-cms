<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$row = $this->row;

// What's the publication status?
$status = $row->getStatusName();
$class  = $row->getStatusCss();
$date   = $row->getStatusDate();

$trClass = $this->i % 2 == 0 ? ' even' : ' odd';

?>
<tr class="mini faded mline<?php echo $trClass; ?>" id="tr_<?php echo $row->get('id'); ?>">
<td class="pub-image"><img src="<?php echo Route::url($row->link('thumb')); ?>" alt="" /></td>
<td><a href="<?php echo Route::url($this->project->link('publications') . '&pid=' . $row->get('id') ); ?>" <?php if ($row->get('abstract')) { echo 'title="' . $this->escape($row->get('abstract')) . '"'; } ?>><?php echo $row->get('title'); ?></a> v.<?php echo $row->get('version_label'); ?></td>
<td><?php echo $row->get('id'); ?></td>
<td class="restype"><?php echo $row->get('base'); ?></td>
<td class="showstatus">
	<span class="<?php echo $class; ?> major_status"><?php echo $status; ?></span>
	<span class="mini faded block"><?php echo $date; ?></span>
</td>
<td>
<?php if ($row->versionProperty('version_label', 'dev') && $row->versionProperty('version_label', 'dev') != $row->get('version_label'))
{ echo '<a href="' . Route::url($this->project->link('publications') . '&pid=' . $row->get('id') . '&version=dev')
. '">&raquo; '. Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEW_VERSION_DRAFT')
. ' <strong>' . $row->versionProperty('version_label', 'dev')  . '</strong></a> '
. Lang::txt('PLG_PROJECTS_PUBLICATIONS_IN_PROGRESS');
	if ($this->project->access('content'))
	{
		echo ' <span class="block"><a href="' . Route::url($this->project->link('publications') . '&pid=' . $row->get('id') . '&action=continue&version=dev') . '" class="btn mini icon-next">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTINUE')  . '</a></span>';
	}
}
elseif ($row->isDev() && $this->project->access('content'))
{
	echo ' <span><a href="' . Route::url($this->project->link('publications') . '&pid=' . $row->get('id') . '&action=continue&version=dev') . '" class="btn mini icon-next">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTINUE')  . '</a></span>';
}
elseif ($row->isWorked()) { echo ' <span><a href="' . Route::url($this->project->link('publications') . '&pid=' . $row->get('id') . '&action=continue&version=' . $row->get('version_number')) . '" class="btn mini icon-next btn-action">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_MAKE_CHANGES')  . '</a></span>'; } ?></td>

<td class="centeralign mini faded"><?php if ($row->versions > 0) { ?><a href="<?php echo Route::url($this->project->link('publications') . '&pid=' . $row->get('id') . '&action=versions'); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_VERSIONS'); ?>"><?php } ?><?php echo $row->get('versions'); ?><?php if ($row->get('versions') > 0) { ?></a><?php } ?></td>
<td class="autowidth">
	<a href="<?php echo Route::url($this->project->link('publications') . '&pid=' . $row->get('id')); ?>" class="manageit" title="<?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_MANAGE_VERSION')); ?>">&nbsp;</a>

	<a href="<?php echo Route::url('index.php?option=com_publications&id=' . $row->get('id') . '&v=' . $row->get('version_number')); ?>" class="public-page" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_PUB_PAGE'); ?>">&nbsp;</a></td>
</tr>