<?php

// phpcs:disable Generic.Files.LineLength

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

?>

<p class="review">
    <a href="<?php echo $this->url; ?>"><?php echo Lang::txt('PLG_RESOURCES_REVIEWS_NUM_REVIEWS', count($this->reviews)); ?></a>
    <?php if (!$this->isAuthor) { ?>
        (<a href="<?php echo $this->url2; ?>"><?php echo Lang::txt('PLG_RESOURCES_REVIEWS_REVIEW_THIS'); ?></a>)
    <?php } ?>
</p>