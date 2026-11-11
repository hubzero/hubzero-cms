<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{postUrl: mixed, siteDetails: mixed, transactionDetails: mixed} $this
 */
?>

<?php

echo '<form method="post" action="' . $this->postUrl . '">';
echo '<input type="hidden" value="' . $this->siteDetails->siteId . '" name="UPAY_SITE_ID">';
foreach ($this->transactionDetails as $k => $v) {
    echo '<input type="hidden" value="' . $v . '" name="' . $k . '">';
}

?>

<input type="hidden" name="provider" value="upay">
<input type="submit" value="Pay with UPay" />
</form>