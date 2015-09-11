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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$database = App::get('db');

$this->status['fulltxt'] = stripslashes($this->status['fulltxt']);

$type = new \Components\Resources\Tables\Type($database);
$type->load(7);

$data = array();
preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->status['fulltxt'], $matches, PREG_SET_ORDER);
if (count($matches) > 0)
{
	foreach ($matches as $match)
	{
		$data[$match[1]] = trim($match[2]);
	}
}

$this->status['fulltxt'] = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $this->status['fulltxt']);
$this->status['fulltxt'] = trim($this->status['fulltxt']);

include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'elements.php');

$elements = new \Components\Resources\Models\Elements($data, $type->customFields);
$fields = $elements->render();

?>
	<div class="explaination">
		<p class="help"><?php echo $this->dev ? Lang::txt('COM_TOOLS_SIDE_EDIT_PAGE') : Lang::txt('COM_TOOLS_SIDE_EDIT_PAGE_CURRENT'); ?></p>
		<p><?php echo Lang::txt('COM_TOOLS_COMPOSE_ABSTRACT_HINT'); ?></p>
	</div>
	<fieldset>
		<legend><?php echo Lang::txt('COM_TOOLS_COMPOSE_ABOUT'); ?></legend>
		<label for="field-title">
			<?php echo Lang::txt('COM_TOOLS_COMPOSE_TITLE'); ?>: <span class="required"><?php echo Lang::txt('COM_TOOLS_REQUIRED'); ?></span>
			<?php if ($this->dev) { ?>
				<input type="text" name="title" id="field-title" maxlength="127" value="<?php echo $this->escape(stripslashes($this->status['title'])); ?>" />
			<?php } else { ?>
				<input type="text" name="rtitle" id="field-title" maxlength="127" value="<?php echo $this->escape(stripslashes($this->status['title'])); ?>" disabled="disabled" />
				<input type="hidden" name="title" maxlength="127" value="<?php echo $this->escape(stripslashes($this->status['title'])); ?>" />
				<p class="warning"><?php echo Lang::txt('COM_TOOLS_TITLE_CANT_CHANGE'); ?></p>
			<?php } ?>
		</label>
		<label for="field-description">
			<?php echo Lang::txt('COM_TOOLS_COMPOSE_AT_A_GLANCE'); ?>: <span class="required"><?php echo Lang::txt('COM_TOOLS_REQUIRED'); ?></span>
			<input type="text" name="description" id="field-description" maxlength="256" value="<?php echo $this->escape(stripslashes($this->status['description'])); ?>" />
		</label>
		<label for="field-fulltxt">
			<?php echo Lang::txt('COM_TOOLS_COMPOSE_ABSTRACT'); ?>: <span class="required"><?php echo Lang::txt('COM_TOOLS_REQUIRED'); ?></span>
			<?php echo $this->editor('fulltxt', $this->escape(stripslashes($this->status['fulltxt'])), 50, 20, 'field-fulltxt'); ?>
		</label>

		<fieldset>
			<legend><?php echo Lang::txt('COM_TOOLS_MANAGE_FILES'); ?></legend>
			<div class="field-wrap">
				<iframe width="100%" height="160" name="filer" id="filer" src="<?php echo Request::base(true); ?>/index.php?option=<?php echo $this->option; ?>&amp;controller=media&amp;tmpl=component&amp;resource=<?php echo $this->row->id; ?>"></iframe>
			</div>
		</fieldset>
	</fieldset><div class="clear"></div>

	<div class="explaination">
		<p><?php echo Lang::txt('COM_TOOLS_COMPOSE_CUSTOM_FIELDS_EXPLANATION'); ?></p>
	</div>
	<fieldset>
		<legend><?php echo Lang::txt('COM_TOOLS_COMPOSE_DETAILS'); ?></legend>
		<?php
		echo $fields;
		?>
	</fieldset><div class="clear"></div>