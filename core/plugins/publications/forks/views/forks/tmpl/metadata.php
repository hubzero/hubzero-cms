<?php

// phpcs:disable Generic.Files.LineLength.TooLong

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

?>
<p class="forks">
    <a href="<?php echo Route::url('index.php?option=com_publications&id=' . $this->publication->id . '&active=forks'); ?>">
        <?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_N', $this->total); ?>
    </a>
</p>