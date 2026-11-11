<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{isAuthor: mixed, reviews: mixed, url: mixed, url2: mixed} $this
 */
?>

<p class="review">
    <?php
    $linkText = Lang::txt('PLG_RESOURCES_REVIEWS_NUM_REVIEWS', count($this->reviews));
    ?>
    <a href="<?php echo $this->url; ?>"><?= $linkText ?></a>
    <?php if (!$this->isAuthor) { ?>
        (<a href="<?php echo $this->url2; ?>"><?php echo Lang::txt('PLG_RESOURCES_REVIEWS_REVIEW_THIS'); ?></a>)
    <?php } ?>
</p>