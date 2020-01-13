<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$forms = $this->forms;
$prereq = $this->prereq;
$prereqId = $prereq->get('id');
$order = $prereq->get('order');
$scopeId = $prereq->get('prerequisite_id');
$title = $prereq->getParent('name');
?>

<li class="prereq-item" data-id="<?php echo $prereqId; ?>">
	<span class="grid">

		<span class="col span1 offset1">
			<input name="prereqs[<?php echo $prereqId; ?>][id]"
				type="hidden"
				value="<?php echo $prereqId; ?>">
			<input name="prereqs[<?php echo $prereqId; ?>][order]"
				type="number"
				min="1"
				value="<?php echo $order; ?>">
		</span>

		<span class="col span4">
			<?php
				$this->view('_form_select')
					->set('forms', $forms)
					->set('name', "prereqs[$prereqId][prerequisite_id]")
					->set('scopeId', $scopeId)
					->display();
			?>
		</span>

		<span class="col span1 offset5 omega">
			<span class="fontcon destroy-button">&#xf014;</span>
		</span>

	</span>
</li>
