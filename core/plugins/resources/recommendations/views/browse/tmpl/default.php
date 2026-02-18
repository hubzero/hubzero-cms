<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$this->js();
?>
<div id="recommendations">
    <h3><?php echo Lang::txt('PLG_RESOURCES_RECOMMENDATIONS_HEADER'); ?></h3>
    <div class="subject" id="recommendations-subject" data-base="<?php echo Request::base(true); ?>">
        <?php if ($this->results) { ?>
            <ul>
            <?php foreach ($this->results as $line) {
                $param = $line->alias ? 'alias=' . $line->alias : 'id=' . $line->id;
                $url = Route::url(
                    'index.php?option=' . $this->option
                    . '&' . $param . '&rec_ref=' . $this->resource->id
                );
                ?>
                <li>
                    <a href="<?php echo $url; ?>"><?php echo $this->escape(stripslashes($line->title)); ?></a>
                </li>
            <?php } ?>
            </ul>
        <?php } else { ?>
            <p><?php echo Lang::txt('PLG_RESOURCES_RECOMMENDATIONS_NO_RESULTS_FOUND'); ?></p>
        <?php } ?>

        <p id="credits">
            <?php $creditsUrl = Request::base(true) . '/about/hubzero#recommendations'; ?>
            <a href="<?php echo $creditsUrl; ?>">
                <?php echo Lang::txt('PLG_RESOURCES_RECOMMENDATIONS_POWERED_BY'); ?>
            </a>
        </p>
    </div>
    <div class="aside">
        <p><?php echo Lang::txt('PLG_RESOURCES_RECOMMENDATIONS_EXPLANATION'); ?></p>
    </div>
</div>