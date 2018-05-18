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

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Careerplans\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_CAREERPLANS') . ': ' . Lang::txt('COM_CAREERPLANS_SUMMARY'), 'careerplan');
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('entry');

$this->css();
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" class="editform" id="item-form">
	<div class="grid">
		<div class="col span8">

			<div id="member-document">
				<?php
				$i = 0;
				foreach ($this->fieldsets as $fieldset)
				{
					?>
					<div id="page-<?php echo $fieldset->get('name'); ?>" class="tab">
						<fieldset class="adminform">
							<legend><span><?php echo $fieldset->get('label'); ?></span></legend>

							<?php
							$html = array();
							$member = $this->careerplan->creator;
							$base = $member->link() . '&active=careerplan';
							$config = Component::params($this->option);

							$r = Hubzero\Activity\Recipient::all()->getTableName();
							$l = Hubzero\Activity\Log::blank()->getTableName();

							foreach ($fieldset->get('fields', array()) as $field)
							{
								$value = $field->renderValue(); //$field->get('value');

								if ($value)
								{
									// If the type is a block of text, parse for macros
									if ($field->get('type') == 'textarea')
									{
										$value = Html::content('prepare', $value);
									}
									// IF the type is a URL, link it
									if ($field->get('type') == 'url')
									{
										$parsed = parse_url($value);
										if (empty($parsed['scheme']))
										{
											$value = 'http://' . ltrim($value, '/');
										}
										$value = '<a href="' . $value . '" rel="external">' . $value . '</a>';
									}
								}

								if (is_array($value))
								{
									$value = array_unique($value);
									foreach ($value as $k => $v)
									{
										$value[$k] = Components\Careerplans\Helpers\Values::renderIfJson($v);
										if (is_array($value[$k]))
										{
											$value[$k] = implode('<br />', $value[$k]);
										}
									}
									$value = implode('<br />', $value);
								}
								else
								{
									$value = Components\Careerplans\Helpers\Values::renderIfJson($value);
								}

								$comments = Hubzero\Activity\Recipient::all()
									->select($r . '.*')
									->including('log')
									->join($l, $l . '.id', $r . '.log_id')
									->whereEquals($l . '.scope', 'careerplan' . $this->careerplan->get('id'))
									->whereEquals($l . '.scope_id', $field->get('id'))
									->whereEquals($r . '.scope', 'user')
									->whereEquals($r . '.scope_id', $member->get('id'))
									->whereEquals($l . '.parent', 0)
									->whereEquals($r . '.state', Hubzero\Activity\Recipient::STATE_PUBLISHED)
									->ordered()
									->rows();

								$html[] = '<div class="input-wrap" id="input-' . $field->get('name') . '">';
								$html[] = '<h4>' . $field->get('label') . '</h4>';
								$html[] = '<div class="input-value">';
								$html[] = $value;
								$html[] = '</div>';
								
								$html[] = $this->view('_list')
										->set('parent', 0)
										->set('option', 'com_members')
										->set('config', $config)
										->set('comments', $comments)
										->set('depth', 0)
										->set('cls', 'odd')
										->set('base', $base)
										->set('field', $field)
										->set('member', $member)
										->loadTemplate();
								$html[] = '</div>';
							}

							if (!empty($html))
							{
								echo implode("\n", $html);
							}
							else
							{
								echo '<p>' . Lang::txt('COM_CAREERPLANS_NONE') . '</p>';
							}
							?>
						</fieldset>
					</div>
					<?php
					$i++;
				}
				?>
			</div>
		</div>
		<div class="col span4">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_CAREERPLANS_FIELD_CREATED_BY'); ?></th>
						<td>
							<?php echo $this->careerplan->creator->get('name'); ?>
							<input type="hidden" name="fields[created_by]" value="<?php echo $this->careerplan->get('created_by'); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_CAREERPLANS_FIELD_CREATED'); ?></th>
						<td><?php echo $this->careerplan->get('created'); ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_CAREERPLANS_FIELD_MODIFIED_BY'); ?></th>
						<td><?php echo (!$this->careerplan->get('modified_by') ? Lang::txt('COM_CAREERPLANS_NA') : $this->careerplan->modifier->get('name')); ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_CAREERPLANS_FIELD_MODIFIED'); ?></th>
						<td><?php echo (!$this->careerplan->get('modified') ? Lang::txt('COM_CAREERPLANS_NA') : $this->careerplan->get('modified')); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<input type="hidden" name="id" value="<?php echo $this->careerplan->get('id'); ?>" />

	<?php echo Html::input('token'); ?>
</form>