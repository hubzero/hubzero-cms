<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

// phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{question: mixed, total: mixed} $this
 */
?>
<label for="captcha-answer<?php echo $this->total; ?>">
    <?php echo $this->question['question']; ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
    <input type="text" name="captcha[answer]" id="captcha-answer<?php echo $this->total; ?>" value="" />
</label>
<input type="hidden" name="captcha[instance]" id="captcha-instance" value="<?php echo $this->total; ?>" />