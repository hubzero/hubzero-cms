<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die('Restricted access');

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{paymentInfo: mixed} $this
 */

?>

<div class="section">

    <h2>Payment info</h2>

    <?php

    echo '<p>' . $this->paymentInfo . '</p>';

    echo '<a href="';
    echo Route::url('index.php?option=com_cart') . 'checkout/payment?update=true';
    echo '">Change</a>';
    ?>

</div>