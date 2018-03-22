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
$itemDownloadLink = Route::url($this->model->link('files') . "&action=download&connection=$this->connectionId$this->subdirPath&asset=$urlEncodedItemName");
$itemDropdownClass = ($handlers) ? ' hasMultiple' : '';
$itemFileNameShort = Html::shortenFileName($this->itemFileName, 60);
?>

<div class="file-action-dropdown<?php echo $itemDropdownClass; ?>">
	<a href="<?php echo $itemDownloadLink; ?>" class="preview file:<?php echo urlencode($this->itemName); ?>">
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

