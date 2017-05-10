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
	\Hubzero\Utility\String::truncate(strip_tags($this->question->get('subject')), 50),
	$this->question->link()
);

Document::setTitle(Lang::txt('COM_ANSWERS') . ': ' . strip_tags($this->question->get('subject')));

$this->css()
     ->js();
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_ANSWERS'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-search search btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=search'); ?>">
				<span><?php echo Lang::txt('COM_ANSWERS_ALL_QUESTIONS'); ?></span>
			</a>
		</p>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
	<div class="section-inner">
		<?php if ($this->getError()) { ?>
			<p class="warning"><?php echo $this->getError(); ?></p>
		<?php } ?>

	<!-- start question block -->
	<?php if ($this->question->isDeleted() or !$this->question->get('id')) { ?>
		<h3><?php echo Lang::txt('COM_ANSWERS_ERROR_QUESTION_NOT_FOUND'); ?></h3>
		<?php /*if ($this->note['msg']!='') { ?>
		<p class="help"><?php echo urldecode($this->note['msg']); ?></p>
		<?php } else {*/ ?>
		<p class="error"><?php echo Lang::txt('COM_ANSWERS_NOTICE_QUESTION_REMOVED'); ?></p>
		<?php //} ?>
	</div><!-- / .section-inner -->
</section><!-- / .main section -->

<?php } else { ?>

		<div class="subject">
			<div class="entry question" id="q<?php echo $this->question->get('id'); ?>">
				<p class="entry-member-photo">
					<img src="<?php echo $this->question->creator->picture($this->question->get('anonymous')); ?>" alt="" />
				</p><!-- / .question-member-photo -->
				<div class="entry-content">
					<?php if (!$this->question->isReported()) { ?>
						<p class="entry-voting voting">
							<?php
							$this->view('_vote')
							     ->set('option', $this->option)
							     ->set('item', $this->question)
							     ->set('vote', $this->question->ballot())
							     ->display();
							?>
						</p><!-- / .question-voting -->
					<?php } ?>

					<p class="entry-title">
						<strong><?php
						$name = Lang::txt('COM_ANSWERS_ANONYMOUS');
						if (!$this->question->get('anonymous'))
						{
							$name = $this->escape(stripslashes($this->question->creator->get('name', Lang::txt('COM_ANSWERS_UNKNOWN'))));
							if (in_array($this->question->creator->get('access'), User::getAuthorisedViewLevels()))
							{
								$name = '<a href="' . Route::url($this->question->creator->link()) . '">' . $name . '</a>';
							}
						}
						echo $name;
						?></strong>
						<a class="permalink" href="<?php echo Route::url($this->question->link()); ?>" title="<?php echo Lang::txt('COM_ANSWERS_PERMALINK'); ?>">
							<span class="entry-date-at"><?php echo Lang::txt('COM_ANSWERS_DATETIME_AT'); ?></span>
							<span class="icon-time time"><time datetime="<?php echo $this->question->created(); ?>"><?php echo $this->question->created('time'); ?></time></span>
							<span class="entry-date-on"><?php echo Lang::txt('COM_ANSWERS_DATETIME_ON'); ?></span>
							<span class="icon-date date"><time datetime="<?php echo $this->question->created(); ?>"><?php echo $this->question->created('date'); ?></time></span>
						</a>
					</p><!-- / .question-title -->

					<?php if ($this->question->isReported()) { ?>
						<p class="warning">
							<?php echo Lang::txt('COM_ANSWERS_NOTICE_QUESTION_REPORTED'); ?>
						</p>
					<?php } else { ?>
						<div class="entry-subject">
							<?php echo $this->question->subject; ?>
						</div><!-- / .question-subject -->

						<?php if ($this->question->get('question')) { ?>
							<div class="entry-long">
								<?php echo htmlspecialchars_decode($this->question->question); ?>
							</div><!-- / .question-long -->
						<?php } ?>

						<div class="entry-tags">
							<?php echo $this->question->tags('cloud', 0); ?>
						</div><!-- / .question-tags -->
					<?php } ?>
				</div><!-- / .question-content -->

				<p class="entry-status">
					<?php if (!$this->question->isReported()) { ?>
							<span>
								<a class="icon-abuse abuse" href="<?php echo Route::url($this->question->link('report')); ?>" title="<?php echo Lang::txt('COM_ANSWERS_TITLE_REPORT_ABUSE'); ?>">
									<?php echo Lang::txt('COM_ANSWERS_REPORT_ABUSE'); ?>
								</a>
							</span>
						<?php if (($this->question->get('created_by') == User::get('id') && User::authorise('core.delete', $this->option)) || User::authorise('core.manage', $this->option)) { //$this->question->isOpen() ?>
							<span>
								<a class="icon-delete delete" href="<?php echo Route::url($this->question->link('delete')); ?>" title="<?php echo Lang::txt('COM_ANSWERS_DELETE_QUESTION'); ?>">
									<?php echo Lang::txt('COM_ANSWERS_DELETE'); ?>
								</a>
							</span>
						<?php } ?>
					<?php } ?>
				</p><!-- / .question-status -->
			</div><!-- / .question -->

			<?php if ($this->question->isOpen() && $this->question->config('banking') && $this->question->get('reward')) { ?>
				<div class="subject-wrap">
					<p><?php echo Lang::txt('COM_ANSWERS_POINT_BREAKDOWN_TBL_SUMMARY'); ?></p>
					<table id="pointbreakdown">
						<thead>
							<tr>
								<th scope="col"><?php echo Lang::txt('COM_ANSWERS_POINTS_BREAKDOWN'); ?></th>
								<th scope="col"><?php echo Lang::txt('COM_ANSWERS_POINTS'); ?></th>
								<th scope="col"><?php echo Lang::txt('COM_ANSWERS_DETAILS'); ?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="3">
									* <?php echo Lang::txt('COM_ANSWERS_ACTIVITY_POINTS_EXPLANATION'); ?> <a href="<?php echo $this->question->config('infolink'); ?>"><?php echo Lang::txt('COM_ANSWERS_READ_FURTHER_DETAILS'); ?></a>.
								</td>
							</tr>
						</tfoot>
						<tbody>
							<tr>
								<th scope="row"><?php echo Lang::txt('COM_ANSWERS_ACTIVITY'); ?>*</th>
								<td><?php echo $this->question->reward('marketvalue'); ?></td>
								<td> </td>
							</tr>
							<tr>
								<th scope="row"><?php echo Lang::txt('COM_ANSWERS_BONUS'); ?></th>
								<td><?php echo $this->question->reward(); ?></td>
								<td> </td>
							</tr>
							<tr>
								<th scope="row"><?php echo Lang::txt('COM_ANSWERS_TOTAL_MARKET_VALUE'); ?></th>
								<td><?php echo $this->question->reward('totalmarketvalue') ?></td>
								<td><?php echo Lang::txt('COM_ANSWERS_TOTAL'); ?></td>
							</tr>
							<tr>
								<th scope="row"><?php echo Lang::txt('COM_ANSWERS_ASKER_WILL_EARN'); ?></th>
								<td><?php echo $this->question->reward('asker_earnings'); ?></td>
								<td><?php echo Lang::txt('COM_ANSWERS_ONE_THIRD_OF_ACTIVITY_POINTS'); ?></td>
							</tr>
							<tr>
								<th scope="row"><?php echo Lang::txt('COM_ANSWERS_ASKER_WILL_PAY'); ?></th>
								<td><?php echo $this->question->reward(); ?></td>
								<td><?php echo Lang::txt('COM_ANSWERS_REWARD_ASSIGNED_BY_ASKER'); ?></td>
							</tr>
							<tr>
								<th scope="row"><?php echo Lang::txt('COM_ANSWERS_BEST_ANSWER_MAY_EARN'); ?></th>
								<td><?php echo $this->question->reward('answer_earnings'); ?></td>
								<td><?php echo Lang::txt('COM_ANSWERS_UP_TO_TWO_THIRDS_OF_ACTIVITY_POINTS'); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			<?php } ?>

			<?php if ($this->task == 'delete' && $this->question->isOpen() && !$this->question->isReported()) { // delete question ?>
				<section class="below section">
					<div class="subject-wrap">
						<p class="warning"><?php echo Lang::txt('COM_ANSWERS_NOTICE_CONFIRM_DELETE'); ?></p>

						<form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=deleteq&id=' . $this->question->get('id')); ?>" method="post" id="deleteForm">
							<input type="hidden" name="qid" value="<?php echo $this->question->get('id'); ?>" />
							<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
							<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
							<input type="hidden" name="task" value="deleteq" />

							<?php echo Html::input('token'); ?>

							<p class="submit">
								<input class="btn btn-danger" type="submit" value="<?php echo Lang::txt('COM_ANSWERS_YES_DELETE'); ?>" />
								<a class="btn btn-secondary" href="<?php echo Route::url($this->question->link()); ?>"><?php echo Lang::txt('COM_ANSWERS_NO_DELETE'); ?></a>
							</p>
						</form>
					</div><!-- / .subject-wrap -->
				</section><!-- / .below section -->
			<?php } ?>
		</div><!-- / .subject -->
		<aside class="aside">
			<div class="container">
				<div class="status_display">
					<?php
					if ($this->question->isOpen() && !$this->question->isReported()) {
						$status = 'open';
					} else if ($this->question->isReported()) {
						$status = 'underreview';
					} else {
						$status = 'closed';
					}
					?>
					<p class="entry-status <?php echo $status; ?>">
						<strong><?php echo Lang::txt('COM_ANSWERS_STATUS'); ?>:</strong>
					<?php if ($status == 'open') { ?>
						<span class="open"><?php echo Lang::txt('COM_ANSWERS_STATUS_ACCEPTING_ANSWERS'); ?></span>
					<?php } else if ($status == 'underreview') { ?>
						<span class="underreview"><?php echo Lang::txt('COM_ANSWERS_STATUS_UNDER_REVIEW'); ?></span>
					<?php } else { ?>
						<span class="closed"><?php echo Lang::txt('COM_ANSWERS_STATUS_CLOSED'); ?></span>
					<?php } ?>
					</p>
					<?php
					$tags = $this->question->tags('array', 1);
					$resource = null;
					$publication = null;

					foreach ($tags as $tag)
					{
						if (!$tag)
						{
							continue;
						}
						if (!is_object($tag))
						{
							$tag = new \Components\Tags\Models\Tag($tag);
						}
						if (preg_match('/^tool:/i', $tag->get('raw_tag')))
						{
							$resource = 'alias=' . substr($tag->get('raw_tag'), strlen('tool:'));
						}
						else if (preg_match('/^resource(\d+)$/i', $tag->get('tag')))
						{
							$resource = 'id=' . substr($tag->get('tag'), strlen('resource'));
						}

						if ($resource)
						{
							?>
							<p><?php echo Lang::txt('COM_ANSWERS_QUESTION_ASKED_ON', '<a href="' . Route::url('index.php?option=com_resources&' . $resource) . '">' . Lang::txt('COM_ANSWERS_FOLLOWING_RESOURCE') . '</a>'); ?></p>
							<?php
							break;
						}

						if (preg_match('/^publication(\d+)$/i', $tag->get('tag')))
						{
							$publication = 'id=' . substr($tag->get('tag'), strlen('publication'));
						}
						if ($publication)
						{
							?>
							<p><?php echo Lang::txt('COM_ANSWERS_QUESTION_ASKED_ON', '<a href="' . Route::url('index.php?option=com_publications&' . $publication) . '">' . Lang::txt('COM_ANSWERS_FOLLOWING_RESOURCE') . '</a>'); ?></p>
							<?php
							break;
						}
					}
					?>

				<?php if ($this->question->reward() && $this->question->isOpen() && $this->question->config('banking')) { ?>
					<p class="intro">
						<?php echo Lang::txt('COM_ANSWERS_BONUS'); ?>: <span class="pointvalue"><a href="<?php echo $this->question->config('infolink'); ?>" title="<?php echo Lang::txt('COM_ANSWERS_WHAT_ARE_POINTS'); ?>"><?php echo Lang::txt('COM_ANSWERS_WHAT_ARE_POINTS'); ?></a><?php echo Lang::txt('COM_ANSWERS_NUMBER_POINTS', $this->question->reward()); ?></span>
					</p>
				<?php } ?>

				<?php if ($this->question->get('maxaward') && $this->question->isOpen() && $this->question->config('banking')) { ?>
					<p class="youcanearn">
						<?php echo Lang::txt('COM_ANSWERS_EARN_UP_TO_FOR_BEST_ANSWER', $this->question->get('maxaward')); ?>
					</p>
				<?php } ?>
				</div><!-- / .status_display -->
			</div><!-- / .container -->
		</aside><!-- / .aside -->
	</div><!-- / .section-inner -->
</section>

	<?php if (!$this->question->isReported()) { ?>

		<?php if ($this->task == 'answer') { // answer form ?>
			<section class="below section">
				<div class="section-inner">
					<div class="subject">
						<h3>
							<?php echo Lang::txt('COM_ANSWERS_YOUR_ANSWER'); ?>
						</h3>
						<?php if (!User::isGuest()) { ?>
							<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="commentform">
								<p class="comment-member-photo">
									<span class="comment-anchor"></span>
									<?php
									$jxuser = Components\Members\Models\Member::oneOrNew(User::get('id'));
									$anon = (!User::isGuest() ? 0 : 1);
									?>
									<img src="<?php echo $jxuser->picture($anon); ?>" alt="<?php echo Lang::txt('COM_ANSWERS_MEMBER_PICTURE'); ?>" />
								</p>
								<fieldset>
									<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
									<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
									<input type="hidden" name="task" value="savea" />

									<?php echo Html::input('token'); ?>

									<input type="hidden" name="response[id]" value="0" />
									<input type="hidden" name="response[question_id]" value="<?php echo $this->question->get('id'); ?>" />

									<label for="responseanswer">
										<?php echo Lang::txt('COM_ANSWERS_YOUR_RESPONSE'); ?>:
										<?php echo $this->editor('response[answer]', '', 50, 10, 'responseanswer', array('class' => 'minimal')); ?>
									</label>

									<label for="answer-anonymous" id="answer-anonymous-label">
										<input class="option" type="checkbox" name="response[anonymous]" value="1" id="answer-anonymous" />
										<?php echo Lang::txt('COM_ANSWERS_POST_ANON'); ?>
									</label>

									<p class="submit">
										<input type="submit" value="<?php echo Lang::txt('COM_ANSWERS_SUBMIT'); ?>" />
									</p>

									<div class="sidenote">
										<p>
											<strong><?php echo Lang::txt('COM_ANSWERS_COMMENT_KEEP_RELEVANT'); ?></strong>
										</p>
										<p>
											<?php echo Lang::txt('COM_ANSWERS_COMMENT_HELP'); ?>
										</p>
									</div>
								</fieldset>
							</form>
						<?php } else { ?>
							<p>
								<?php echo Lang::txt('COM_ANSWERS_PLEASE_LOGIN_TO_ANSWER', '<a href="' . Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($this->question->link('answer'), false, true))) . '">' . Lang::txt('COM_ANSWERS_LOGIN') . '</a>'); ?>
							</p>
						<?php } ?>
					</div><!-- / .subject -->
					<div class="aside">

					</div><!-- / .aside -->
				</div><!-- / .section-inner -->
			</section><!-- / .below section -->
		<?php } ?>

		<?php
		$chosen = $this->question->chosen()
			->including(['creator', function ($creator){
				$creator->select('*');
			}])
			->rows();

		if ($chosen->count()) { ?>
			<!-- list of chosen answers -->
			<section class="below section" id="bestanswer">
				<div class="section-inner">
					<div class="subject">
						<h3>
							<?php echo Lang::txt('COM_ANSWERS_CHOSEN_ANSWER'); ?>
						</h3>
						<?php
						$this->view('_list')
							 ->set('item_id', 0)
							 ->set('parent', 0)
							 ->set('cls', 'odd')
							 ->set('thread', 'ca')
							 ->set('depth', 0)
							 ->set('option', $this->option)
							 ->set('question', $this->question)
							 ->set('comments', $chosen)
							 ->set('base', $this->question->link())
							 ->set('config', $this->question->config())
							 ->display();
						?>
					</div>
					<div class="aside">
					</div>
				</div>
			</section>
			<!-- end list of chosen answers -->
		<?php } ?>

		<!-- start comment block -->
		<section class="below section" id="answers">
			<div class="section-inner">
				<div class="subject">
					<?php
					$responses = $this->question->responses()
						->including(['creator', function ($creator){
							$creator->select('*');
						}])
						->whereIn('state', array(0, 3))
						->rows();
					?>
					<h3>
						<span class="comment-count"><?php echo $responses->count(); ?></span> <?php echo Lang::txt('COM_ANSWERS_RESPONSES'); ?>
					</h3>
					<?php if ($responses->count()) { ?>
						<?php
						$this->view('_list')
							 ->set('item_id', 0)
							 ->set('parent', 0)
							 ->set('cls', 'odd')
							 ->set('depth', 0)
							 ->set('option', $this->option)
							 ->set('question', $this->question)
							 ->set('comments', $responses)
							 ->set('config', $this->question->config())
							 ->set('base', $this->question->link())
							 ->display();
						?>
					<?php } else if ($this->question->chosen('count')) { ?>
						<div class="subject-wrap">
							<p><?php echo Lang::txt('No other responses made.'); ?></p>
						</div>
					<?php } else { ?>
						<div class="subject-wrap">
							<p><?php echo Lang::txt('COM_ANSWERS_NO_ANSWERS_BE_FIRST'); ?> <a href="<?php echo Route::url($this->question->link('answer')); ?>"><?php echo Lang::txt('COM_ANSWERS_BE_FIRST_ANSWER_THIS'); ?></a>.</p>
						<?php if ($this->question->config('banking')) { ?>
							<p class="help">
								<strong><?php echo Lang::txt('COM_ANSWERS_DID_YOU_KNOW_ABOUT_POINTS'); ?></strong><br />
								<a href="<?php echo $this->question->config('infolink'); ?>"><?php echo Lang::txt('COM_ANSWERS_LEARN_MORE'); ?></a> <?php echo Lang::txt('COM_ANSWERS_LEARN_HOW_POINTS_AWARDED'); ?>.
							</p>
						<?php } ?>
						</div>
					<?php } ?>
				</div>
				<div class="aside">
					<?php if ($this->question->isOpen() && $this->task != 'answer' && !$this->question->isReported()) { ?>
						<div class="container">
							<p><a class="icon-add add btn" href="<?php
							$route = Route::url($this->question->link('answer'), false, true);
							echo (User::isGuest() ? Route::url('index.php?option=com_users&view=login&return=' . base64_encode($route)) : $route);
							?>"><?php echo Lang::txt('COM_ANSWERS_ANSWER_THIS'); ?></a></p>
						</div><!-- / .container -->
					<?php } ?>

					<?php if (User::get('id') == $this->question->get('created_by') && $this->question->isOpen()) { ?>
						<div class="container">
							<p class="info"><?php echo Lang::txt('COM_ANSWERS_DO_NOT_FORGET_TO_CLOSE') . ($this->question->config('banking') ? ' ' . Lang::txt('COM_ANSWERS_DO_NOT_FORGET_TO_CLOSE_POINTS') : ''); ?></p>
						</div><!-- / .container -->
					<?php } ?>
				</div>
			</div>
		</section>
		<!-- end comment block -->
	<?php } ?>
<?php } ?>
