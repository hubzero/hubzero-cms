<?php
use \Components\Projects\Helpers\Html;

$directoryBrowseUrl = Route::url($this->model->link('files') . "&action=browse&connection=$this->connectionId&subdir=$this->urlEncodedItemPath");
$directoryDisplayName = Html::shortenFileName($this->itemDisplayName, 60);
$directoryTitle = Lang::txt('PLG_PROJECTS_FILES_GO_TO_DIR') . " $this->itemName";
$urlEncodedItemName = urlencode($this->itemName);
?>

<a href="<?php echo $directoryBrowseUrl; ?>" class="dir:<?php echo $urlEncodedItemName; ?>" title="<?php echo $directoryTitle; ?>">
	<?php echo $directoryDisplayName; ?>
</a>
