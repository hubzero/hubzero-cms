<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{active: mixed, sections: mixed} $this
 */
// No direct access.
defined('_HZEXEC_') or die();

foreach ($this->sections as $section) {
    if (!$section['html']) {
        continue;
    }

    $cls = '';
    if ($section['area'] == $this->active) {
        $cls .= ' active';
    }
    ?>
    <div class="main section<?php echo $cls; ?>" id="<?php echo $section['area']; ?>-section">
        <?php echo $section['html']; ?>
    </div>
    <?php
}
