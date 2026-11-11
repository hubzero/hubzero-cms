<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Components\Projects\Helpers\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{
 *     connectionId: mixed,
 *     itemDisplayName: mixed,
 *     itemName: mixed,
 *     model: mixed,
 *     urlEncodedItemPath: mixed,
 * } $this
 */

$directoryBrowseUrl = Route::url(
    $this->model->link('files') . "&action=browse&connection=$this->connectionId&subdir=$this->urlEncodedItemPath",
);
$directoryDisplayName = Html::shortenFileName($this->itemDisplayName, 60);
$directoryTitle = Lang::txt('PLG_PROJECTS_FILES_GO_TO_DIR') . " $this->itemName";
$urlEncodedItemName = urlencode($this->itemName);
?>

<a href="<?= $directoryBrowseUrl ?>" class="dir:<?= $urlEncodedItemName ?>"
    title="<?= $this->escape($directoryTitle) ?>">
    <?php echo str_replace('&amp;#8230;', '&#8230;', $this->escape($directoryDisplayName)); ?>
</a>
