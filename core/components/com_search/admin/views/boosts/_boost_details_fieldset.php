<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$boost = $this->boost;
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

				$this->view('_boost_document_type_select')
					->set('boost', $boost)
					->set('typeOptions', $typeOptions)
					->display();
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
