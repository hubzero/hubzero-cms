<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$toolComponentPath = Component::path('com_tools');

require_once "$toolComponentPath/models/orm/handler.php";

use \Components\Tools\Models\Orm\Handler;
use \Components\Projects\Helpers\Html;

$model = $this->model;
$handlerPath = str_replace(
	['{project}', '{file}'],
	[$model->get('alias'), $this->itemPath],
	$this->handlerBase
);
$handlers = Handler::getLaunchUrlsForFile($handlerPath);
$urlEncodedItemName = urlencode($this->itemName);
$itemDropdownClass = $handlers ? ' hasMultiple' : '';
$itemFileNameShort = Html::shortenFileName($this->itemFileName, 60);
$itemMimeType = $this->itemMimeType;

$linkUrl = Route::url($model->link('files') . "&action=download&connection=$this->connectionId$this->subdirPath&asset=$urlEncodedItemName");
$linkTarget = '';

if ($itemMimeType && strpos($itemMimeType, 'application/vnd.google') === 0)
{
	// https://developers.google.com/drive/api/v3/mime-types
	$formatMappings = [
		'document'     => 'document',      // Google Docs
		'presentation' => 'presentation',  // Google Slides
		'spreadsheet'  => 'spreadsheets',  // Google Sheets
		//'map'          => 'map',           // Google My Maps
		'form'         => 'forms',          // Google Forms
		//'site'         => 'site',          // Google Sites
		//'script'       => 'script',        // Google Apps Scripts
		//'fusiontable'  => 'fusiontable',   // Google Fusion Tables
	];
	$unlinkable = [
		'drive-sdk'    => 'drive-sdk'      // 3rd party shortcut
	];

	$match = [];

	// application/vnd.google-apps.{format}
	if (preg_match('/^application\/vnd\.google\-apps\.([^.]+)/', $itemMimeType, $match))
	{
		if (isset($formatMappings[$match[1]]))
		{
			$itemFormat = $formatMappings[$match[1]];
			$linkUrl = "https://docs.google.com/$itemFormat/d/$urlEncodedItemName/edit";
			$linkTarget = 'rel="nofollow external" target="_blank"';
		}
		elseif (isset($unlinkable[$match[1]]))
		{
			$itemFormat = '';
			$linkUrl = '';
			$linkTarget = '';

			$itemFileNameShort .= ' (unlinkable 3rd party shortcut)';
		}
	}
}
?>

<div class="file-action-dropdown<?php echo $itemDropdownClass; ?>">
	<?php if ($linkUrl) : ?>
		<a href="<?php echo $linkUrl; ?>" class="preview file:<?php echo $urlEncodedItemName; ?>" <?php echo $linkTarget; ?>>
	<?php endif; ?>
			<?php echo $itemFileNameShort; ?>
	<?php if ($linkUrl) : ?>
		</a>
	<?php endif; ?>
	<?php if ($handlers && count($handlers) > 0) : ?>
		<?php foreach ($handlers as $handler) : ?>
		<a href="<?php echo Route::url($handler['url']); ?>">
			<?php echo $handler['prompt']; ?>
		</a>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
