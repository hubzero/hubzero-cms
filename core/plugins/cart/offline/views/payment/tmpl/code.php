<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Route;
use Hubzero\Facades\Html;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{transaction: mixed} $this
 */
?>

<form method="post" action="<?= Route::url('index.php?option=com_cart&controller=order&task=pay') ?>">

    <?php

    $buttonVars = [];
    $buttonVars['custom'] = $this->transaction->token . '-' . $this->transaction->info->tId;

    foreach ($buttonVars as $k => $v) {
        echo '<input type="hidden" value="' . $v . '" name="' . $k . '">';
    }

    ?>

    <input type="hidden" name="provider" value="offline">
    <?= Html::input('token') ?>
    <input type="submit" value="Pay with offline" />
</form>