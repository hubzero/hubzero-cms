<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$shareUrl = Route::url(
    'index.php?option=' . $this->option
    . '&id=' . $this->publication->id
    . '&active=share&v=' . $this->publication->version_number
    . '&sharewith=' . strtolower($this->name)
);
$shareTitle = Lang::txt('PLG_PUBLICATION_SHARE_ON', ucfirst($this->name));
$shareClass = 'share_' . strtolower($this->name);
$shareLabel = Lang::txt('PLG_PUBLICATIONS_SHARE_' . strtoupper($this->name));
?>
    <a href="<?php echo $shareUrl; ?>"
        title="<?php echo $shareTitle; ?>"
        class="popup"
        rel="external"
    ><span class="<?php echo $shareClass; ?>"><span><?php echo $shareLabel; ?></span></span></a>