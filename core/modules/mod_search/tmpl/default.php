<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;
?>
<form action="<?php echo Route::url('index.php?option=com_search'); ?>" method="get" id="searchform" <?php if ($moduleclass_sfx) { echo ' class="' . $moduleclass_sfx . '"'; } ?>>
	<fieldset>
		<legend><?php echo $text; ?></legend>

		<?php
			$output  = '<label for="searchword" id="searchword-label">' . $label . '</label>';
			$output .= '<input type="text" name="terms" id="searchword" size="' . $width . '" placeholder="' . $text . '" />';

			if ($button):
				$button = '<input type="submit" id="submitquery" value="' . $button_text . '" />';
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
