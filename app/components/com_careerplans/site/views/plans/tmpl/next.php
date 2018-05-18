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

use Components\Careerplans\Models\Careerplan;
use Components\Careerplans\Models\Field;

// No direct access
defined('_HZEXEC_') or die();

$title = Lang::txt('COM_CAREERPLANS');

if (Pathway::count() <= 0)
{
	Pathway::append(
		Lang::txt('COM_CAREERPLANS'),
		'index.php?option=' . $this->option
	);
}
Pathway::append(
	$this->page->get('label'),
	'index.php?option=' . $this->option . '&page=' . $this->page->get('ordering')
);

Document::setTitle($title . ': ' . $this->page->get('label'));

$errors = $this->getError();
$invalid = array();
$missing = array();
if ($errors && isset($errors['_invalid']))
{
	$invalid = $errors['_invalid'];
}
if ($errors && isset($errors['_missing']))
{
	$missing = $errors['_missing'];
}

$this->css()
     ->js();
?>
<header id="content-header">
	<h2><?php echo $title; ?></h2>
</header>

<section class="main section">
	<form action="<?php echo Route::url('index.php?option=' . $this->option . ($this->fieldsets->last()->get('id') != $this->page->get('id') ? '&page=' . ($this->page->get('ordering') + 1) : '&task=submit')); ?>" method="post" class="section-inner full" id="hubForm">
		<div class="subject application-wrap">
			<?php
			// Convert to XML so we can use the Form processor
			$xml = Field::toXml($this->fields);

			// Gather data to pass to the form processor
			$answers = $this->plan
				->answers()
				->ordered()
				->rows();

			// Gather data to pass to the form processor
			$data = new Hubzero\Config\Registry(
				Careerplan::collect($answers)
			);

			// Create a new form
			Hubzero\Form\Form::addFieldPath(Component::path($this->option) . '/models/fields');

			$form = new Hubzero\Form\Form('application', array('control' => 'questions'));
			$form->load($xml);
			$form->bind($data);

			$scripts = array();
			$toggle = array();

			if ($this->fields->count() > 0): ?>
				<fieldset>
					<legend><?php echo $this->page->get('label'); ?></legend>

					<?php foreach ($this->fields as $field): ?>
						<?php
						$formfield = $form->getField($field->get('name'));

						if ($field->options->count())
						{
							$i = 0;
							$hasEvents = false;
							$opts = array();
							$hide = array();

							foreach ($field->options as $option)
							{
								$opts[] = '#' . $formfield->id . $i;

								$i++;

								if (!$option->get('dependents'))
								{
									continue;
								}

								$events = json_decode($option->get('dependents'));
								$option->set('dependents', $events);

								if (empty($events))
								{
									continue;
								}

								$hide = array_merge($hide, $events);

								$hasEvents = true;
							}

							if ($hasEvents)
							{
								if ($field->get('type') == 'dropdown')
								{
									$scripts[] = '	$("#'. $formfield->id . '").on("change", function(e){';
								}
								else
								{
									$scripts[] = '	$("'. implode(',', $opts) . '").on("change", function(e){';
								}
								$hidden = array();
								foreach ($hide as $h)
								{
									$hidden[] = '#input-' . $h;
								}
								$scripts[] = '		$("' . implode(', ', $hidden) . '").hide();';
							}

							$i = 0;
							foreach ($field->options as $option)
							{
								if (!$option->get('dependents'))
								{
									continue;
								}

								$events = $option->get('dependents');

								if ($field->get('type') == 'dropdown')
								{
									$scripts[] = '		if ($(this).val() == "' . ($option->value ? $option->value : $option->label) . '") {';
									$show = array();
									foreach ($events as $s)
									{
										$show[] = '#input-' . $s;
									}
									//$hide = array_merge($hide, $show);
									$scripts[] = '			$("' . implode(', ', $show) . '").show();';
									//$scripts[] = '		} else {';
									//$scripts[] = '			$("' . implode(', ', $show) . '").hide();';
									$scripts[] = '		}';

									$toggle[] = '	if ($("#profile_' . $field->get('name') . '").val() == "' . ($option->value ? $option->value : $option->label) . '") {';
									$toggle[] = '		$("' . implode(', ', $show) . '").show();';
									//$toggle[] = '	} else {';
									//$toggle[] = '		$("' . implode(', ', $show) . '").hide();';
									$toggle[] = '	}';
								}
								else
								{
									$scripts[] = '		if ($(this).is(":checked") && $(this).val() == "' . ($option->value ? $option->value : $option->label) . '") {';
									$show = array();
									foreach ($events as $s)
									{
										$show[] = '#input-' . $s;
									}
									$hide = array_merge($hide, $show);
									$scripts[] = '			$("' . implode(', ', $show) . '").show();';
									//$scripts[] = '		} else {';
									//$scripts[] = '			$("' . implode(', ', $show) . '").hide();';
									$scripts[] = '		}';

									$toggle[] = '	if ($("#questions_' . $field->get('name') . $i . '").is(":checked") && $("#questions_' . $field->get('name') . $i . '").val() == "' . ($option->value ? $option->value : $option->label) . '") {';
									$toggle[] = '		$("' . implode(', ', $show) . '").show();';
									//$toggle[] = '	} else {';
									//$toggle[] = '		$("' . implode(', ', $show) . '").hide();';
									$toggle[] = '	}';
								}

								$i++;
							}

							if ($hasEvents)
							{
								$scripts[] = '	});';
								$scripts[] = '	$("' . implode(', ', $hide) . '").hide();';
								$scripts[] = implode("\n", $toggle);
							}
						}

						if ($value = $field->get('default_value'))
						{
							$formfield->setValue($value);
						}

						if (isset($this->submission[$field->get('name')]))
						{
							$formfield->setValue($this->submission[$field->get('name')]);
						}

						$errors = (!empty($invalid[$field->get('name')])) ? '<span class="error">' . $invalid[$field->get('name')] . '</span>' : '';
						?>
						<div class="input-wrap<?php echo $errors ? ' fieldWithErrors' : ''; ?>" id="input-<?php echo $field->get('name'); ?>">
							<?php
							if (strtolower($formfield->type) != 'paragraph')
							{
								echo $formfield->label;
							}
							echo $formfield->input;
							if ($formfield->description && strtolower($formfield->type) != 'paragraph')
							{
								echo '<span class="hint">' . $formfield->description . '</span>';
							}
							echo $errors;
							?>
						</div>
					<?php endforeach; ?>
				</fieldset>
			<?php endif;

			if (!empty($scripts))
			{
				$this->js("jQuery(document).ready(function($){\n" . implode("\n", $scripts) . "\n});");
			}
			?>

			<div class="submit grid">
				<div class="col span6">
					<?php
					if ($this->fieldsets->first()->get('id') != $this->page->get('id'))
					{
						?>
						<a class="icon-prev btn btn-secondary" href="<?php echo Route::url('index.php?option=' . $this->option . '&page=' . ($this->page->get('ordering') - 1)); ?>"><?php echo Lang::txt('COM_CAREERPLANS_PREV'); ?></a>
						<?php
					}
					?>
				</div>
				<div class="col span6 omega">
					<?php
					if ($this->fieldsets->last()->get('id') == $this->page->get('id'))
					{
						?>
						<input type="hidden" name="task" value="submit" />
						<input class="btn btn-success" type="submit" name="save" value="<?php echo Lang::txt('COM_CAREERPLANS_SAVE'); ?>" />
						<?php
					}
					else
					{
						?>
						<input type="hidden" name="task" value="next" />
						<input class="btn" type="submit" name="next" value="<?php echo Lang::txt('COM_CAREERPLANS_NEXT'); ?>" />
						<?php
					}
					?>
				</div>
			</div>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="active" value="<?php echo $this->page->get('ordering'); ?>" />
			<input type="hidden" name="page" value="<?php echo ($this->fieldsets->last()->get('id') != $this->page->get('id') ? $this->page->get('ordering') + 1 : 'submit'); ?>" />
			<?php echo Html::input('token'); ?>
		</div>
		<aside class="aside">
			<ol>
			<?php
			foreach ($this->fieldsets as $fieldset)
			{
				$cls = array('application-pages');
				if ($fieldset->get('id') == $this->page->get('id'))
				{
					$cls[] = 'active';
				}
				?>
				<li class="<?php echo implode(' ', $cls); ?>">
					<a href="<?php echo Route::url($fieldset->link()); ?>"><?php echo $fieldset->get('label'); ?></a>
				</li>
				<?php
			}
			?>
			</ol>

			<!-- <div class="submit-wrap">
				<input type="hidden" name="task" value="submit" />
				<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('COM_CAREERPLANS_SUBMIT'); ?>" />
				<p><?php echo Lang::txt('COM_CAREERPLANS_SUBMIT_DESC'); ?></p>
			</div> -->
		</aside>
	</form>
</section><!-- / .main section -->
