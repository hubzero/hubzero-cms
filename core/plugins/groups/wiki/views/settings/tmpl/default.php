<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$mode      = $this->params->get('automatic_toc', '');
$threshold = $this->params->get('toc_threshold', '');

$defMode   = $this->defaults->get('automatic_toc', 'inline');
$defThresh = (int) $this->defaults->get('toc_threshold', 4);

$modes = array(
	''        => Lang::txt('PLG_GROUPS_WIKI_SETTINGS_TOC_DEFAULT', Lang::txt('PLG_GROUPS_WIKI_SETTINGS_TOC_' . strtoupper($defMode))),
	'inline'  => Lang::txt('PLG_GROUPS_WIKI_SETTINGS_TOC_INLINE'),
	'sidebar' => Lang::txt('PLG_GROUPS_WIKI_SETTINGS_TOC_SIDEBAR'),
	'off'     => Lang::txt('PLG_GROUPS_WIKI_SETTINGS_TOC_OFF'),
);
?>
<div class="container">
	<h3><?php echo Lang::txt('PLG_GROUPS_WIKI_SETTINGS'); ?></h3>

	<form action="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=wiki'); ?>" method="post">
		<fieldset>
			<legend><?php echo Lang::txt('PLG_GROUPS_WIKI_SETTINGS_TOC_LEGEND'); ?></legend>

			<label for="automatic_toc"><?php echo Lang::txt('PLG_GROUPS_WIKI_SETTINGS_AUTOMATIC_TOC'); ?></label>
			<select name="automatic_toc" id="automatic_toc">
				<?php foreach ($modes as $v => $label) { ?>
					<option value="<?php echo $this->escape($v); ?>"<?php if ((string) $mode === (string) $v) { echo ' selected="selected"'; } ?>><?php echo $this->escape($label); ?></option>
				<?php } ?>
			</select>

			<label for="toc_threshold"><?php echo Lang::txt('PLG_GROUPS_WIKI_SETTINGS_THRESHOLD'); ?></label>
			<input type="text" name="toc_threshold" id="toc_threshold" value="<?php echo $this->escape($threshold); ?>" placeholder="<?php echo Lang::txt('PLG_GROUPS_WIKI_SETTINGS_THRESHOLD_DEFAULT', $defThresh); ?>" />
		</fieldset>

		<p class="submit">
			<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_GROUPS_WIKI_SETTINGS_SAVE'); ?>" />
		</p>

		<input type="hidden" name="action" value="savesettings" />
		<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
		<input type="hidden" name="active" value="wiki" />
		<?php echo Html::input('token'); ?>
	</form>
</div>
