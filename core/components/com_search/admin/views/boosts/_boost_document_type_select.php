<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$boost = $this->boost;
$fieldValue = $boost->getFormattedFieldValue();
$options = $this->typeOptions;
?>

<select name="boost[document_type]">
	<?php
		foreach ($options as $option):
		$selectedText = ($fieldValue == $option) ? 'selected' : '';
	?>
		<option <?php echo $selectedText; ?>>
			<?php echo $option; ?>
		</option>
	<?php	endforeach; ?>
</select>
