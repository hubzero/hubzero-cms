<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No Direct Access
defined('_HZEXEC_') or die();

/**
 * The front page
 *
 * index.php hands the whole of main to this file on the front page, so what a
 * visitor sees there is laid out here rather than in the page's usual gutters.
 *
 * A band per section, each holding whatever modules the hub put in that
 * position. The heading of a band is the module's own title, so the sections,
 * their order and what they say are all set in the admin rather than here.
 *
 * A hub that has put nothing in any of them gets its front page the way every
 * other page gets one - from the menu item's component - which is also what a
 * hub sees before anyone has built a front page at all.
 */

$bands = $this->countModules('home-1 or home-2 or home-3 or home-4');

?>
<?php if (!$bands) : ?>
    <div class="inner">
        <section class="main section">
            <div class="section-inner">
                <!-- start component output -->
                <jdoc:include type="component" />
                <!-- end component output -->
            </div>
        </section>
    </div>
<?php endif; ?>

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
