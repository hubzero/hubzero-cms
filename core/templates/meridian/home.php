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
 * visitor sees below the hero is laid out here rather than in the page's usual
 * gutters. The hero itself, and the navigation sitting over it, are drawn by
 * index.php: they share one ground, and that ground ends here.
 *
 * A band per section, each holding whatever modules the hub put in that
 * position. The heading of a band is the module's own title, so the sections,
 * their order and what they say are all set in the admin rather than here.
 *
 * A hub that has put nothing in any of them gets its front page the way every
 * other page gets one, from the menu item's component, which is also what a
 * hub sees before anyone has built a front page at all.
 */

$bands = array('home-1', 'home-2', 'home-3');

?>
<?php if (!$this->countModules(implode(' or ', $bands))) : ?>
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

<?php foreach ($bands as $band) : ?>
    <?php if ($this->countModules($band)) : ?>
    <div class="home-section <?php echo $band; ?>">
        <div class="inner">
            <jdoc:include type="modules" name="<?php echo $band; ?>" style="xhtml" />
        </div>
    </div>
    <?php endif; ?>
<?php endforeach; ?>
