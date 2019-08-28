<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$order = $this->order;
$boost = $this->boost;
$id = $boost->getId();
$type = $boost->getType();
$strength = $boost->getStrength();
?>

<tr>
	<td>
		<input class="record-checkbox"
			type="checkbox" name="boostIds[]"
			id="cb<?php echo $order; ?>"
			value="<?php echo $id; ?>" />
	</td>

	<td>
		<?php echo $id; ?>
	</td>

	<td>
		<?php echo $type; ?>
	</td>

	<td>
		<?php echo $strength; ?>
	</td>
</tr>
