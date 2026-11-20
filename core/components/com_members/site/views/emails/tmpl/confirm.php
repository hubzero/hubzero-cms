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
 * @var \Hubzero\Component\View&object{
 *     baseURL: mixed,
 *     confirm: mixed,
 *     email: mixed,
 *     login: mixed,
 *     name: mixed,
 *     registerDate: mixed,
 *     sitename: mixed,
 * } $this
 */

$this->set('baseURL', rtrim($this->baseURL, '/'));

$confirmationCode = -$this->confirm;
$userEmail = urlencode($this->email);
$relativeUrl = "index.php?option=$this->option&task=confirm&confirm=$confirmationCode&email=$userEmail";
$link = $this->baseURL . Route::urlForClient('site', $relativeUrl, false);
$link = str_replace('/administrator', '', $link);
?>

<?php echo Lang::txt('COM_MEMBERS_EMAIL_CREATED') . ": $this->registerDate (UTC)\n"; ?>
<?php echo Lang::txt('COM_MEMBERS_EMAIL_NAME') . ": $this->name\n"; ?>
<?php echo Lang::txt('COM_MEMBERS_EMAIL_USERNAME') . ": $this->login\n"; ?>

<?php echo Lang::txt('COM_MEMBERS_EMAIL_CONFIRM_MESSAGE', $this->sitename) . "\n"; ?>

<?php echo $link . "\n"; ?>

<?php echo Lang::txt('COM_MEMBERS_EMAIL_CONFIRM_DO_NOT_REPLY');
