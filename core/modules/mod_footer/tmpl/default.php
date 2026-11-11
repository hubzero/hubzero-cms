<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

// no direct access
defined('_HZEXEC_') or die;

/**
 * What this template is given.
 *
 * @var \Hubzero\Module\Module $this
 */

/**
 * Set by the code that includes this file.
 *
 * @var mixed $lineone
 * @var mixed $moduleclass_sfx
 */
?>
<footer class="footer<?php echo $moduleclass_sfx; ?>">
    <div class="footer1<?php echo $moduleclass_sfx; ?>"><?php echo $lineone; ?></div>
    <div class="footer2<?php echo $moduleclass_sfx; ?>"><?php echo Lang::txt('MOD_FOOTER_LINE2'); ?></div>
</footer>