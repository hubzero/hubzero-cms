<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_DEVELOPER_API_OAUTH_AUTHORIZATION_NEEDED'); ?></h2>
</header>

<section class="main section">
	<div class="section-inner">
		<p><?php echo Lang::txt('COM_DEVELOPER_API_OAUTH_AUTHORIZATION_NEEDED_DESC', $this->application->get('name')); ?></p>
		<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" id="oauth_form" method="post">
			<fieldset class="buttons">
				<button type="submit" name="authorize" value="1" class="btn btn-success"><?php echo Lang::txt('Authorize'); ?></button>
				<button type="submit" name="authorize" value="0" class="btn btn-danger btn-secondary"><?php echo Lang::txt('No Thanks'); ?></button>
			</fieldset>
			<input type="hidden" name="option" value="com_developer" />
			<input type="hidden" name="controller" value="oauth" />
			<input type="hidden" name="task" value="doauthorize" />
			<input type="hidden" name="client_id" value="<?php echo $this->application->get('client_id'); ?>" />
			<input type="hidden" name="response_type" value="<?php echo $this->escape(Request::getWord('response_type', '')); ?>" />
			<input type="hidden" name="redirect_uri" value="<?php echo $this->escape(Request::getString('redirect_uri', '')); ?>" />
			<input type="hidden" name="state" value="<?php echo $this->escape(Request::getCmd('state', '')); ?>" />
		</form>
	</div>
</section>
