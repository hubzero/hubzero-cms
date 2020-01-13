<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('formPagesNew');

$page = $this->page;
$pageOrder = $page->get('order');
$pageTitle = $page->get('title');

$fieldsetLegend = Lang::txt('COM_FORMS_FIELDSET_PAGE_INFO');
$orderLabel = Lang::txt('COM_FORMS_FIELDS_ORDER');
$titleLabel = Lang::txt('COM_FORMS_FIELDS_TITLE');
?>

<fieldset>

	<legend>
		<?php echo $fieldsetLegend; ?>
	</legend>

	<div class="grid">
		<div class="col span1">
			<label>
				<?php echo $orderLabel; ?>
				<input name="page[order]" type="number" min="0" value="<?php echo $pageOrder; ?>">
			</label>
		</div>

		<div class="col span11 omega">
			<label>
				<?php echo $titleLabel; ?>
				<input name="page[title]" type="text" value="<?php echo $pageTitle; ?>">
			</label>
		</div>
	</div>

</fieldset>
