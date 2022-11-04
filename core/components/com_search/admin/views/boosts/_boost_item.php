<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$order = $this->order;
$boost = $this->boost;
$id = $boost->getId();
$boostEditUrl = Route::url(
	"index.php?option=$this->option&controller=boosts&task=edit&id=$id"
);
$type = $boost->getFormattedFieldValue();
$strength = $boost->getStrength();
?>

<tr>
	<td>
		<a href="<?php echo $boostEditUrl; ?>">
			<?php echo $id; ?>
		</a>
	</td>

	<td>
		<a href="<?php echo $boostEditUrl; ?>">
			<?php echo $type; ?>
		</a>
	</td>

	<td>
		<a href="<?php echo $boostEditUrl; ?>">
			<?php echo $strength; ?>
		</a>
	</td>
</tr>
