<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No Direct Access
defined('_HZEXEC_') or die();

/**
 * The front page, when the hub has built one
 *
 * A band per section, each holding whatever modules the hub put in that
 * position. The heading of a band is the module's own title, so the sections,
 * their order and what they say are all set in the admin rather than here.
 *
 * A hub that puts nothing in any of them never reaches this file: index.php
 * renders the front page's component instead, the way every other page does.
 */

?>
<?php if ($this->countModules('home-1')) : ?>
    <div class="home-section home-1">
        <div class="inner">
            <jdoc:include type="modules" name="home-1" style="xhtml" />
        </div>
    </div>
<?php endif; ?>

<?php if ($this->countModules('home-2')) : ?>
    <div class="home-section home-2">
        <div class="inner">
            <jdoc:include type="modules" name="home-2" style="xhtml" />
        </div>
    </div>
<?php endif; ?>

<?php if ($this->countModules('home-3')) : ?>
    <div class="home-section home-3">
        <div class="inner">
            <jdoc:include type="modules" name="home-3" style="xhtml" />
        </div>
    </div>
<?php endif; ?>

<?php if ($this->countModules('home-4')) : ?>
    <div class="home-section home-4">
        <div class="inner">
            <jdoc:include type="modules" name="home-4" style="xhtml" />
        </div>
    </div>
<?php endif; ?>
