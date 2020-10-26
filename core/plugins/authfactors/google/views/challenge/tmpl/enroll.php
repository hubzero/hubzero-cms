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

?>

<form class="gauth2fa" action="<?php echo Request::current(); ?>" method="POST">
	<div class="title">Google Authenticator Setup</div>

	<p class="subtitle"> Please scan with the <a target="_blank" rel="noopener noreferrer" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en_US"> Google Authenticator </a> or the <a target="_blank" rel="noopener noreferrer" href="https://play.google.com/store/apps/details?id=com.duosecurity.duomobile&hl=en_US"> Duo Mobile</a>  App on your device. </p>

	<?php
	   // Setup Google Authenticator, call the google_authentictor script
	   $username = User::get('username');
	   $user_id = User::get('id');
	   $cmd = '/usr/share/adm/scripts/google-authenticator-setup.sh ' . $username . ' ' . $user_id;
	   exec($cmd, $exec_response);

	   $data = json_decode(Factor::currentOrFailByDomain('google')->data);
	   ?>

	   <p class="qrcode">
	   <a  href="<?php echo $data->qrcode; ?>"><img style="border: 0; padding:10px" src="<?php echo $data->qrcode; ?>"/></a>
	   </p>

		<input type="hidden" name="action" value="registered" />
		<input type="hidden" name="factor" value="gauth2fa" />
		<div class="grouping">
			<input type="submit" value="Complete 2FA Enrollment" class="btn btn-success" />
			<p class="subtitle">The QR code will no longer be available after Enrollment is completed.</p>
		</div>
</form>
