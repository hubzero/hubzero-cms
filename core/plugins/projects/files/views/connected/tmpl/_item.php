<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use \Components\Projects\Helpers\Html;

$item = $this->item;
$itemIsFile = $item->isFile();
$itemName = $item->getName();
$itemOwner = ($item->getOwner() == User::get('id')) ? Lang::txt('PLG_PROJECTS_FILES_ME') : User::getInstance($item->getOwner())->get('name');
$itemPath = $item->getPath();
$itemTypeInputName = $item->isFile() ? 'asset[]' : 'folder[]';
$itemTypeJs = $item->isDir() ? ' dirr' : '';
$model = $this->model;
$subdirPath = $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';
$urlEncodedItemName = urlencode($itemName);
$urlEncodedItemPath = urlencode($itemPath);
$fileDeleteUrl = Route::url($model->link('files') . "&action=delete$subdirPath&asset=$urlEncodedItemName");
$fileDeleteTip = Lang::txt('PLG_PROJECTS_FILES_DELETE_TOOLTIP');
$fileMoveUrl = Route::url($model->link('files') . "&action=move$subdirPath&asset=$urlEncodedItemName");
$fileMoveTip = Lang::txt('PLG_PROJECTS_FILES_MOVE_TOOLTIP');

try
{
	$itemTimestamp = $item->getTimestamp();
	$itemTimestamp = Html::formatTime(Date::of($itemTimestamp)->toSql());
}
catch (Exception $e)
{
	$itemTimestamp = 'N/A';
}

if ($itemIsFile)
{
	$itemSize = $item->getSize();
	$itemSize = ($itemSize > 0) ? $itemSize : 'N/A';
}
else
{
	$itemSize = '';
}

?>

<tr class="mini faded mline connections">
	<?php if ($model->access('content')) : ?>
		<td class="middle_valign">
			<input type="checkbox" value="<?php echo $urlEncodedItemPath; ?>"
				name="<?php echo $itemTypeInputName; ?>" class="checkasset js<?php echo $itemTypeJs; ?>" />
		</td>
	<?php endif; ?>
	<td class="middle_valign nobsp is-relative">
		<?php
			$this->view('_item_link')
				->set('connectionId', $this->connection->id)
				->set('handlerBase', $this->handlerBase)
				->set('item', $item)
				->set('itemIsFile', $itemIsFile)
				->set('itemName', $itemName)
				->set('itemPath', $itemPath)
				->set('model', $model)
				->set('subdirPath', $subdirPath)
				->set('urlEncodedItemPath', $urlEncodedItemPath)
				->display();
		?>
	</td>
	<td class="shrinked middle_valign"></td>
	<td class="shrinked middle_valign"><?php echo $itemSize; ?></td>
	<td class="shrinked middle_valign"><?php echo $itemTimestamp; ?></td>
	<td class="shrinked middle_valign"><?php echo $itemOwner; ?></td>
	<td class="shrinked middle_valign nojs">
		<?php if ($model->access('content')): ?>
			<a href="<?php echo $fileDeleteUrl; ?>" title="<?php echo $fileDeleteTip; ?>" class="i-delete">&nbsp;</a>
			<a href="<?php echo $fileMoveUrl; ?>" title="<?php echo $fileMoveUrl; ?>" class="i-move">&nbsp;</a>
		<?php endif; ?>
	</td>
</tr>
