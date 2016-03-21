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

$this->row->fulltxt = ($this->row->fulltxt) ? stripslashes($this->row->fulltxt): stripslashes($this->row->introtext);

$type = new \Components\Resources\Tables\Type($this->database);
$type->load($this->row->type);

$data = array();
preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->row->fulltxt, $matches, PREG_SET_ORDER);
if (count($matches) > 0)
{
	foreach ($matches as $match)
	{
		$data[$match[1]] = trim($match[2]);
	}
}

$this->row->fulltxt = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $this->row->fulltxt);
$this->row->fulltxt = trim($this->row->fulltxt);

include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'elements.php');

$elements = new \Components\Resources\Models\Elements($data, $type->customFields);
$fields = $elements->render();

$this->css('create.css')
     ->js('create.js');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=draft'); ?>">
				<?php echo Lang::txt('COM_CONTRIBUTE_NEW_SUBMISSION'); ?>
			</a>
		</p>
	</div><!-- / #content-header -->
</header><!-- / #content-header -->

<section class="main section">
	<?php
		$this->view('steps')
		     ->set('option', $this->option)
		     ->set('step', $this->step)
		     ->set('steps', $this->steps)
		     ->set('id', $this->id)
		     ->set('resource', $this->row)
		     ->set('progress', $this->progress)
		     ->display();
	?>

	<?php if ($this->getError()) { ?>
		<p class="warning"><?php echo implode('<br />', $this->getErrors()); ?></p>
	<?php } ?>

	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=draft&step=' . $this->next_step . '&id=' . $this->id); ?>" method="post" id="hubForm" accept-charset="utf-8">
		<div class="explaination">
			<p><?php echo Lang::txt('COM_CONTRIBUTE_COMPOSE_EXPLANATION'); ?></p>

			<p><?php echo Lang::txt('COM_CONTRIBUTE_COMPOSE_ABSTRACT_HINT'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_CONTRIBUTE_COMPOSE_ABOUT'); ?></legend>

			<label for="field-title">
				<?php echo Lang::txt('COM_CONTRIBUTE_COMPOSE_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
				<input type="text" name="title" id="field-title" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
			</label>

			<label for="field-fulltxt">
				<?php echo Lang::txt('COM_CONTRIBUTE_COMPOSE_ABSTRACT'); ?>:
				<?php echo $this->editor('fulltxt', $this->escape(stripslashes($this->row->fulltxt)), 50, 20, 'field-fulltxt'); ?>
			</label>

			<fieldset>
				<legend><?php echo Lang::txt('COM_CONTRIBUTE_MEDIA_MANAGER'); ?></legend>
				<div class="field-wrap">
					<iframe width="100%" height="160" name="filer" id="filer" src="<?php echo Request::base(true); ?>/index.php?option=<?php echo $this->option; ?>&amp;controller=media&amp;tmpl=component&amp;resource=<?php echo $this->row->id; ?>"></iframe>
				</div>
			</fieldset>
		</fieldset><div class="clear"></div>

		<?php if ($fields) { ?>
			<div class="explaination">
				<p><?php echo Lang::txt('COM_CONTRIBUTE_COMPOSE_CUSTOM_FIELDS_EXPLANATION'); ?></p>
			</div>
			<fieldset>
				<legend><?php echo Lang::txt('COM_CONTRIBUTE_COMPOSE_DETAILS'); ?></legend>
				<?php
				echo $fields;
				?>
			</fieldset><div class="clear"></div>
		<?php } ?>

		<input type="hidden" name="published" value="<?php echo $this->row->published; ?>" />
		<input type="hidden" name="standalone" value="1" />
		<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="type" value="<?php echo $this->row->type; ?>" />
		<input type="hidden" name="created" value="<?php echo $this->row->created; ?>" />
		<input type="hidden" name="created_by" value="<?php echo $this->row->created_by; ?>" />
		<input type="hidden" name="publish_up" value="<?php echo $this->row->publish_up; ?>" />
		<input type="hidden" name="publish_down" value="<?php echo $this->row->publish_down; ?>" />
		<input type="hidden" name="group_owner" value="<?php echo $this->row->group_owner; ?>" />

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
		<input type="hidden" name="step" value="<?php echo $this->next_step; ?>" />

		<?php echo Html::input('token'); ?>

		<p class="submit">
			<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('COM_CONTRIBUTE_NEXT'); ?>" />
		</p>
	</form>
</section><!-- / .main section -->
