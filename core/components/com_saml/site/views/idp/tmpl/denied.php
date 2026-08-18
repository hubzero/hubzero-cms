<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Document::setTitle(Lang::txt('COM_SAML_DENIED_TITLE'));
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_SAML_DENIED_TITLE'); ?></h2>
</header>

<section class="main section">
	<p class="error">
		<?php echo Lang::txt('COM_SAML_DENIED_MESSAGE', $this->escape($this->sp->get('name'))); ?>
	</p>
	<p>
		<?php echo Lang::txt('COM_SAML_DENIED_HELP'); ?>
	</p>
	<p>
		<a class="btn" href="<?php echo Route::url('index.php?option=com_support'); ?>">
			<?php echo Lang::txt('COM_SAML_DENIED_CONTACT_SUPPORT'); ?>
		</a>
		<a class="btn" href="/logout">
			<?php echo Lang::txt('COM_SAML_DENIED_LOGOUT'); ?>
		</a>
	</p>
</section>
