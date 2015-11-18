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

if (Pathway::count() <= 0)
{
	Pathway::append(
		Lang::txt(strtoupper($this->option)),
		'index.php?option=' . $this->option
	);
}
Pathway::append(
	Lang::txt('COM_ANSWERS_NEW'),
	'index.php?option=' . $this->option . '&task=new'
);

Document::setTitle(Lang::txt('COM_ANSWERS') . ': ' . Lang::txt('COM_ANSWERS_NEW'));

$this->css();
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_ANSWERS') . ': ' . Lang::txt('COM_ANSWERS_NEW'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-search search btn" href="<?php echo Route::url('index.php?option=' . $this->option); ?>">
				<span><?php echo Lang::txt('COM_ANSWERS_ALL_QUESTIONS'); ?></span>
			</a>
		</p>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
	<div class="section-inner">
		<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="hubForm">
			<div class="explaination">
				<p><?php echo Lang::txt('COM_ANSWERS_BE_POLITE'); ?></p>
				<?php if ($this->config->get('banking')) { ?>
					<p class="help">
						<strong><?php echo Lang::txt('COM_ANSWERS_WHAT_IS_REWARD'); ?></strong><br />
						<?php echo Lang::txt('COM_ANSWERS_EXPLAINED_MARKET_VALUE'); ?> <a href="<?php echo $this->config->get('infolink'); ?>"><?php echo Lang::txt('COM_ANSWERS_LEARN_MORE'); ?></a> <?php echo Lang::txt('COM_ANSWERS_ABOUT_POINTS'); ?>
					</p>
				<?php } ?>
			</div><!-- / .explaination -->
			<fieldset>
				<legend><?php echo Lang::txt('COM_ANSWERS_YOUR_QUESTION'); ?></legend>

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="saveq" />

				<?php echo Html::input('token'); ?>
				<?php echo Html::input('honeypot'); ?>

				<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->question->get('id', 0)); ?>" />
				<input type="hidden" name="fields[funds]" value="<?php echo $this->escape($this->funds); ?>" />
				<input type="hidden" name="fields[email]" value="1" />
				<input type="hidden" name="fields[state]" value="0" />

				<label for="field-anonymous">
					<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1" />
					<?php echo Lang::txt('COM_ANSWERS_POST_QUESTION_ANON'); ?>
				</label>

				<label>
					<?php echo Lang::txt('COM_ANSWERS_TAGS'); ?>: <span class="required"><?php echo Lang::txt('COM_ANSWERS_REQUIRED'); ?></span><br />
					<?php echo $this->autocompleter('tags', 'tags', $this->escape($this->tag), 'actags'); ?>
				</label>

				<label for="field-subject">
					<?php echo Lang::txt('COM_ANSWERS_ASK_ONE_LINER'); ?>: <span class="required"><?php echo Lang::txt('COM_ANSWERS_REQUIRED'); ?></span><br />
					<input type="text" name="fields[subject]" id="field-subject" value="<?php echo $this->escape($this->question->get('subject', '')); ?>" />
				</label>

				<label for="field-question">
					<?php echo Lang::txt('COM_ANSWERS_ASK_DETAILS'); ?>:<br />
					<?php
						echo $this->editor(
							'fields[question]', // name
							$this->question->get('question'), // content
							35,  // cols
							10,   // rows
							'field-question'   // id
						);
					?>
				</label>

				<?php if ($this->config->get('banking')) { ?>
					<label for="field-reward">
						<?php echo Lang::txt('COM_ANSWERS_ASSIGN_REWARD'); ?>:<br />
						<input type="text" name="fields[reward]" id="field-reward" value="" size="5" <?php if ($this->funds <= 0) { echo 'disabled="disabled" '; } ?>/>
						<?php echo Lang::txt('COM_ANSWERS_YOU_HAVE'); ?> <strong><?php echo $this->escape($this->funds); ?></strong> <?php echo Lang::txt('COM_ANSWERS_POINTS_TO_SPEND'); ?>
					</label>
				<?php } else { ?>
					<input type="hidden" name="fields[reward]" value="0" />
				<?php } ?>

				<?php echo Html::input('honeypot'); ?>
			</fieldset>
			<div class="clear"></div>

			<p class="submit">
				<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('COM_ANSWERS_SUBMIT'); ?>" />

				<a class="btn btn-secondary" href="<?php echo $this->question->get('id') ? Route::url($this->question->link()) : Route::url('index.php?option=' . $this->option); ?>">
					<?php echo Lang::txt('COM_ANSWERS_CANCEL'); ?>
				</a>
			</p>
		</form>
	</div><!-- / .section-inner -->
</section><!-- / .main section -->
