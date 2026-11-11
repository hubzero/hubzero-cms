<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Route;
use Hubzero\Facades\Html;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{code: mixed, email: mixed, return: mixed, title: mixed} $this
 */

$this
     ->css('register')
     ->js('register');
?>
<header id="content-header">
    <h2><?php echo $this->escape($this->title); ?></h2>
</header>

<section class="main section">
    <?php
    $strongText = $this->escape($this->email);
    ?>
    <p>Confirm that <strong><?= $strongText ?></strong> is your email address to finish setting up your account.</p>

    <form method="post" id="hubForm"
        action="<?= Route::url('index.php?option=com_members&controller=register&task=confirm') ?>">
        <p class="submit">
            <button type="submit" class="btn btn-primary">Confirm my email address</button>
        </p>

        <input type="hidden" name="option" value="com_members" />
        <input type="hidden" name="controller" value="register" />
        <input type="hidden" name="task" value="confirm" />
        <input type="hidden" name="confirm" value="<?php echo $this->escape($this->code); ?>" />
        <input type="hidden" name="return" value="<?php echo $this->escape($this->return); ?>" />
        <?php echo Html::input('token'); ?>
    </form>
</section><!-- / .section -->
