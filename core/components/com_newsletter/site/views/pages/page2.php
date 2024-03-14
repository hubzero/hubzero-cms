<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('basicForm')
     ->css('page2');

$code = $this->code;
$pageId = $this->pageId;

$user = $this->user;
$campaignId = $this->campaign;

$submitText = Lang::txt('INPUT_SUBMIT');
$breadcrumbs = ['Reply' => ''];

$this->view('_breadcrumbs', 'shared')
	->set('breadcrumbs', $breadcrumbs)
	->set('pageTitle', '')
	->display();
?>

<section class="main section">
	<div class="grid">
		<div class="col span6 offset3">
			<?php $this->view('_page2_instructions')->display(); ?>
		</div>
		<div class="col span6 offset3">
			<form id="hubForm" class="full" method="POST" action="/newsletter/replies/create">
				<textarea name="reply[text]" rows="30"></textarea>

				<?php echo Html::input('token'); ?>
				<input type="hidden" name="code" value="<?php echo $code; ?>">
				<input type="hidden" name="page_id" value="<?php echo $pageId; ?>">

				<input type="hidden" name="user" value="<?php echo $user; ?>">
				<input type="hidden" name="campaign_id" value="<?php echo $campaignId; ?>">

				<input type="submit" class="btn btn-success" value="<?php echo $submitText; ?>">
			</form>
		</div>
	</div>
</section>
