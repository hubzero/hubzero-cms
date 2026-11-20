<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View $this
 */
?>
<form action="<?php echo $this->get('url'); ?>" method="post">
    <input type="hidden" name="paymentProvider" value="offline">
    <input type="submit" class="btn" name="paymentSelect" value="Use offline payment" />
</form>