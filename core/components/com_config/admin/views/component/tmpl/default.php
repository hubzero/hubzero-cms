<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Load the tooltip behavior.
Html::behavior('tooltip');
Html::behavior('formvalidation');

$this->js();
?>

<style>
/*
 * The component Options screen opens in a popup (tmpl=component, body
 * #component-body.contentpane). Keep the Save/Cancel buttons and the tab
 * headers fixed and scroll only the active tab panel, so the scrollbar starts
 * below them instead of running the full height of the popup -- and every long
 * tab (e.g. Permissions) can be scrolled. All rules are scoped to
 * #component-body.contentpane, so nothing outside this popup is affected.
 */
body#component-body.contentpane { height: 100vh; margin: 0; overflow: hidden; display: flex; flex-direction: column; }
body#component-body.contentpane > #system-message-container { flex: 0 0 auto; }
body#component-body.contentpane > form#component-form { flex: 1 1 auto; min-height: 0; display: flex; flex-direction: column; }
#component-body.contentpane #component-form > fieldset { flex: 0 0 auto; }
#component-body.contentpane #component-form > dl.tabs { flex: 0 0 auto; }
/* overflow-y:auto alone makes overflow-x compute to auto too, which adds a
   spurious horizontal bar on short tabs; pin it to hidden. */
#component-body.contentpane #component-form > .current { flex: 1 1 auto; min-height: 0; overflow-y: auto; overflow-x: hidden; }
/* Radio/checkbox groups emit a screen-reader legend duplicating the field label;
   a template legend style overrides .sr-only and renders it as a stray uppercase
   heading. Restore proper visually-hidden clipping (kept for accessibility). */
#component-body.contentpane #component-form legend.sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
</style>

<script>
// Suppress the admin template's always-on root scrollbar (html{overflow-y:scroll})
// for this popup only. Done here rather than in CSS so it needs no :has() support:
// guarded by the popup body id, it can't affect any other admin screen.
if (document.body && document.body.id === 'component-body') {
	document.documentElement.style.overflowY = 'hidden';
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" id="component-form" method="post" name="adminForm" autocomplete="off" class="form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<fieldset>
		<div class="configuration">
			<div class="configuration-options">
				<button type="button" id="btn-apply"><?php echo Lang::txt('JAPPLY');?></button>
				<button type="button" id="btn-save"><?php echo Lang::txt('JSAVE');?></button>
				<button type="button" id="btn-cancel"<?php echo Request::getBool('refresh', 0) ? ' data-refresh="1"' : ''; ?>><?php echo Lang::txt('JCANCEL');?></button>
			</div>

			<?php echo Lang::txt($this->component->option . '_configuration'); ?>
		</div>
	</fieldset>

	<?php
	echo Html::tabs('start', 'config-tabs-' . $this->component->option . '_configuration', array('useCookie' => 1));

		if ($this->form) :
			$fieldSets = $this->form->getFieldsets();

			foreach ($fieldSets as $name => $fieldSet) :
				$label = empty($fieldSet->label) ? 'COM_CONFIG_'.$name.'_FIELDSET_LABEL' : $fieldSet->label;
				echo Html::tabs('panel', Lang::txt($label), 'publishing-details');
				if (isset($fieldSet->description) && !empty($fieldSet->description)) :
					echo '<p class="tab-description">'.Lang::txt($fieldSet->description).'</p>';
				endif;
				?>
				<ul class="config-option-list">
					<?php foreach ($this->form->getFieldset($name) as $field): ?>
						<li>
							<?php if (!$field->hidden) : ?>
								<?php echo $field->label; ?>
							<?php endif; ?>
							<?php echo $field->input; ?>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="clr"></div>
				<?php
			endforeach;
		else :
			echo '<p class="warning">' . Lang::txt('COM_CONFIG_ERROR_COMPONENT_CONFIG_NOT_FOUND', $this->component->option) . '</p>';
		endif;

	echo Html::tabs('end');
	?>

	<input type="hidden" name="id" value="<?php echo $this->component->id; ?>" />
	<input type="hidden" name="component" value="<?php echo $this->component->option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="path" value="<?php echo $this->model->get('component.path'); ?>" />

	<?php echo Html::input('token'); ?>
</form>
