<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// include css
$this->css('introduction', 'system')
     ->css('intro')
     ->css();
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_DEVELOPER'); ?></h2>
</header>

<section class="main section developer-section api">
	<div class="section-inner">
		<div class="grid">
			<div class="col span8">
				<h3><?php echo Lang::txt('COM_DEVELOPER_API_DEVELOPMENT'); ?></h3>
				<?php echo Lang::txt('COM_DEVELOPER_API_DEVELOPMENT_DESC'); ?>
				<a href="<?php echo Route::url('index.php?option=com_developer&controller=api'); ?>" class="btn btn-info icon-go opposite section-link-home">
					<?php echo Lang::txt('COM_DEVELOPER_API_DEVELOPMENT_HOME'); ?>
				</a>
			</div>
			<div class="col span4 omega">
				<div class="icon-api"></div>
			</div>
		</div>
	</div>
</section>

<?php /*
<section class="main section developer-section web">
	<div class="section-inner">
		<div class="grid">
			<div class="col span4">
				<div class="icon-web"></div>
			</div>
			<div class="col span8 omega">
				<h3><?php echo Lang::txt('COM_DEVELOPER_WEB_DEVELOPMENT'); ?></h3>
				<?php echo Lang::txt('COM_DEVELOPER_WEB_DEVELOPMENT_DESC'); ?>
				<a href="<?php echo Route::url('index.php?option=com_developer&controller=web'); ?>" class="btn btn-info icon-go opposite section-link-home">
					<?php echo Lang::txt('COM_DEVELOPER_WEB_DEVELOPMENT_HOME'); ?>
				</a>
			</div>
		</div>
	</div>
</section>
*/ ?>

<section class="main section developer-section tool">
	<div class="section-inner">
		<div class="grid">
			<div class="col span4">
				<div class="icon-tool"></div>
			</div>
			<div class="col span8 omega">
				<h3><?php echo Lang::txt('COM_DEVELOPER_TOOL_DEVELOPMENT'); ?></h3>
				<?php echo Lang::txt('COM_DEVELOPER_TOOL_DEVELOPMENT_DESC'); ?>
				<a href="<?php echo Route::url('index.php?option=com_tools'); ?>" class="btn btn-info icon-go opposite section-link-home">
					<?php echo Lang::txt('COM_DEVELOPER_TOOL_DEVELOPMENT_HOME'); ?>
				</a>
			</div>
		</div>
	</div>
</section>