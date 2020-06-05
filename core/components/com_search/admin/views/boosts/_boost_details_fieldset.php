<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$boost = $this->boost;
$disableType = isset($this->disableType) ? $this->disableType : false;
$typeOptions = $this->typeOptions;
$typeSelectLabel = Lang::txt('COM_SEARCH_FIELDS_BOOST_TYPE');
?>

<fieldset class="adminform">
	<legend>
		<span><?php echo Lang::txt('JDETAILS'); ?></span>
	</legend>

	<div class="input-wrap">
		<label>
			<?php
				echo "$typeSelectLabel:";
				if ($disableType): ?>
					<input type="text"
						value="<?php echo $boost->getFormattedFieldValue(); ?>"
						disabled>
			<?php	else:
					$this->view('_boost_document_type_select')
						->set('boost', $boost)
						->set('typeOptions', $typeOptions)
						->display();
				endif;
			?>
		</label>
	</div>

	<div class="input-wrap">
		<label>
			<?php echo Lang::txt('COM_SEARCH_FIELDS_BOOST_STRENGTH'); ?>:

			<input name="boost[strength]"
				type="number"
				step="1"
				value="<?php echo $boost->getStrength();?>" />
		</label>
	</div>
</fieldset>
