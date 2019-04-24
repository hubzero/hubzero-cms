<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;
?>
<form action="<?php echo Route::url('index.php?option=com_search'); ?>" method="get" id="searchform<?php echo (self::$instances) > 1 ? $this->module->id : ''; ?>" class="<?php echo $moduleclass_sfx; ?>searchform">
	<fieldset>
		<legend><?php echo $text; ?></legend>

		<?php
			$output  = '<label for="searchword' . (self::$instances > 1 ? $this->module->id : '') . '" class="' . $moduleclass_sfx . 'searchword-label" id="searchword-label' . (self::$instances > 1 ? $this->module->id : '') . '">' . $label . '</label>';
			$output .= '<input type="text" name="terms" class="' . $moduleclass_sfx . 'searchword" id="searchword' . (self::$instances > 1 ? $this->module->id : '') . '" size="' . $width . '" placeholder="' . $text . '" />';

			if ($button):
				$button = '<input type="submit" class="' . $moduleclass_sfx . 'searchsubmit" id="submitquery' . (self::$instances > 1 ? $this->module->id : '') . '" value="' . $button_text . '" />';
			endif;

			switch ($button_pos):
				case 'top':
					$output = $button . '<br />' . $output;
				break;

				case 'bottom':
					$output = $output . '<br />' . $button;
				break;

				case 'right':
					$output = $output . $button;
				break;

				case 'left':
				default:
					$output = $button . $output;
				break;
			endswitch;

			echo $output;
		?>
	</fieldset>
</form>
