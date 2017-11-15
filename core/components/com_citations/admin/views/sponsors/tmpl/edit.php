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

$canDo = \Components\Citations\Helpers\Permissions::getActions('sponsor');

$text = ($this->task == 'edit' ? Lang::txt('EDIT') : Lang::txt('NEW'));

Toolbar::title(Lang::txt('CITATIONS') . ' ' . Lang::txt('CITATION_SPONSORS') . ': ' . $text, 'citation.png');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('sponsor');

$id      = null;
$sponsor = null;
$link    = null;
$image   = null;
if ($this->sponsor)
{
	$id      = $this->sponsor->get('id');
	$sponsor = $this->escape(stripslashes($this->sponsor->get('sponsor')));
	$link    = $this->escape(stripslashes($this->sponsor->get('link')));
	$image   = $this->escape(stripslashes($this->sponsor->get('image')));
}
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	return submitform(pressbutton);
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('CITATION_SPONSORS'); ?></span></legend>

		<div class="input-wrap">
			<label for="field-sponsor"><?php echo Lang::txt('CITATION_SPONSORS_NAME'); ?></label>
			<input type="text" name="sponsor[sponsor]" id="field-sponsor" value="<?php echo $sponsor; ?>" />
		</div>
		<div class="input-wrap">
			<label for="field-link"><?php echo Lang::txt('CITATION_SPONSORS_LINK'); ?></label>
			<input type="text" name="sponsor[link]" id="field-link" value="<?php echo $link; ?>" />
		</div>
		<div class="input-wrap">
			<label for="field-image"><?php echo Lang::txt('CITATION_SPONSORS_IMAGE'); ?></label>
			<input type="text" name="sponsor[image]" id="field-image" value="<?php echo $image; ?>" />
		</div>
	</fieldset>

	<input type="hidden" name="sponsor[id]" value="<?php echo $id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
