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

$title = Lang::txt('COM_CAREERPLANS');

if (Pathway::count() <= 0)
{
	Pathway::append(
		Lang::txt('COM_CAREERPLANS'),
		'index.php?option=' . $this->option
	);
}

Document::setTitle($title);

$this->css();
?>
<header id="content-header">
	<h2><?php echo $title; ?></h2>
</header>

<section class="main section">
	<div class="section-inner full">
		<div class="subject application-wrap">
			<form method="get" action="<?php echo Route::url('index.php?option=' . $this->option); ?>" id="hubForm" class="full">
				<?php
				if (!$this->plan->isNew())
				{
					$i = 0;
					foreach ($this->fieldsets as $fieldset)
					{
						?>
						<div id="page-<?php echo $fieldset->get('name'); ?>" class="tab">
							<fieldset class="adminform">
								<legend><span><?php echo $fieldset->get('label'); ?></span></legend>

								<?php
								$fields = $fieldset->get('fields', array());
								foreach ($fields as $field)
								{
									$value = $field->renderValue(); //get('value');

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

									echo '<div class="input-wrap" id="input-' . $field->get('name') . '">';
									echo '<h4>' . $field->get('label') . '</h4>';
									echo '<div class="input-value">';
									echo $value;
									echo '</div>';
									echo '</div>';
								}
								?>
								<?php if (!$this->plan->isNew()) { ?>
									<p class="button-wrap">
										<a class="btn" href="<?php echo $fieldset->link(); ?>"><?php echo (count($fields) ? Lang::txt('JACTION_EDIT') : Lang::txt('COM_CAREERPLANS_START')); ?></a>
									</p>
								<?php } ?>
							</fieldset>
						</div>
						<?php
						$i++;
					}
				}
				else
				{
					?>
					<h3>Thank you for your interest in EcologyPlus!</h3>
					<p>EcologyPlus connects diverse college students and early career scientists with timely and relevant career opportunities and a community of peers and professionals in ecology and related careers across all sectors.</p>
					<p>Your responses to these questions will provide EcologyPlus with a rich profile of your interests, accomplishments and background.</p>
					<p class="button-wrap">
						<a class="btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&page=1'); ?>"><?php echo Lang::txt('COM_CAREERPLANS_START'); ?></a>
					</p>
					<?php
				}
				?>
			</form>
		</div>
		<aside class="aside">
			<?php
			$state = 'COM_CAREERPLANS_STATE_DRAFT';
			$cls = 'draft';
			?>
			<div class="application-status <?php echo $cls; ?>">
				<p><strong><?php echo Lang::txt($state); ?></strong></p>
			</div><!-- / .entry-status -->
		</aside>
	</form>
</section><!-- / .main section -->
