<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{factors: mixed} $this
 */

$this->css('factors.css');
?>

<header id="content-header">
    <h2><?php echo Lang::txt('COM_USERS_FACTOR_VERIFICATION'); ?></h2>
</header>

<section class="main section factors">
    <?php foreach ($this->factors as $factor) : ?>
        <div class="factor-wrap">
            <div class="factor">
                <?php echo $factor->html; ?>
            </div>
        </div>
    <?php endforeach; ?>
</section>