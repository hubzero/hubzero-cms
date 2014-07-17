<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$this->css()
     ->css('vote.css')
     ->js()
     ->js('vote.js');

$name = JText::_('COM_ANSWERS_ANONYMOUS');
if (!$this->question->get('anonymous'))
{
	$user = $this->question->creator();
	if (is_object($user))
	{
		$name = $user->get('name');
	}
	else
	{
		$name = JText::_('COM_ANSWERS_UNKNOWN');
	}
}

?>
<header id="content-header">
	<h2><?php echo JText::_('COM_ANSWERS'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-search search btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=search'); ?>">
				<span><?php echo JText::_('COM_ANSWERS_ALL_QUESTIONS'); ?></span>
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
	<?php if ($this->question->isDeleted() or !$this->question->exists()) { ?>
		<h3><?php echo JText::_('COM_ANSWERS_ERROR_QUESTION_NOT_FOUND'); ?></h3>
		<?php if ($this->note['msg']!='') { ?>
		<p class="help"><?php echo urldecode($this->note['msg']); ?></p>
		<?php } else { ?>
		<p class="error"><?php echo JText::_('COM_ANSWERS_NOTICE_QUESTION_REMOVED'); ?></p>
		<?php } ?>
	</div><!-- / .section-inner -->
</section><!-- / .main section -->

<?php } else { ?>

		<div class="subject">
			<div class="entry question" id="q<?php echo $this->question->get('id'); ?>">
				<p class="entry-member-photo">
					<img src="<?php echo $this->question->creator()->getPicture($this->question->get('anonymous')); ?>" alt="" />
				</p><!-- / .question-member-photo -->
				<div class="entry-content">
				<?php if (!$this->question->isReported()) { ?>
					<p class="entry-voting voting">
						<?php
							$this->view('vote')
								 ->set('option', $this->option)
								 ->set('controller', $this->controller)
								 ->set('question', $this->question)
								 ->set('voted', $this->voted)
								 ->display();
						?>
					</p><!-- / .question-voting -->
				<?php } ?>
	
					<p class="entry-title">
						<strong><?php echo $name; ?></strong>
						<a class="permalink" href="<?php echo JRoute::_($this->question->link()); ?>" title="<?php echo JText::_('COM_ANSWERS_PERMALINK'); ?>">
							<span class="entry-date-at"><?php echo JText::_('COM_ANSWERS_DATETIME_AT'); ?></span>
							<span class="icon-time time"><time datetime="<?php echo $this->question->created(); ?>"><?php echo $this->question->created('time'); ?></time></span>
							<span class="entry-date-on"><?php echo JText::_('COM_ANSWERS_DATETIME_ON'); ?></span>
							<span class="icon-date date"><time datetime="<?php echo $this->question->created(); ?>"><?php echo $this->question->created('date'); ?></time></span>
						</a>
					</p><!-- / .question-title -->
	
			<?php if ($this->question->isReported()) { ?>
					<p class="warning">
						<?php echo JText::_('COM_ANSWERS_NOTICE_QUESTION_REPORTED'); ?>
					</p>
			<?php } else { ?>
					<div class="entry-subject">
						<?php echo $this->question->subject('parsed'); ?>
					</div><!-- / .question-subject -->
	
				<?php if ($this->question->get('question')) { ?>
					<div class="entry-long">
						<?php echo $this->question->content('parsed'); ?>
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
						<a class="icon-abuse abuse" href="<?php echo JRoute::_($this->question->link('report')); ?>" title="<?php echo JText::_('COM_ANSWERS_TITLE_REPORT_ABUSE'); ?>">
							<?php echo JText::_('COM_ANSWERS_REPORT_ABUSE'); ?>
						</a>
					</span>
				<?php if ($this->question->get('created_by') == $this->juser->get('id') && $this->question->isOpen()) { ?>
					<span>
						<a class="icon-delete delete" href="<?php echo JRoute::_($this->question->link('delete')); ?>" title="<?php echo JText::_('COM_ANSWERS_DELETE_QUESTION'); ?>">
							<?php echo JText::_('COM_ANSWERS_DELETE'); ?>
						</a>
					</span>
				<?php } ?>
			<?php } ?>
				</p><!-- / .question-status -->
			</div><!-- / .question -->
	
		<?php if (count($this->notifications) > 0) { ?>
			<div class="subject-wrap">
			<?php foreach ($this->notifications as $notification) { ?>
				<p class="<?php echo $notification['type']; ?>"><?php echo $notification['message']; ?></p>
			<?php } ?>
			</div>
		<?php } ?>
		<!-- end question block -->
	
			<?php if ($this->responding == 4 && $this->question->isOpen() && !$this->question->isReported()) { // delete question ?>
				<section class="below section">
					<div class="subject-wrap">
						<p class="warning"><?php echo JText::_('COM_ANSWERS_NOTICE_CONFIRM_DELETE'); ?></p>
	
						<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=deleteq&id=' . $this->question->get('id')); ?>" method="post" id="deleteForm">
							<input type="hidden" name="qid" value="<?php echo $this->question->get('id'); ?>" />
							<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
							<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
							<input type="hidden" name="task" value="deleteq" />
	
							<p class="submit">
								<input class="btn btn-danger" type="submit" value="<?php echo JText::_('COM_ANSWERS_YES_DELETE'); ?>" />
								<a class="btn btn-secondary" href="<?php echo JRoute::_($this->question->link()); ?>"><?php echo JText::_('COM_ANSWERS_NO_DELETE'); ?></a>
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
						<strong><?php echo JText::_('COM_ANSWERS_STATUS'); ?>:</strong>
					<?php if ($status == 'open') { ?>
						<span class="open"><?php echo JText::_('COM_ANSWERS_STATUS_ACCEPTING_ANSWERS'); ?></span>
					<?php } else if ($status == 'underreview') { ?>
						<span class="underreview"><?php echo JText::_('COM_ANSWERS_STATUS_UNDER_REVIEW'); ?></span>
					<?php } else { ?>
						<span class="closed"><?php echo JText::_('COM_ANSWERS_STATUS_CLOSED'); ?></span></p>
					<?php } ?>
					</p>
					<?php
					$tags = $this->question->tags('array', 1);
					$resource = null;
					foreach ($tags as $tag)
					{
						if (preg_match('/^tool:/i', $tag->get('raw_tag')))
						{
							$resource = 'alias=' . substr($tag->get('raw_tag'), strlen('tool:'));
						}
						else if (preg_match('/^resource:/i', $tag->get('raw_tag')))
						{
							$resource = 'id=' . substr($tag->get('raw_tag'), strlen('resource:'));
						}
						if ($resource)
						{
							?>
							<p><?php echo JText::sprintf('COM_ANSWERS_QUESTION_ASKED_ON', '<a href="' . JRoute::_('index.php?option=com_resources&' . $resource) . '">' . JText::_('COM_ANSWERS_FOLLOWING_RESOURCE') . '</a>'); ?></p>
							<?php
							break;
						}
					}
					?>
	
				<?php if ($this->question->reward() && $this->question->isOpen() && $this->question->config('banking')) { ?>
					<p class="intro">
						<?php echo JText::_('COM_ANSWERS_BONUS'); ?>: <span class="pointvalue"><a href="<?php echo $this->question->config('infolink'); ?>" title="<?php echo JText::_('COM_ANSWERS_WHAT_ARE_POINTS'); ?>"><?php echo JText::_('COM_ANSWERS_WHAT_ARE_POINTS'); ?></a><?php echo JText::sprintf('COM_ANSWERS_NUMBER_POINTS', $this->question->reward()); ?></span>
					</p>
				<?php } ?>
	
				<?php if ($this->question->get('maxaward') && $this->question->isOpen() && $this->question->config('banking')) { ?>
					<p class="youcanearn">
						<?php echo JText::sprintf('COM_ANSWERS_EARN_UP_TO_FOR_BEST_ANSWER', $this->question->get('maxaward')); ?>
					</p>
				<?php } ?>
				</div><!-- / .status_display -->
			</div><!-- / .container -->
		</aside><!-- / .aside -->
	</div><!-- / .section-inner -->
</section>

	<?php if (!$this->question->isReported()) { ?>
		<?php if ($this->responding == 6 && $this->question->isOpen() && $this->question->config('banking')) { // show how points are awarded   ?>
			<section class="below section">
				<div class="section-inner">
					<div class="subject">
						<div class="subject-wrap">
							<table id="pointbreakdown">
								<caption><?php echo JText::_('COM_ANSWERS_POINTS_BREAKDOWN'); ?></caption>
								<thead>
									<tr>
										<th> </th>
										<th scope="col"><?php echo JText::_('COM_ANSWERS_POINTS'); ?></th>
										<th scope="col"><?php echo JText::_('COM_ANSWERS_DETAILS'); ?></th>
									</tr>
								</thead>
								<tfoot>
									<tr>
										<td colspan="3">
											* <?php echo JText::_('COM_ANSWERS_ACTIVITY_POINTS_EXPLANATION'); ?> <a href="<?php echo $this->question->config('infolink'); ?>"><?php echo JText::_('COM_ANSWERS_READ_FURTHER_DETAILS'); ?></a>.
										</td>
									</tr>
								</tfoot>
								<tbody>
									<tr>
										<th scope="row"><?php echo JText::_('COM_ANSWERS_ACTIVITY'); ?>*</th>
										<td><?php echo $this->question->reward('marketvalue'); ?></td>
										<td> </td>
									</tr>
									<tr>
										<th scope="row"><?php echo JText::_('COM_ANSWERS_BONUS'); ?></th>
										<td><?php echo $this->question->reward(); ?></td>
										<td> </td>
									</tr>
									<tr>
										<th scope="row"><?php echo JText::_('COM_ANSWERS_TOTAL_MARKET_VALUE'); ?></th>
										<td><?php echo $this->question->reward('totalmarketvalue') ?></td>
										<td><?php echo JText::_('COM_ANSWERS_TOTAL'); ?></td>
									</tr>
									<tr>
										<th scope="row"><?php echo JText::_('COM_ANSWERS_ASKER_WILL_EARN'); ?></th>
										<td><?php echo $this->question->reward('asker_earnings'); ?></td>
										<td><?php echo JText::_('COM_ANSWERS_ONE_THIRD_OF_ACTIVITY_POINTS'); ?></td>
									</tr>
									<tr>
										<th scope="row"><?php echo JText::_('COM_ANSWERS_ASKER_WILL_PAY'); ?></th>
										<td><?php echo $this->question->reward(); ?></td>
										<td><?php echo JText::_('COM_ANSWERS_REWARD_ASSIGNED_BY_ASKER'); ?></td>
									</tr>
									<tr>
										<th scope="row"><?php echo JText::_('COM_ANSWERS_BEST_ANSWER_MAY_EARN'); ?></th>
										<td><?php echo $this->question->reward('answer_earnings'); ?></td>
										<td><?php echo JText::_('COM_ANSWERS_UP_TO_TWO_THIRDS_OF_ACTIVITY_POINTS'); ?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div><!-- / .subject -->
					<div class="aside">
						<div class="container">
							<p class="info"><?php echo JText::_('COM_ANSWERS_POINT_BREAKDOWN_TBL_SUMMARY'); ?></p>
						</div><!-- / .container -->
					</div><!-- / .aside -->
				</div><!-- / .section-inner -->
			</section><!-- / .below section -->
		<?php } ?>

		<?php if ($this->responding == 1) { // answer form ?>
			<section class="below section">
				<div class="section-inner">
					<div class="subject">
						<h3>
							<?php echo JText::_('COM_ANSWERS_YOUR_ANSWER'); ?>
						</h3>
						<?php if (!$this->juser->get('guest')) { ?>
						<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="post" id="commentform">
							<p class="comment-member-photo">
								<span class="comment-anchor"></span>
								<?php
									$jxuser = \Hubzero\User\Profile::getInstance($this->juser->get('id'));
									if (!$this->juser->get('guest')) {
										$anon = 0;
									} else {
										$anon = 1;
									}
								?>
								<img src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($jxuser, $anon); ?>" alt="<?php echo JText::_('COM_ANSWERS_MEMBER_PICTURE'); ?>" />
							</p>
							<fieldset>
								<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
								<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
								<input type="hidden" name="task" value="savea" />
	
								<?php echo JHTML::_('form.token'); ?>
	
								<input type="hidden" name="response[id]" value="0" />
								<input type="hidden" name="response[question_id]" value="<?php echo $this->question->get('id'); ?>" />
	
								<label for="responseanswer">
									<?php echo JText::_('COM_ANSWERS_YOUR_RESPONSE'); ?>:
									<?php
									echo $this->helpers()->editor('response[answer]', '', 50, 10, 'responseanswer', array('class' => 'minimal'));
									?>
								</label>
	
								<label for="answer-anonymous" id="answer-anonymous-label">
									<input class="option" type="checkbox" name="response[anonymous]" value="1" id="answer-anonymous" />
									<?php echo JText::_('COM_ANSWERS_POST_ANON'); ?>
								</label>
	
								<p class="submit">
									<input type="submit" value="<?php echo JText::_('COM_ANSWERS_SUBMIT'); ?>" />
								</p>
	
								<div class="sidenote">
									<p>
										<strong><?php echo JText::_('COM_ANSWERS_COMMENT_KEEP_RELEVANT'); ?></strong>
									</p>
									<p>
										<?php echo JText::_('COM_ANSWERS_COMMENT_HELP'); ?>
									</p>
								</div>
							</fieldset>
						</form>
						<?php } else { ?>
							<p>
								<?php echo JText::sprintf('COM_ANSWERS_PLEASE_LOGIN_TO_ANSWER', '<a href="' . JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($this->question->link('answer'), false, true))) . '">' . JText::_('COM_ANSWERS_LOGIN') . '</a>'); ?>
							</p>
						<?php } ?>
					</div><!-- / .subject -->
					<div class="aside">
	
					</div><!-- / .aside -->
				</div><!-- / .section-inner -->
			</section><!-- / .below section -->
		<?php } ?>

		<?php if ($this->question->chosen('count')) { ?>
			<!-- list of chosen answers -->
			<section class="below section" id="bestanswer">
				<div class="section-inner">
					<div class="subject">
						<h3>
							<?php echo JText::_('COM_ANSWERS_CHOSEN_ANSWER'); ?>
						</h3>
						<?php
						$this->view('_list')
							 ->set('item_id', 0)
							 ->set('parent', 0)
							 ->set('cls', 'odd')
							 ->set('depth', 0)
							 ->set('option', $this->option)
							 ->set('question', $this->question)
							 ->set('comments', $this->question->chosen('list'))
							 ->set('base', $this->question->link())
							 ->set('config', $this->question->config())
							 ->display();
						?>
					</div><!-- / .subject -->
					<div class="aside">
					</div><!-- / .aside -->
				</div><!-- / .section-inner -->					
			</section><!-- / .below section -->
			<!-- end list of chosen answers -->
		<?php } ?>

		<!-- start comment block -->
		<section class="below section" id="answers">
			<div class="section-inner">
				<div class="subject">
					<h3>
						<span class="comment-count"><?php echo $this->question->comments('count'); ?></span> <?php echo JText::_('COM_ANSWERS_RESPONSES'); ?>
					</h3>
					<?php if ($this->question->comments('count')) { ?>
						<?php
						$this->view('_list')
							 ->set('item_id', 0)
							 ->set('parent', 0)
							 ->set('cls', 'odd')
							 ->set('depth', 0)
							 ->set('option', $this->option)
							 ->set('question', $this->question)
							 ->set('comments', $this->question->comments('list'))
							 ->set('config', $this->question->config())
							 ->set('base', $this->question->link())
							 ->display();
						?>
					<?php } else if ($this->question->chosen('count')) { ?>
						<div class="subject-wrap">
							<p>No other responses made.</p>
						</div>
					<?php } else { ?>
						<div class="subject-wrap">
							<p><?php echo JText::_('COM_ANSWERS_NO_ANSWERS_BE_FIRST'); ?> <a href="<?php echo JRoute::_($this->question->link('answer')); ?>"><?php echo JText::_('COM_ANSWERS_BE_FIRST_ANSWER_THIS'); ?></a>.</p>
						<?php if ($this->question->config('banking')) { ?>
							<p class="help">
								<strong><?php echo JText::_('COM_ANSWERS_DID_YOU_KNOW_ABOUT_POINTS'); ?></strong><br />
								<a href="<?php echo $this->question->config('infolink'); ?>"><?php echo JText::_('COM_ANSWERS_LEARN_MORE'); ?></a> <?php echo JText::_('COM_ANSWERS_LEARN_HOW_POINTS_AWARDED'); ?>.
							</p>
						<?php } ?>
						</div>
					<?php } //if ($this->responses) { ?>
				</div><!-- / .subject -->
				<div class="aside">
					<?php if ($this->question->isOpen() && $this->responding!=1 && !$this->question->isReported() && $this->question->get('created_by') != $this->juser->get('id')) { ?>
						<div class="container">
							<p><a class="icon-add add btn" href="<?php
							$route = JRoute::_($this->question->link('answer'), false, true);
							echo ($this->juser->get('guest') ? JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($route)) : $route);
							?>"><?php echo JText::_('COM_ANSWERS_ANSWER_THIS'); ?></a></p>
						</div><!-- / .container -->
					<?php } ?>
	
					<?php if ($this->juser->get('id') == $this->question->get('created_by') && $this->question->isOpen()) { ?>
						<div class="container">
							<p class="info"><?php echo JText::_('COM_ANSWERS_DO_NOT_FORGET_TO_CLOSE') . ($this->question->config('banking') ? ' ' . JText::_('COM_ANSWERS_DO_NOT_FORGET_TO_CLOSE_POINTS') : ''); ?></p>
						</div><!-- / .container -->
					<?php } ?>
				</div><!-- / .aside -->
			</div><!-- / .section-inner -->
		</section>
		<!-- end comment block -->
	<?php } ?>
<?php } ?>