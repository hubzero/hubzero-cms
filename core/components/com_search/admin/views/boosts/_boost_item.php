<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
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
		<?php echo $type; ?>
	</td>

	<td>
		<?php echo $strength; ?>
	</td>
</tr>
