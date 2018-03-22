<?php
require_once PATH_CORE . DS . 'components' . DS . 'com_tools' . DS . 'models' . DS . 'orm' . DS . 'handler.php';

use \Components\Tools\Models\Orm\Handler;
use \Components\Projects\Helpers\Html;

$handlerPath = str_replace(
	array('{project}', '{file}'),
	array($this->model->get('alias'), $this->itemPath),
	$this->handlerBase
);
$handlers = Handler::getLaunchUrlsForFile($handlerPath);
$urlEncodedItemName = urlencode($this->itemName);
$itemDropdownClass = ($handlers) ? ' hasMultiple' : '';
$itemFileNameShort = Html::shortenFileName($this->itemFileName, 60);
$itemMimeType = $this->itemMimeType;

if ($itemMimeType && strpos($itemMimeType, "application/vnd.google") === 0)
{
	$formatMappings = [
		'document' => 'document',
		'presentation' => 'presentation',
		'spreadsheet' => 'spreadsheets'
	];
	$match = [];
	preg_match('/\.(\w+\z)/', $itemMimeType, $match);
	$itemFormat = $formatMappings[$match[1]];
	$linkUrl = "https://docs.google.com/$itemFormat/d/$urlEncodedItemName/edit";
	$linkTarget = 'target="blank"';
}
else
{
	$linkUrl = Route::url($this->model->link('files') . "&action=download&connection=$this->connectionId$this->subdirPath&asset=$urlEncodedItemName");
}

?>

<div class="file-action-dropdown<?php echo $itemDropdownClass; ?>">
	<a href="<?php echo $linkUrl; ?>" class="preview file:<?php echo $urlEncodedItemName; ?>" <?php echo $linkTarget; ?>>
		<?php echo $itemFileNameShort; ?>
	</a>
	<?php if ($handlers && count($handlers) > 0) : ?>
		<?php foreach ($handlers as $handler) : ?>
		<a href="<?php echo Route::url($handler['url']); ?>">
			<?php echo $handler['prompt']; ?>
		</a>
		<?php endforeach; ?>
	<?php endif; ?>
</div>

