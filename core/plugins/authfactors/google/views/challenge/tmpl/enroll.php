<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$this->css('enroll')
	->js('enroll');

use Hubzero\Auth\Factor;
use Hubzero\Utility\Validate;
use phpseclib\Crypt\Hash;

// Enrollment is for the signed-in member; the challenge page itself does not
// turn guests away, and the setup script must not run for user '' / 0
if (User::isGuest())
{
	return;
}

// Setup Google Authenticator, call the google_authentictor script
$username = User::get('username');
$user_id = User::get('id');
$cmd = '/usr/share/adm/scripts/google-authenticator-setup.sh ' . escapeshellarg($username) . ' ' . escapeshellarg((string) $user_id);
exec($cmd, $exec_response);

// The script writes the factor row; without it there is nothing to show
$factor = Factor::currentOrFailByDomain('google');
$data   = $factor ? json_decode($factor->data) : null;

if (!$data || empty($data->qrcode))
{
	echo '<p class="error">Google Authenticator enrollment is not available at the moment. Please contact support.</p>';
	return;
}
?>

<form class="gauth2fa" action="<?php echo $this->escape(Request::current()); ?>" method="POST">
	<div class="title">Google Authenticator Setup</div>

	<p class="subtitle"> Please scan with the <a target="_blank" rel="noopener noreferrer" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en_US"> Google Authenticator </a> or the <a target="_blank" rel="noopener noreferrer" href="https://play.google.com/store/apps/details?id=com.duosecurity.duomobile&hl=en_US"> Duo Mobile</a>  App on your device. </p>

	   <p class="qrcode">
	   <a  href="<?php echo $this->escape($data->qrcode); ?>"><img style="border: 0; padding:10px" src="<?php echo $this->escape($data->qrcode); ?>"/></a>
	   </p>

		<input type="hidden" name="action" value="registered" />
		<input type="hidden" name="factor" value="gauth2fa" />
		<?php echo Html::input('token'); ?>
		<div class="grouping">
			<input type="submit" value="Complete 2FA Enrollment" class="btn btn-success" />
			<p class="subtitle">The QR code will no longer be available after Enrollment is completed.</p>
		</div>
</form>
