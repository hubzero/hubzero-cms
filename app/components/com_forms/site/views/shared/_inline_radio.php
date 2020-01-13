<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$checked = $this->checked;
$name = $this->name;
$text = $this->text;
$value = $this->value;
?>

<span class="inline-radio">
	<input type="radio" name="<?php echo $name; ?>" value="<?php echo $value; ?>"
		<?php if ($checked) echo 'checked'; ?>
	>
	<?php echo $text; ?>
</span>

