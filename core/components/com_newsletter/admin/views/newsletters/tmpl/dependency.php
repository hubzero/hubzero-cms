<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View $this
 */

//set title
//Toolbar::title(Lang::txt('COM_NEWSLETTER'), 'newsletter.png');
?>

<p class="warning">
    <?php echo Lang::txt('COM_NEWSLETTER_DEPENDENCY'); ?>
    <br />
    <?php $cronUrl = Route::url('index.php?option=com_cron'); ?>
    <a href="<?php echo $cronUrl; ?>"><?php echo Lang::txt('COM_NEWSLETTER_CHECK_CRON'); ?></a>
</p>
