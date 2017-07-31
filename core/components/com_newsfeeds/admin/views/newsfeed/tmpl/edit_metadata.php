<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_newsfeeds
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_HZEXEC_') or die();

$fieldSets = $this->form->getFieldsets('metadata');
foreach ($fieldSets as $name => $fieldSet) :
	echo Html::sliders('panel', Lang::txt($fieldSet->label), $name.'-options');
	if (isset($fieldSet->description) && trim($fieldSet->description)) :
		echo '<p class="tip">'.$this->escape(Lang::txt($fieldSet->description)).'</p>';
	endif;
	?>
	<fieldset class="panelform">
		<ul class="adminformlist">
			<?php if ($name == 'jmetadata') : // Include the real fields in this panel. ?>
				<li>
					<div class="input-wrap">
						<?php echo $this->form->getLabel('metadesc'); ?>
						<?php echo $this->form->getInput('metadesc'); ?>
					</div>
				</li>

				<li>
					<div class="input-wrap">
						<?php echo $this->form->getLabel('metakey'); ?>
						<?php echo $this->form->getInput('metakey'); ?>
					</div>
				</li>

				<li>
					<div class="input-wrap">
						<?php echo $this->form->getLabel('xreference'); ?>
						<?php echo $this->form->getInput('xreference'); ?>
					</div>
				</li>
			<?php endif; ?>
			<?php foreach ($this->form->getFieldset($name) as $field) : ?>
				<li>
					<div class="input-wrap">
						<?php echo $field->label; ?>
						<?php echo $field->input; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</fieldset>
<?php endforeach; ?>
