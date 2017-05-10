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

$this->css()
     ->css('vote.css', 'com_answers')
     ->css('jquery.ui.css', 'system')
     ->js();

	$error = $this->getError();

	// What name should we dispay for the submitter?
	$user = $this->wish->proposer;

	$name = Lang::txt('COM_WISHLIST_ANONYMOUS');
	if (!$this->wish->get('anonymous'))
	{
		$name = $this->escape(stripslashes($this->wish->proposer->get('name', $name)));
		if (in_array($this->wish->proposer->get('access'), User::getAuthorisedViewLevels()))
		{
			$name = '<a href="' . Route::url($this->wish->proposer->link()) . '">' . $name . '</a>';
		}
	}

	// && ($this->wish->get('admin')==2 or $this->wish->get('admin')==1)
	$assigned = ($this->wish->get('assigned')) ? Lang::txt('COM_WISHLIST_WISH_ASSIGNED_TO', '<a href="'.Route::url('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->get('category').'&rid='.$this->wishlist->get('referenceid') . '&wishid='.$this->wish->get('id')).'?filterby='.$this->filters['filterby'].'&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag'].'&action=editplan#plan">'.$this->wish->owner('name').'</a>') : '';

	if (!$assigned && ($this->wish->get('admin')==2 or $this->wish->get('admin')==1) && $this->wish->get('status')==0)
	{
		$assigned = '<a href="' . Route::url('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->get('category').'&rid='.$this->wishlist->get('referenceid') . '&wishid='.$this->wish->get('id')).'?filterby='.$this->filters['filterby'].'&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag'].'&action=editplan#plan">'.Lang::txt('unassigned').'</a>';
	}

	$this->wish->set('status', ($this->wish->get('accepted')==1 && $this->wish->get('status')==0 ? 6 : $this->wish->get('status')));
	$due  = ($this->wish->get('due') !='0000-00-00 00:00:00') ? Date::of($this->wish->get('due'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')) : '';

	$this->wish->set('positive', $this->wish->votes()->whereEquals('helpful', 'yes')->total());
	$this->wish->set('negative', $this->wish->votes()->whereEquals('helpful', 'no')->total());
?>
	<header id="content-header">
		<h2><?php echo $this->title . ': ' . Lang::txt('COM_WISHLIST_WISH') . ' #' . $this->wish->get('id'); ?></h2>

		<div id="content-header-extra">
			<ul id="useroptions">
				<li>
				<?php /*if ($prv = $this->wish->neighbor('prev')) { ?>
					<a class="icon-prev prev btn" href="<?php echo Route::url($this->wishlist->link('permalink', array_merge($this->filters, array('wishid' => $prv)))); ?>">
						<span><?php echo Lang::txt('COM_WISHLIST_PREV'); ?></span>
					</a>
				<?php } else { ?>
					<span class="icon-prev prev btn">
						<span><?php echo Lang::txt('COM_WISHLIST_PREV'); ?></span>
					</span>
				<?php } ?>
				</li>
				<li>
					<a class="all btn" href="<?php echo Route::url($this->wishlist->link('permalink', $this->filters)); ?>">
						<span><?php echo Lang::txt('COM_WISHLIST_All'); ?></span>
					</a>
				</li>
				<li class="last">
				<?php if ($nxt = $this->wish->neighbor('next')) { ?>
					<a class="icon-next next opposite btn" href="<?php echo Route::url($this->wishlist->link('permalink', array_merge($this->filters, array('wishid' => $nxt)))); ?>">
						<span><?php echo Lang::txt('COM_WISHLIST_NEXT'); ?></span>
					</a>
				<?php } else { ?>
					<span class="icon-next next opposite btn">
						<span><?php echo Lang::txt('COM_WISHLIST_NEXT'); ?></span>
					</span>
				<?php }*/ ?>
				</li>
			</ul>
		</div><!-- / #content-header-extra -->
	</header><!-- / #content-header -->

	<?php if (!$this->getError()) { ?>
		<?php if ($this->wish->get('saved')==3) { ?>
			<p class="passed">
				<?php echo Lang::txt('COM_WISHLIST_NOTICE_WISH_CREATED'); ?>
			</p>
		<?php } ?>

		<?php if ($this->wish->get('saved')==2 && $this->wishlist->access('manage')) { ?>
			<p class="passed">
				<?php echo Lang::txt('COM_WISHLIST_NOTICE_WISH_CHANGES_SAVED'); ?>
			</p>
		<?php } ?>
	<?php } ?>

	<section class="main section">
		<div class="subject">
		<?php if ($this->wish->isReported()) { ?>
			<p class="warning"><?php echo Lang::txt('COM_WISHLIST_NOTICE_POSTING_REPORTED'); ?></p>
		</div><!-- / .subject -->
		<div class="aside">
		</div>
	</section><!-- / .main section -->
		<?php } else if ($this->wish->isDeleted() || (!$this->wish->get('admin') && $this->wish->isWithdrawn())) { ?>
			<p class="warning"><?php echo Lang::txt('COM_WISHLIST_NOTICE_WISH_WITHDRAWN'); ?></p>
		</div><!-- / .subject -->
		<div class="aside">
		</div>
	</section><!-- / .main section -->
		<?php } else { ?>
			<div class="entry wish" id="w<?php echo $this->wish->get('id'); ?>">
				<p class="entry-member-photo">
					<img src="<?php echo $this->wish->proposer->picture(); ?>" alt="<?php echo Lang::txt('COM_WISHLIST_MEMBER_PICTURE'); ?>" />
				</p><!-- / .wish-member-photo -->

				<div class="entry-content">
					<p class="entry-voting voting" id="wish_<?php echo $this->wish->get('id'); ?>">
						<?php
						$this->view('_vote')
						     ->set('option', $this->option)
						     ->set('item', $this->wish)
						     ->set('listid', $this->wishlist->get('id'))
						     ->set('plugin', 0)
						     ->set('admin', $this->wish->get('admin'))
						     ->set('page', 'wish')
						     ->set('filters', $this->filters)
						     ->display();
						?>
					</p><!-- / .wish-voting -->

					<p class="entry-title">
						<strong><?php echo $name; ?></strong>
						<a class="permalink" href="<?php echo Route::url($this->wish->link()); ?>" rel="bookmark" title="<?php echo Lang::txt('COM_WISHLIST_PERMALINK'); ?>">
							<span class="entry-date-at"><?php echo Lang::txt('COM_WISHLIST_AT'); ?></span>
							<span class="time"><time datetime="<?php echo $this->wish->proposed(); ?>"><?php echo $this->wish->proposed('time'); ?></time></span>
							<span class="entry-date-on"><?php echo Lang::txt('COM_WISHLIST_ON'); ?></span>
							<span class="date"><time datetime="<?php echo $this->wish->proposed(); ?>"><?php echo $this->wish->proposed('date'); ?></time></span>
						</a>
					</p><!-- / .wish-title -->

					<div class="entry-subject">
						<p><?php echo $this->escape(stripslashes($this->wish->get('subject'))); ?></p>
					</div><!-- / .wish-subject -->

					<?php if ($content = $this->wish->content) { ?>
						<div class="entry-long">
							<?php echo $content; ?>
						</div><!-- / .wish-details -->
					<?php } ?>

					<div class="entry-tags">
						<p>Tags:</p>
						<?php if ($tags = $this->wish->tags('string')) { ?>
							<?php echo $tags; ?>
						<?php } else { ?>
							<?php echo Lang::txt('COM_WISHLIST_NONE'); ?>
						<?php } ?>
					</div><!-- / .wish-tags -->
				</div><!-- / .wish-content -->

				<?php
					if ($this->wishlist->access('manage'))
					{
						$owners = $this->wishlist->getOwners();

						$eligible = array_merge($owners['individuals'], $owners['advisory']);
						$eligible = array_unique($eligible);

						$voters = ($this->wish->votes()->total() <= count($eligible)) ? count($eligible) : $this->wish->votes()->total();
						//$html .= "\t\t\t".'<div class="wishpriority">'.Lang::txt('PRIORITY').': '.$this->wish->ranking.' <span>('.$this->wish->num_votes.' '.Lang::txt('NOTICE_OUT_OF').' '.$voters.' '.Lang::txt('VOTES').')</span>';
						$html = '';
						if ($this->wish->due() != '0000-00-00 00:00:00' && !$this->wish->isGranted())
						{
							$html .= ($this->wish->get('due') <= Date::of('now')->toSql())
									? '<span class="overdue"><a href="'.Route::url($this->wish->link('editplan')).'">'.Lang::txt('COM_WISHLIST_OVERDUE')
									: '<span class="due"><a href="'.Route::url($this->wish->link('editplan')).'">'.Lang::txt('COM_WISHLIST_WISH_DUE_IN').' '.\Components\Wishlist\Helpers\Html::nicetime($this->wish->get('due'));
							$html .= '</a></span>';
						}
						//$html .= '</div>'."\n";
						echo $html;
					}
				?>
				<ul class="wish-options">
					<?php if ($this->wishlist->access('admin') && $this->wishlist->get('admin') != 3) { ?>
						<?php if (!$this->wish->isGranted()) { ?>
							<li>
								<a class="changestatus" href="<?php echo Route::url($this->wish->link('changestatus')); ?>">
									<?php echo Lang::txt('COM_WISHLIST_ACTION_CHANGE_STATUS'); ?>
								</a>
							</li>
						<?php } ?>
							<li>
								<a class="transfer" href="<?php echo Route::url($this->wish->link('move')); ?>">
									<?php echo Lang::txt('COM_WISHLIST_MOVE'); ?>
								</a>
							</li>
						<?php if ($this->wish->isPrivate()) { ?>
							<li>
								<a class="makepublic" href="<?php echo Route::url($this->wish->link('privacy', array('private' => '0'))); ?>">
									<?php echo Lang::txt('COM_WISHLIST_MAKE_PUBLIC'); ?>
								</a>
							</li>
						<?php } else { ?>
							<li>
								<a class="makeprivate" href="<?php echo Route::url($this->wish->link('privacy', array('private' => '1'))); ?>">
									<?php echo Lang::txt('COM_WISHLIST_MAKE_PRIVATE'); ?>
								</a>
							</li>
						<?php } ?>
					<?php } ?>
					<?php if (($this->wishlist->access('manage') && $this->wishlist->get('admin') != 3) || User::get('id') == $this->wish->get('proposed_by')) { ?>
						<li>
							<a class="edit" href="<?php echo Route::url($this->wish->link('edit')); ?>">
								<?php echo ucfirst(Lang::txt('COM_WISHLIST_ACTION_EDIT')); ?>
							</a>
						</li>
					<?php } ?>
						<li>
							<a class="abuse" data-txt-flagged="<?php echo Lang::txt('COM_WISHLIST_COMMENT_REPORTED_AS_ABUSIVE'); ?>" href="<?php echo Route::url($this->wish->link('report')); ?>">
								<?php echo Lang::txt('COM_WISHLIST_REPORT_ABUSE'); ?>
							</a>
						</li>
					<?php if (User::get('id') == $this->wish->get('proposed_by') && $this->wish->isOpen()) { ?>
						<li>
							<a class="delete" href="<?php echo Route::url($this->wish->link('withdraw')); ?>">
								<?php echo Lang::txt('COM_WISHLIST_ACTION_WITHDRAW_WISH'); ?>
							</a>
						</li>
					<?php } ?>
				</ul>

			<?php if ($this->wishlist->access('manage') && !$this->wish->isDeleted() && !$this->wish->isWithdrawn() && $voters > 0) { ?>
				<div class="container">
					<form method="post" action="<?php echo Route::url('index.php?option=' . $this->option); ?>" class="rankingform" id="rankForm">
						<table class="wish-priority" id="priority">
							<caption>
								<?php echo Lang::txt('COM_WISHLIST_PRIORITY'); ?>: <strong><?php echo $this->wish->get('ranking'); ?></strong>
								<span>(<?php echo $this->wish->rankings()->total('count') . ' '.Lang::txt('COM_WISHLIST_NOTICE_OUT_OF').' '.$voters.' '.Lang::txt('COM_WISHLIST_VOTES'); ?>)</span>
							</caption>
							<thead>
								<tr>
									<th></th>
									<?php if ($this->wishlist->access('manage')) { // My opinion is available for list owners/advisory committee only ?>
										<th><?php echo Lang::txt('COM_WISHLIST_MY_OPINION'); ?></th>
									<?php } ?>
									<th><?php echo Lang::txt('COM_WISHLIST_CONSENSUS'); ?></th>
									<th><?php echo Lang::txt('COM_WISHLIST_COMMUNITY_VOTE'); ?></th>
								</tr>
							</thead>
						<?php if ($this->wishlist->access('manage')) { // My opinion is available for list owners/advisory committee only ?>
							<tfoot>
								<tr>
									<td></td>
									<td>
										<input type="hidden" name="task" value="savevote" />
										<input type="hidden" name="category" value="<?php echo $this->escape($this->wishlist->get('category')); ?>" />
										<input type="hidden" name="rid" value="<?php echo $this->escape($this->wishlist->get('referenceid')); ?>" />
										<input type="hidden" name="wishid" value="<?php echo $this->escape($this->wish->get('id')); ?>" />

										<?php echo Html::input('token'); ?>

										<input type="submit" value="<?php echo Lang::txt('COM_WISHLIST_SAVE'); ?>" />
									</td>
									<td></td>
									<td></td>
								</tr>
							</tfoot>
						<?php } ?>
							<tbody>
								<tr>
									<th><?php echo Lang::txt('COM_WISHLIST_IMPORTANCE'); ?></th>
								<?php
								// My opinion is available for list owners/advisory committee only
								if ($this->wishlist->access('manage'))
								{
									$importance = array(
										''    => Lang::txt('COM_WISHLIST_SELECT_IMP'),
										'0.0' => '0 -' . Lang::txt('COM_WISHLIST_RUBBISH'),
										'1'   => '1 - ' . Lang::txt('COM_WISHLIST_MAYBE'),
										'2'   => '2 - ' . Lang::txt('COM_WISHLIST_INTERESTING'),
										'3'   => '3 - ' . Lang::txt('COM_WISHLIST_GOODIDEA'),
										'4'   => '4 - ' . Lang::txt('COM_WISHLIST_IMPORTANT'),
										'5'   => '5 - ' . Lang::txt('COM_WISHLIST_CRITICAL')
									);
									?>
									<td>
										<?php echo \Components\Wishlist\Helpers\Html::formSelect('importance', $importance, $this->wish->ranking('importance'), 'rankchoices'); ?>
									</td>
									<?php
								}
								if ($this->wish->rankings()->total() == 0)
								{
								?>
									<td><?php echo Lang::txt('COM_WISHLIST_NA'); ?></td>
								<?php
								}
								else
								{
									?>
									<td><?php echo \Components\Wishlist\Helpers\Html::convertVote($this->wish->get('average_imp', $this->wish->ranking('importance')), 'importance'); ?></td>
									<?php
								}
								?>
									<td class="voting">
										<?php
										$this->view('_vote')
										     ->set('option', $this->option)
										     ->set('item', $this->wish)
										     ->set('listid', $this->wishlist->get('id'))
										     ->set('plugin', 0)
										     ->set('admin', $this->wish->get('admin'))
										     ->set('page', 'wish')
										     ->set('filters', $this->filters)
										     ->display();
										?>
									</td>
								</tr>
								<tr>
									<th><?php echo Lang::txt('COM_WISHLIST_EFFORT'); ?></th>
								<?php
								// My opinion is available for list owners/advisory committee only
								if ($this->wishlist->access('manage'))
								{
									$effort = array(
										''    => Lang::txt('COM_WISHLIST_SELECT_EFFORT'),
										'5'   => Lang::txt('COM_WISHLIST_FOURHOURS'),
										'4'   => Lang::txt('COM_WISHLIST_ONEDAY'),
										'3'   => Lang::txt('COM_WISHLIST_TWODAYS'),
										'2'   => Lang::txt('COM_WISHLIST_ONEWEEK'),
										'1'   => Lang::txt('COM_WISHLIST_TWOWEEKS'),
										'0.0' => Lang::txt('COM_WISHLIST_TWOMONTHS'),
										'6'   => Lang::txt('COM_WISHLIST_DONT_KNOW')
									);
									?>
									<td>
										<?php echo \Components\Wishlist\Helpers\Html::formSelect('effort', $effort, $this->wish->ranking('effort'), 'rankchoices'); ?>
									</td>
									<?php
								}

								if ($this->wish->rankings()->total() == 0)
								{
									?>
									<td><?php echo Lang::txt('COM_WISHLIST_NA'); ?></td>
									<?php
								}
								else
								{
									?>
									<td>
										<?php echo \Components\Wishlist\Helpers\Html::convertVote($this->wish->get('average_effort', $this->wish->ranking('effort')), 'effort'); ?>
									</td>
									<?php
								}
								?>
									<td class="reward">
									<?php if ($this->wishlist->get('banking')) { ?>
										<span class="entry-reward">
										<?php if ($this->wish->get('bonus', 0) > 0 && ($this->wish->isOpen() or $this->wish->isAccepted())) { ?>
											<a class="bonus tooltips" href="<?php echo Route::url($this->wish->link('addbonus')); ?>" title="<?php echo Lang::txt('COM_WISHLIST_WISH_ADD_BONUS'); ?>">+ <?php echo $this->wish->get('bonus', 0); ?></a>
										<?php } else if ($this->wish->isOpen() or $this->wish->isAccepted()) { ?>
											<a class="no-bonus tooltips" href="<?php echo Route::url($this->wish->link('addbonus')); ?>" title="<?php echo Lang::txt('COM_WISHLIST_WISH_ADD_BONUS'); ?>">0</a>
										<?php } else { ?>
											<span class="bonus-inactive" title="<?php echo Lang::txt('COM_WISHLIST_WISH_BONUS_NOT_ACCEPTED'); ?>">&nbsp;</span>
										<?php } ?>
										</span>
									<?php } ?>
									</td>
								</tr>
							</tbody>
						</table>

						<?php echo Html::input('token'); ?>

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="task" value="savevote" />
						<input type="hidden" name="category" value="<?php echo $this->wishlist->get('category'); ?>" />
						<input type="hidden" name="rid" value="<?php echo $this->wishlist->get('referenceid'); ?>" />
						<input type="hidden" name="wishid" value="<?php echo $this->wish->get('id'); ?>" />
					</form>
				</div><!-- / .container -->
			<?php } //if ($this->admin) { ?>

			<?php if ($this->wish->get('action') == 'delete') { ?>
				<div class="warning" id="action">
					<h4><?php echo Lang::txt('COM_WISHLIST_ARE_YOU_SURE_DELETE_WISH'); ?></h4>
					<p>
						<span class="say_yes">
							<a class="btn btn-danger" href="<?php echo Route::url($this->wish->link('delete')); ?>">
								<?php echo Lang::txt('COM_WISHLIST_YES'); ?>
							</a>
						</span>
						<span class="say_no">
							<a class="btn btn-secondary" href="<?php echo Route::url($this->wish->link()); ?>">
								<?php echo Lang::txt('COM_WISHLIST_NO'); ?>
							</a>
						</span>
					</p>
				</div><!-- / .error -->
			<?php } ?>

			<?php if ($this->wish->get('action') == 'changestatus') { ?>
				<div class="takeaction" id="action">
					<form class="edit-form" id="changeStatus" method="post" action="<?php echo Route::url('index.php?option=' . $this->option); ?>">
						<h4><?php echo Lang::txt('COM_WISHLIST_ACTION_CHANGE_STATUS_TO'); ?></h4>
						<fieldset>
							<div class="sidenote">
								<p><?php echo Lang::txt('COM_WISHLIST_WISH_STATUS_INFO'); ?></p>
							</div>

							<?php echo Html::input('token'); ?>

							<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
							<input type="hidden" name="task" value="editwish" />
							<input type="hidden" id="wishlist" name="wishlist" value="<?php echo $this->escape($this->wishlist->get('id')); ?>" />
							<input type="hidden" id="category" name="category" value="<?php echo $this->escape($this->wishlist->get('category')); ?>" />
							<input type="hidden" id="rid" name="rid" value="<?php echo $this->escape($this->wishlist->get('referenceid')); ?>" />
							<input type="hidden" id="wishid" name="wishid" value="<?php echo $this->escape($this->wish->get('id')); ?>" />

							<label for="field-status-pending">
								<input type="radio" name="status" id="field-status-pending" value="pending" <?php echo ($this->wish->isOpen()) ? 'checked="checked"' : ''; ?> />
								<?php echo Lang::txt('COM_WISHLIST_WISH_STATUS_PENDING'); ?>
							</label>

							<label for="field-status-accepted">
								<input type="radio" name="status" id="field-status-accepted" value="accepted" <?php echo ($this->wish->isAccepted()) ? 'checked="checked"' : ''; ?> />
								<?php echo Lang::txt('COM_WISHLIST_WISH_STATUS_ACCEPTED'); ?>
							</label>

							<label for="field-status-rejected">
								<input type="radio" name="status" id="field-status-rejected" value="rejected" <?php echo ($this->wish->isRejected()) ? 'checked="checked"' : ''; ?> />
								<?php echo Lang::txt('COM_WISHLIST_WISH_STATUS_REJECTED'); ?>
							</label>

							<label<?php if ($this->wishlist->get('category') == 'resource') { echo ' class="grantstatus"'; } ?>>
								<input type="radio" name="status" value="granted" <?php echo ($this->wish->get('status') == 1) ? 'checked="checked"' : ''; echo ($this->wish->get('assigned') && $this->wish->get('assigned') != User::get('id')) ? 'disabled="disabled"' : ''; ?> />
								<?php echo Lang::txt('COM_WISHLIST_WISH_STATUS_GRANTED'); ?>
							<?php if ($this->wish->get('assigned') && $this->wish->get('assigned') != User::get('id')) { ?>
								<span class="forbidden"> - <?php echo Lang::txt('COM_WISHLIST_WISH_STATUS_GRANTED_WARNING'); ?>
							<?php }
							// Throws error Hubzero\Base\Model; Method [versions] does not exist.
							/*else if ($this->wishlist->get('category')=='resource' && $this->wish->versions()) { ?>
								<label class="doubletab">
									<?php echo Lang::txt('COM_WISHLIST_IN'); ?>
									<select name="vid" id="vid">
								<?php foreach ($this->wish->versions() as $v) {
									$v_label = $v->state == 3 ? Lang::txt('COM_WISHLIST_NEXT_TOOL_RELEASE') : Lang::txt('COM_WISHLIST_VERSION').' '.$v->version.' ('.Lang::txt('COM_WISHLIST_REVISION').' '.$v->revision.')';
								?>
										<option value="<?php echo $v->id; ?>"><?php echo $v_label; ?></option>
								<?php } ?>
									</select>
								</label>
							<?php }
							*/ ?>
							</label>

							<p>
								<input type="submit" class="btn btn-success" value="<?php echo strtolower(Lang::txt('COM_WISHLIST_ACTION_CHANGE_STATUS')); ?>" />

								<a class="btn btn-secondary" href="<?php echo Route::url($this->wish->link()); ?>">
									<?php echo Lang::txt('COM_WISHLIST_CANCEL'); ?>
								</a>
							</p>
						</fieldset>
					</form>
				</div><!-- / .takeaction -->
			<?php } ?>

			<?php if (!$this->wish->isDeleted() && !$this->wish->isWithdrawn()) { ?>
				<?php if ($this->wish->get('action') == 'addbonus' && $this->wish->get('status')!=1 && $this->wishlist->get('banking')) { ?>
					<div class="addbonus" id="action">
						<form class="edit-form" id="addBonus" method="post" action="<?php echo Route::url('index.php?option=' . $this->option); ?>">
							<h4><?php echo Lang::txt('COM_WISHLIST_WISH_ADD_BONUS'); ?></h4>
							<fieldset>
								<div class="sidenote">
									<p><?php echo Lang::txt('COM_WISHLIST_WHY_ADDBONUS'); ?></p>
								</div>

								<p class="summary">
									<strong>
										<?php
										$bonus = $this->wish->get('bonus', 0);
										echo $this->wish->get('bonusgivenby') .' '.Lang::txt('user(s)').' '.Lang::txt('COM_WISHLIST_WISH_BONUS_CONTRIBUTED_TOTAL').' '.$bonus.' '.Lang::txt('COM_WISHLIST_POINTS').' '.Lang::txt('COM_WISHLIST_WISH_BONUS_AS_BONUS');
										?>
									</strong>
								</p>

								<input type="hidden" name="task" value="addbonus" />
								<input type="hidden" name="wishlist" id="wishlist" value="<?php echo $this->escape($this->wishlist->get('id')); ?>" />
								<input type="hidden" name="wish" id="wish" value="<?php echo $this->escape($this->wish->get('id')); ?>" />

								<label for="field-amount">
									<?php echo Lang::txt('COM_WISHLIST_ACTION_ADD'); ?>
									<span class="price"></span>
									<input class="option" type="text" maxlength="4" name="amount" id="field-amount" value=""<?php echo ($this->wish->get('funds') <= 0) ? ' disabled="disabled"' : ''; ?> />
									<span>
										(<?php echo Lang::txt('COM_WISHLIST_NOTICE_OUT_OF'); ?> <?php echo $this->wish->get('funds'); ?> <?php echo Lang::txt('COM_WISHLIST_NOTICE_POINTS_AVAILABLE'); ?>
										<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=points'); ?>"><?php echo Lang::txt('COM_WISHLIST_ACCOUNT'); ?></a>)
									</span>
								</label>

								<p>
									<?php if ($this->wish->get('funds') > 0) { ?>
										<input type="submit" class="btn btn-success process" value="<?php echo strtolower(Lang::txt('COM_WISHLIST_ACTION_ADD_POINTS')); ?>" />
									<?php } ?>
									<a class="btn btn-secondary" href="<?php echo Route::url($this->wish->link()); ?>">
										<?php echo Lang::txt('COM_WISHLIST_CANCEL'); ?>
									</a>
								</p>
							</fieldset>
						</form>
					<?php if ($this->wish->get('funds') <= 0) { ?>
						<p class="nofunds"><?php echo Lang::txt('COM_WISHLIST_SORRY_NO_FUNDS'); ?></p>
					<?php } ?>
						<div class="clear"></div>
					</div><!-- / .addbonus -->
				<?php } ?>

				<?php if ($this->wish->get('action') == 'move') { ?>
					<div class="moveitem" id="action">
						<form class="edit-form" id="moveWish" method="post" action="<?php echo Route::url('index.php?option=' . $this->option); ?>">
						<?php if ($this->getError()) {
							echo '<p class="error">' .$this->getError() . '</p>';
						} ?>
							<h4><?php echo Lang::txt('COM_WISHLIST_WISH_BELONGS_TO'); ?>:</h4>
							<fieldset>
								<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
								<input type="hidden"  name="task" value="movewish" />
								<input type="hidden" id="wishlist" name="wishlist" value="<?php echo $this->wishlist->get('id'); ?>" />
								<input type="hidden" id="wish" name="wish" value="<?php echo $this->wish->get('id'); ?>" />

								<label>
									<input class="option" type="radio" name="type" value="general" <?php echo ($this->wishlist->get('category')=='general') ? 'checked="checked"' : ''; ?> />
									<?php echo Lang::txt('COM_WISHLIST_MAIN_NAME'); ?>
								</label>

								<label>
									<input class="option" type="radio" name="type" value="resource" <?php echo ($this->wishlist->get('category')=='resource') ? 'checked="checked"' : ''; ?> />
									<?php echo Lang::txt('COM_WISHLIST_RESOURCE_NAME'); ?>
								</label>
								<label>
									<input class="secondary_option" type="text" name="resource" id="acresource" value="<?php echo ($this->wishlist->get('category')=='resource') ? $this->wishlist->get('referenceid') : ''; ?>" autocomplete="off" />
								</label>

							<?php if ($this->wish->get('cats') && preg_replace("/group/", '', $this->wish->get('cats')) != $this->wish->get('cats')) { ?>
								<label>
									<input class="option" type="radio" name="type" value="group" <?php if ($this->wishlist->get('category')=='group') { echo 'checked="checked"'; } ?> />
									<?php echo Lang::txt('COM_WISHLIST_GROUP_NAME'); ?>
								</label>

								<label>
									<input type="text" name="group" value="<?php if ($this->wishlist->get('category')=='group') { echo $this->wishlist->item('alias'); } ?>" id="acgroup" class="secondary_option" autocomplete="off" />
								</label>
							<?php } ?>
								<fieldset>
									<legend><?php echo Lang::txt('COM_WISHLIST_TRANSFER_OPTIONS'); ?>:</legend>
									<label>
										<input class="option" type="checkbox" name="keepcomments" value="1" checked="checked" />
										<?php echo Lang::txt('COM_WISHLIST_TRANSFER_OPTIONS_PRESERVE_COMMENTS'); ?>
									</label>
									<label>
										<input class="option" type="checkbox" name="keepplan" value="1" checked="checked" />
										<?php echo Lang::txt('COM_WISHLIST_TRANSFER_OPTIONS_PRESERVE_PLAN'); ?>
									</label>
									<label>
										<input class="option" type="checkbox" name="keepstatus" value="1" checked="checked" />
										<?php echo Lang::txt('COM_WISHLIST_TRANSFER_OPTIONS_PRESERVE_STATUS'); ?>
									</label>
									<label>
										<input class="option" type="checkbox" name="keepfeedback" value="1" checked="checked" />
										<?php echo Lang::txt('COM_WISHLIST_TRANSFER_OPTIONS_PRESERVE_VOTES'); ?>
									</label>
								</fieldset>

								<p>
									<input type="submit" value="<?php echo strtolower(Lang::txt('COM_WISHLIST_ACTION_MOVE_THIS_WISH')); ?>" />
									<span class="cancelaction">
										<a href="<?php echo Route::url($this->wish->link()); ?>">
											<?php echo Lang::txt('COM_WISHLIST_CANCEL'); ?>
										</a>
									</span>
								</p>
							</fieldset>
						</form>
					</div><!-- / .moveitem -->
				<?php } ?>
			<?php } // if not withdrawn ?>
			</div><!-- / .wish -->
		</div><!-- / .subject -->
		<aside class="aside">
			<div class="wish-status">
				<p class="<?php echo $this->wish->status('alias'); ?>">
				<?php if ($this->wishlist->access('manage')) { ?>
					<a href="<?php echo Route::url($this->wish->link('changestatus')); ?>">
				<?php } ?>
						<strong><?php echo $this->wish->status('text'); ?></strong>
				<?php if ($this->wishlist->access('manage')) { ?>
					</a>
				<?php } ?>
				</p>
				<?php if ($this->wishlist->access('manage')) { ?>
					<p class="note">
						<?php echo $this->wish->status('note'); ?>
					</p>
				<?php } ?>
			</div><!-- / .wish-status -->
		</aside><!-- / .aside -->
	</section><!-- / .main section -->

<?php if (!$this->wish->isDeleted() && !$this->wish->isWithdrawn()) { ?>
	<?php $comments = $this->wish->comments()->whereIn('state', array(1, 3))->rows(); ?>
	<section class="below section" id="section-comments">
		<div class="subject">
			<h3>
				<?php echo Lang::txt('COM_WISHLIST_COMMENTS');?> (<?php echo $comments->count(); ?>)
			</h3>
			<?php
			if ($comments->count() > 0)
			{
				$this->view('_list')
				     ->set('parent', 0)
				     ->set('cls', 'odd')
				     ->set('depth', 0)
				     ->set('option', $this->option)
				     ->set('comments', $comments)
				     ->set('wishlist', $this->wishlist)
				     ->set('wish', $this->wish)
				     ->display();
			}
			else
			{
				?>
				<p>
					<?php echo Lang::txt('COM_WISHLIST_NO_COMMENTS'); ?> <a href="<?php echo Route::url($this->wish->link('comment')); ?>"><?php echo Lang::txt('COM_WISHLIST_MAKE_A_COMMENT'); ?></a>.
				</p>
				<?php
			}
			?>
		</div><!-- / .subject -->
		<div class="aside">
			<p>
				<?php 
					$link = Route::url($this->wish->link('comment'));
					if (User::isGuest())
					{
						$link = Route::url('index.php?option=com_users&view=login&return=' . base64_encode($link));
					}
				?>
				<a class="icon-add add btn" href="<?php echo $link;?>">
					<?php echo Lang::txt('COM_WISHLIST_ADD_A_COMMENT'); ?>
				</a>
			</p>
		</div><!-- / .aside -->
	</section><!-- / .below section -->

	<?php if (!User::isGuest()) { //if (is_object($this->addcomment) && $this->addcomment->item_id == $this->wish->get('id')) { ?>
		<section class="below section">
			<div class="subject">
				<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="commentform" enctype="multipart/form-data">
					<h3>
						<?php echo Lang::txt('COM_WISHLIST_ACTION_ADD_COMMENT'); ?>
					</h3>
					<p class="comment-member-photo">
						<img src="<?php echo User::picture(); ?>" alt="" />
					</p>
					<fieldset>
						<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
						<input type="hidden" name="listid" value="<?php echo $this->escape($this->wishlist->get('id')); ?>" />
						<input type="hidden" name="wishid" value="<?php echo $this->escape($this->wish->get('id')); ?>" />
						<input type="hidden" name="task" value="savereply" />
						<input type="hidden" name="referenceid" value="<?php echo $this->escape($this->wish->get('id')); ?>" />
						<input type="hidden" name="cat" value="wish" />

						<input type="hidden" name="comment[item_id]" value="<?php echo $this->wish->get('id'); ?>" />
						<input type="hidden" name="comment[item_type]" value="wish" />
						<input type="hidden" name="comment[parent]" value="0" />

						<?php echo Html::input('token'); ?>

						<label for="comment<?php echo $this->wish->get('id'); ?>">
							<?php echo Lang::txt('COM_WISHLIST_ENTER_COMMENTS'); ?>
							<?php
							echo $this->editor('comment[content]', '', 35, 4, 'comment' . $this->wish->get('id'), array('class' => 'minimal no-footer'));
							?>
						</label>

						<fieldset>
							<div class="grouping">
								<label for="comment-upload">
									<?php echo Lang::txt('COM_WISHLIST_ACTION_ATTACH_FILE'); ?>
									<input type="file" name="upload" id="comment-upload" />
								</label>
								<label for="comment-description">
									<?php echo Lang::txt('COM_WISHLIST_ACTION_ATTACH_FILE_DESC'); ?>
									<input type="text" name="description" id="comment-description" value="" />
								</label>
							</div>
						</fieldset>

						<label id="comment-anonymous-label" for="comment-anonymous">
							<input class="option" type="checkbox" name="comment[anonymous]" value="1" id="comment-anonymous" />
							<?php echo Lang::txt('COM_WISHLIST_POST_COMMENT_ANONYMOUSLY'); ?>
						</label>

						<p class="submit">
							<input type="submit" value="<?php echo Lang::txt('COM_WISHLIST_POST_COMMENT'); ?>" />
						</p>

						<div class="sidenote">
							<p>
								<strong><?php echo Lang::txt('COM_WISHLIST_COMMENT_KEEP_POLITE'); ?></strong>
							</p>
						</div>
					</fieldset>
				</form>
			</div><!-- / .subject -->
			<div class="aside">
			</div><!-- / .aside -->
		</section><!-- / .below section -->
	<?php } ?>

	<?php if ($this->wishlist->access('manage')) {  // let advisory committee view this too ?>
		<section class="below section" id="plan">
			<div class="subject" id="full_plan">
				<h3>
					<?php echo Lang::txt('COM_WISHLIST_IMPLEMENTATION_PLAN'); ?>
					<?php if ($this->wish->plan->get('id')) { ?>
						(<a href="<?php echo Route::url($this->wish->link('editplan')); ?>"><?php echo Lang::txt('COM_WISHLIST_ACTION_EDIT'); ?></a>)
					<?php } else { ?>
						(<?php echo Lang::txt('COM_WISHLIST_PLAN_NOT_STARTED'); ?>)
					<?php } ?>
				</h3>
				<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="planform" enctype="multipart/form-data">
					<p class="plan-member-photo">
						<span class="plan-anchor"></span>
						<img src="<?php echo User::picture(0); ?>" alt="<?php echo Lang::txt('COM_WISHLIST_MEMBER_PICTURE'); ?>" />
					</p>
					<fieldset>
				<?php if ($this->wish->get('action') == 'editplan') { ?>
						<div class="grid">
							<div class="col span6">
								<label>
									<?php echo Lang::txt('COM_WISHLIST_WISH_ASSIGN_TO'); ?>:
									<?php echo $this->wish->get('assignlist'); ?>
								</label>
							</div>
							<div class="col span6 omega">
								<label for="publish_up" id="publish_up-label">
									<?php echo Lang::txt('COM_WISHLIST_DUE'); ?> (<?php echo Lang::txt('COM_WISHLIST_OPTIONAL'); ?>)
									<input class="option" type="text" name="publish_up" id="publish_up" size="10" maxlength="10" placeholder="YYYY-MM-DD" value="<?php echo $due ? $this->wish->due() : ''; ?>" />
								</label>
							</div>
						</div>

						<?php if ($this->wish->get('plan')) { ?>
							<label class="newrev" for="create_revision">
								<input type="checkbox" class="option" name="create_revision" id="create_revision" value="1" />
								<?php echo Lang::txt('COM_WISHLIST_PLAN_NEW_REVISION'); ?>
							</label>
						<?php } else { ?>
							<input type="hidden" name="create_revision" value="0" />
						<?php } ?>
						<label>
							<?php echo Lang::txt('COM_WISHLIST_ACTION_INSERT_TEXT'); ?>
							<?php echo $this->editor('pagetext', $this->escape($this->wish->plan->get('pagetext')), 35, 40, 'pagetext', array('class' => 'minimal no-footer')); ?>
						</label>

						<input type="hidden" name="pageid" value="<?php echo $this->wish->plan()->get('id', 0); ?>" />
						<input type="hidden" name="version" value="<?php echo $this->wish->plan()->get('version', 1); ?>" />
						<input type="hidden" name="wishid" value="<?php echo $this->wish->get('id'); ?>" />
						<input type="hidden" name="option" value="'<?php echo $this->option; ?>" />
						<input type="hidden" name="created_by" value="<?php echo User::get('id'); ?>" />
						<input type="hidden" name="task" value="saveplan" />

						<?php echo Html::input('token'); ?>

						<p class="submit">
							<input type="submit" name="submit" value="<?php echo Lang::txt('COM_WISHLIST_SAVE'); ?>" />
							<span class="cancelaction">
								<a href="<?php echo Route::url($this->wish->link()); ?>">
									<?php echo Lang::txt('COM_WISHLIST_CANCEL'); ?>
								</a>
							</span>
						</p>

						<div class="sidenote">
							<p>
								<?php echo Lang::txt('COM_WISHLIST_PLAN_FORMATTING_HELP'); ?>
							</p>
						</div>
				<?php } else if (!$this->wish->plan->get('id')) { ?>
						<p>
							<?php echo Lang::txt('COM_WISHLIST_THERE_IS_NO_PLAN'); ?>
							<a href="<?php echo Route::url($this->wish->link('editplan')); ?>">
								<?php echo Lang::txt('COM_WISHLIST_START_PLAN'); ?>
							</a>.
						</p>
						<?php if ($this->wish->isOpen() or $this->wish->isAccepted()) { ?>
							<p>
								<?php echo Lang::txt('COM_WISHLIST_PLAN_IS_ASSIGNED'); ?>
								<?php echo $assigned; ?>

								<?php echo Lang::txt('COM_WISHLIST_PLAN_IS_DUE'); ?>
								<a href="<?php echo Route::url($this->wish->link('editplan')); ?>'">
									<?php echo ($this->wish->due() && $this->wish->due() != '0000-00-00 00:00:00') ? $this->wish->due() : Lang::txt('COM_WISHLIST_DUE_NEVER'); ?>
								</a>
							</p>
						<?php } ?>
				<?php } else { ?>
					<?php if ($this->wish->isOpen() or $this->wish->isAccepted()) { ?>
						<p>
							<?php echo Lang::txt('COM_WISHLIST_PLAN_IS_ASSIGNED'); ?>
							<?php echo $assigned; ?>
							<?php echo Lang::txt('COM_WISHLIST_PLAN_IS_DUE'); ?>
							<a href="<?php echo Route::url($this->wish->link('editplan')); ?>'">
								<?php echo ($this->wish->due() && $this->wish->due() != '0000-00-00 00:00:00') ? $this->wish->due() : Lang::txt('COM_WISHLIST_DUE_NEVER'); ?>
							</a>.
						</p>
					<?php } ?>
						<div class="planbody">
							<p class="plannote">
								<?php echo Lang::txt('COM_WISHLIST_PLAN_LAST_EDIT').' '.$this->wish->plan->created('date').' at '.$this->wish->plan->created('time').' '.Lang::txt('COM_WISHLIST_BY').' '.$this->wish->plan->creator->get('name');?>
							</p>
							<?php echo $this->wish->plan->content; ?>
						</div>
				<?php } ?>
					</fieldset>
				</form>
			</div><!-- / .subject -->
			<aside class="aside">
			<?php if ($this->wish->get('action') != 'editplan') { ?>
				<p>
					<a class="icon-add add btn" href="<?php echo Route::url($this->wish->link('editplan')); ?>">
						<?php echo Lang::txt('COM_WISHLIST_ADD_TO_THE_PLAN'); ?>
					</a>
				</p>
			<?php } else { ?>
				<p><?php echo Lang::txt('COM_WISHLIST_PLAN_DEADLINE_EXPLANATION'); ?></p>
			<?php } ?>
			</aside><!-- / .aside -->
		</section><!-- / .below section -->
	<?php } // if ($this->admin) ?>
<?php } // if not withdrawn ?>

<?php
}
