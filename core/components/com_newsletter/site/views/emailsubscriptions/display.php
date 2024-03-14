<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('emailSubscriptionsDisplay');

$code = $this->code;
$campaign= $this->campaignId;
$submitText = Lang::txt('INPUT_SUBMIT');
$breadcrumbs = ['Email Subscriptions' => ''];
$userId = $this->userId;
$subs = $this->subs;
$hubname = Config::get('sitename');

// Caution: this property may not exist
if (property_exists($this, "userSubs"))
{
	$userSubs = $this->userSubs;
} else {
	$userSubs = NULL;
}


$this->view('_breadcrumbs', 'shared')
	->set('breadcrumbs', $breadcrumbs)
	->set('pageTitle', '')
	->display();
?>

<section class="main section">
	<div class="grid">
		<div class="col span4 offset4">
			<form id="hubForm" class="full" method="POST"
						action="/newsletter/email-subscriptions/update">

				<?php $this->view('_email_subscriptions_selects')
				           ->set('userId', $userId)
				           ->set('userSubs', $userSubs)
				           ->set('subs', $subs)
				           ->display(); ?>

				<?php echo Html::input('token'); ?>
				<input type="hidden" name="code" value="<?php echo $code; ?>">
				<input type="hidden" name="userId" value="<?php echo $userId; ?>">
				<input type="hidden" name="campaign" value="<?php echo $campaign; ?>">
				<input type="submit" class="btn btn-success" value="<?php echo $submitText; ?>">
			</form>
		</div>
	</div>

</section>

<section class="comms-notice">
	<?php echo $hubname;?> is a community-driven, grant-funded project and we continually strive
	to improve our services. Additionally, we need to share the impact of our
	work with our sponsors. We will periodically send you information about
	outages, terms of usage changes, and <?php echo $hubname;?> improvement surveys and
	assessment communications. For more information regarding communications from
	<?php echo $hubname;?>, see our <a href="/legal/privacy">privacy policy</a>.
</section>
