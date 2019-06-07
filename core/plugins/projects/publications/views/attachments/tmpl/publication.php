<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$data    = $this->data;
$row     = $this->data->row;
$title   = $row->title ? $row->title : $row->path;
$viewer  = $this->data->viewer;

$db = \App::get('db');
$version = new \Components\Publications\Tables\Version($db);
$version->load($row->object_id);

$row->path = Route::url('index.php?option=com_publications&id=' . $version->publication_id . '&v=' . $version->version_number); //$row->object_id);
$details = rtrim(Request::base(), '/') . '/' . ltrim($row->path, '/');
?>
	<li>
		<span class="item-options">
			<?php if ($viewer == 'edit') { ?>
				<span>
					<a href="<?php echo Route::url($data->editUrl . '&action=orderdown&aid=' . $data->id . '&p=' . $data->props); ?>" class="item-movedown" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_MOVEDOWN'); ?>">&darr;</a>
					<a href="<?php echo Route::url($data->editUrl . '&action=orderup&aid=' . $data->id . '&p=' . $data->props); ?>" class="item-moveup" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_MOVEUP'); ?>">&uarr;</a>
					<a href="<?php echo Route::url($data->editUrl . '&action=deleteitem&aid=' . $data->id . '&p=' . $data->props); ?>" class="item-remove" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_REMOVE'); ?>">&nbsp;</a>
				</span>
			<?php } ?>
		</span>
		<span class="item-title link-type">
			<a href="<?php echo $row->path; ?>" rel="external"><?php echo $title; ?></a>
			<span class="item-details"><?php echo $details; ?></span>
		</span>
	</li>