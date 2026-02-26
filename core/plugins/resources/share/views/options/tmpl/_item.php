<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$shareUrl = Route::url(
    'index.php?option=' . $this->option
    . '&id=' . $this->resource->id
    . '&active=share&sharewith=' . strtolower($this->_name)
);
$shareTitle = Lang::txt(
    'PLG_RESOURCES_SHARE_ON',
    Lang::txt('PLG_RESOURCES_SHARE_' . strtoupper($this->_name))
);
$shareClass = 'share_' . strtolower($this->_name);
$shareLabel = Lang::txt('PLG_RESOURCES_SHARE_' . strtoupper($this->_name));
?>
    <a href="<?php echo $shareUrl; ?>"
        title="<?php echo $shareTitle; ?>"
        class="popup"
        rel="external"
    ><span class="<?php echo $shareClass; ?>"><span><?php echo $shareLabel; ?></span></span></a>