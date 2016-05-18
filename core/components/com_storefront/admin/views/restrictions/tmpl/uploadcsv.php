<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$tmpl = Request::getVar('tmpl', '');

$text = 'Upload a CSV file';

if ($tmpl != 'component')
{
	Toolbar::title(Lang::txt('COM_STOREFRONT').': ' . $text, 'addedit.png');
}

Html::behavior('framework');
?>

<script type="text/javascript">
	function closeAndRefresh(pressbutton)
	{
		window.parent.location='index.php?option=<?php echo $this->option; ?>&controller=<?php echo $this->controller; ?>&id=<?php echo $this->sId; ?>';
	}

	jQuery(document).ready(function($){
		$(window).on('keypress', function(){
			if (window.event.keyCode == 13) {
				submitbutton('uploadcsv');
			}
		})
	});
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="component-form">
<?php if ($tmpl == 'component') { ?>
	<fieldset>
		<div class="configuration" >
			<div class="fltrt configuration-options">
				<button type="button" onclick="closeAndRefresh();"><?php echo Lang::txt( 'Close' );?></button>
			</div>
			<?php echo Lang::txt('Uplaod a file with users') ?>
		</div>
	</fieldset>
<?php } ?>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php }
else {
?>
<div class="col width-100">
	<div class="current">
		<p><?php echo $this->inserted; ?> user<?php echo $this->inserted == 1 ? '' : 's'; ?> inserted.</p>

		<?php
		if (!empty($this->skipped))
		{
		?>
		<p><?php echo count($this->skipped); ?> duplicate user<?php echo count($this->skipped) == 1 ? '' : 's'; ?> skipped.</p>
		<?php
		}
		?>

		<?php
		if (!empty($this->ignored))
		{
			?>
			<p><?php echo count($this->ignored); ?> user<?php echo count($this->ignored) == 1 ? '' : 's'; ?> could not be found and <?php echo count($this->ignored) > 1 ? 'were' : 'was'; ?> ignored:</p>
			<ul>

			<?php
			foreach ($this->ignored as $ignore)
			{
				echo '<li>' . $ignore . '</li>';
			}
			?>

			</ul>
		<?php
		}
		?>
	</div>
</div>
<?php }
?>
</form>