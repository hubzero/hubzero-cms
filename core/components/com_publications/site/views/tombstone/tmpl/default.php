<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

// no direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>

<div class="content-header with-nav with-details">
    <h2><?php echo Lang::txt('COM_PUBLICATIONS_TOMBSTONE_DATASET_TITLE') . $this->record->title; ?></h2>
</div>
<div class="tombstone-section">
    <h3><?php echo Lang::txt('COM_PUBLICATIONS_TOMBSTONE_DATASET_RETRACTION_DESCRIPTION'); ?></h3>
    <?php
    $doiUrl = Lang::txt('COM_PUBLICATIONS_TOMBSTONE_DOI_ORG') . $this->record->doi;
    $reason = Lang::txt('COM_PUBLICATIONS_TOMBSTONE_REASON')
        . lcfirst($this->record->unpublished_reason) . ".";
    $idLabel = Lang::txt('COM_PUBLICATIONS_TOMBSTONE_DATASET_IDENTIFIER');
    ?>
    <p>
        <?php echo $idLabel; ?>
        <a target="_blank" rel="external" href="<?php echo $doiUrl; ?>"><?php echo $doiUrl; ?></a>
        <?php echo $reason; ?>
    </p>
    <p><?php echo Lang::txt('COM_PUBLICATIONS_TOMBSTONE_CONTACT_PURR'); ?></p>
</div>

