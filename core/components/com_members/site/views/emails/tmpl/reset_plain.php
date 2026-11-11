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
 * @var \Hubzero\Component\View&object{baseUrl: mixed, config: mixed, return: mixed, token: mixed, user: mixed} $this
 */
?>

Hello <?php echo $this->user->name; ?>,

A request has been made to reset your <?php echo $this->config->get('sitename'); ?>
account password. To reset your password, you will need to submit this
verification code in order to verify that the request was legitimate.

The verification code is: <?php echo $this->token; ?>


Click or copy the URL below to enter the verification code and proceed with resetting your password.

<?php echo $this->baseUrl . $this->return; ?>


Thank you!