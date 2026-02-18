<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

?>
<p class="forks">
    <?php
    $forksUrl = Route::url(
        'index.php?option=com_publications&id='
        . $this->publication->id . '&active=forks'
    );
    ?>
    <a href="<?php echo $forksUrl; ?>">
        <?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_N', $this->total); ?>
    </a>
</p>