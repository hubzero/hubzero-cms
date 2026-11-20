<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// phpcs:disable Generic.Files.LineLength

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{baseURL: mixed, sitename: mixed, xprofile: mixed} $this
 */

$this->set('baseURL', rtrim($this->baseURL, '/'));

// Carry any registration return URL through the confirmation so the user
// lands back where they started. Stored internal-only; urlencoded for the link.
$return = $this->xprofile->getParam('return', false);

$link = $this->baseURL
    . Route::urlForClient(
        'site',
        'index.php?option=' . $this->option . '&task=confirm&confirm=' . -$this->xprofile->get('activation') . '&email='
            . urlencode($this->xprofile->get('email')) . ($return ? '&return=' . urlencode($return) : ''),
        false, // plain text: '&', not '&amp;' (the link's email came through as 'amp;email')
    );

//$link = $this->baseURL . $link;
$link = str_replace('/administrator', '', $link);
?>

<?php echo Lang::txt('COM_MEMBERS_EMAIL_CREATED'); ?>: <?php echo $this->xprofile->get('registerDate'); ?> (UTC)
<?php echo Lang::txt('COM_MEMBERS_EMAIL_NAME'); ?>: <?php echo $this->xprofile->get('name') . "\n"; ?>
<?php echo Lang::txt('COM_MEMBERS_EMAIL_USERNAME'); ?>: <?php echo $this->xprofile->get('username') . "\n"; ?>

<?php echo Lang::txt('COM_MEMBERS_EMAIL_CONFIRM_MESSAGE', $this->sitename) . "\n"; ?>

<?php echo $link . "\n"; ?>

<?php echo Lang::txt('COM_MEMBERS_EMAIL_CONFIRM_DO_NOT_REPLY');
