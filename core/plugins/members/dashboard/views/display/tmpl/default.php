<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// is the dashboard customizable?
$customizable = true;
if ($this->params->get('allow_customization', 1) == 0):
	$customizable = false;
endif;
?>

<h3 class="section-header">
	<?php echo Lang::txt('PLG_MEMBERS_DASHBOARD'); ?>
</h3>

<?php if ($customizable) : ?>
	<ul id="page_options">
		<li>
			<a class="icon-add btn add-module" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=dashboard&action=add'); ?>">
				<?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_ADD_MODULES'); ?>
			</a>
		</li>
	</ul>
<?php endif; ?>

<noscript>
	<p class="warning"><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_NO_JAVASCRIPT'); ?></p>
</noscript>

<div class="modules-container">
	<div class="modules <?php echo ($customizable) ? 'customizable' : ''; ?>" data-userid="<?php echo User::get('id'); ?>" data-token="<?php echo Session::getFormToken(); ?>">
		<?php
			foreach ($this->modules as $module):
				// create view object
				$this->view('module')
				     ->set('admin', $this->admin)
				     ->set('module', $module)
				     ->display();
			endforeach;
		?>
	</div>
</div>

<div class="modules-empty">
	<h3><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_EMPTY_TITLE'); ?></h3>
	<p><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_EMPTY_DESC'); ?></p>
</div>