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

$dateFormat = '%b %d, %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'M d, Y';
	$tz = false;
}

		/* Wish view */
		$error		= $this->getError();
		
		$html = '';
		
if ($this->wishlist && $this->wish) {
			// What name should we dispay for the submitter?
			$user = new \Hubzero\User\Profile();
			$user->load($this->wish->proposed_by);
			$name = JText::_('ANONYMOUS');
			if ($this->wish->anonymous != 1) {
				$name = $this->wish->authorname;
			}
			
			$assigned = ($this->wish->assigned && ($this->admin==2 or $this->admin==1)) ? JText::_('assigned to').' <a href="'.JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id).'?filterby='.$this->filters['filterby'].'&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag'].'&action=editplan#plan">'.$this->wish->assignedto.'</a>' : '';	
			
			if (!$assigned && ($this->admin==2 or $this->admin==1) && $this->wish->status==0) {
				$assigned = '<a href="'.JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id).'?filterby='.$this->filters['filterby'].'&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag'].'&action=editplan#plan">'.JText::_('unassigned').'</a>';
			}
				
			$this->wish->status = ($this->wish->accepted==1 && $this->wish->status==0) ? 6 : $this->wish->status;
			$due  = ($this->wish->due !='0000-00-00 00:00:00') ? JHTML::_('date', $this->wish->due, JText::_('DATE_FORMAT_HZ1')) : '';
				
			switch ($this->wish->status) 
			{
				case 0:    	$status = strtolower(JText::_('COM_WISHLIST_WISH_STATUS_PENDING'));
							$statusnote = JText::_('COM_WISHLIST_WISH_STATUS_PENDING_INFO');
				break;
				case 6:    	$status = strtolower(JText::_('COM_WISHLIST_WISH_STATUS_ACCEPTED'));
							$statusnote = JText::_('COM_WISHLIST_WISH_STATUS_ACCEPTED_INFO');
							$statusnote.= $this->wish->plan ? '; '.JText::_('COM_WISHLIST_WISH_PLAN_STARTED') : '';
							$statusnote.= $due ? '; '.JText::_('COM_WISHLIST_WISH_DUE_SET').' '.$due : '';
				break;
				case 3:    	$status = strtolower(JText::_('COM_WISHLIST_WISH_STATUS_REJECTED'));
							$statusnote = JText::_('COM_WISHLIST_WISH_STATUS_REJECTED_INFO');
				break;
				case 4:    	$status = strtolower(JText::_('COM_WISHLIST_WISH_STATUS_WITHDRAWN'));
							$statusnote = JText::_('COM_WISHLIST_WISH_STATUS_WITHDRAWN_INFO');
				break;
				case 1:    	$status = strtolower(JText::_('COM_WISHLIST_WISH_STATUS_GRANTED'));
							$statusnote = $this->wish->granted!='0000-00-00 00:00:00' ? strtolower(JText::_('ON')).' '.JHTML::_('date', $this->wish->granted, JText::_('DATE_FORMAT_HZ1')).' '.strtolower(JText::_('BY')).' '.$this->wish->grantedby : '';
				break;
			}
			
			// Can't view wishes on a private list if not list admin
	if (!$this->wishlist->public && !$this->admin) {
		$html .= '<header id="content-header"><h2>' . JText::_('COM_WISHLIST_PRIVATE_LIST') . '</h2></header>';
		$html .= '<section class="main section">'."\n";
		$html .= '<p class="error">' . JText::_('COM_WISHLIST_WARNING_NOT_AUTHORIZED_PRIVATE_LIST') . '</p>' ."\n";
		$html .= '</section>'."\n";	
	 } else {
		
		$filters  = '';
		$filters .= ($this->filters['filterby']) ? '&filterby=' . $this->filters['filterby'] : '';
		$filters .= ($this->filters['sortby'])   ? '&sortby=' . $this->filters['sortby']     : '';
		$filters .= ($this->filters['tag'])      ? '&tags=' . $this->filters['tag']          : '';
		$filters .= ($this->filters['limit'])    ? '&limit=' . $this->filters['limit']       : '';
		$filters .= ($this->filters['start'])    ? '&start=' . $this->filters['start']       : '';
?>
	<header id="content-header">
		<h2><?php echo $this->title . ': ' . JText::_('COM_WISHLIST_WISH') . ' #' . $this->wish->id; ?></h2>

		<div id="content-header-extra">
			<ul id="useroptions">
				<li>
			<?php if ($this->wish->prev) { ?>
					<a class="icon-prev prev btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->prev . $filters); ?>">
						<span><?php echo JText::_('COM_WISHLIST_PREV'); ?></span>
					</a>
			<?php } else { ?>
					<span class="icon-prev prev btn">
						<span><?php echo JText::_('COM_WISHLIST_PREV'); ?></span>
					</span>
			<?php } ?>
				</li>
				<li>
					<a class="all btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wishlist&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid . $filters); ?>">
						<span><?php echo JText::_('COM_WISHLIST_All'); ?></span>
					</a>
				</li>
				<li class="last">
				<?php if ($this->wish->next) { ?>
					<a class="icon-next next opposite btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->next . $filters); ?>">
						<span><?php echo JText::_('COM_WISHLIST_NEXT'); ?></span>
					</a>
				<?php } else { ?>
					<span class="icon-next next opposite btn">
						<span><?php echo JText::_('COM_WISHLIST_NEXT'); ?></span>
					</span>
				<?php } ?>
				</li>
			</ul>
		</div><!-- / #content-header-extra -->
	</header><!-- / #content-header -->

<?php if ($this->wish->saved==3 && !$error) { ?>
	<p class="passed"><?php echo JText::_('New wish successfully posted. Thank you!'); ?></p>
<?php } ?>
<?php if ($this->wish->saved==2 && !$error && $this->admin) { ?>
	<p class="passed"><?php echo JText::_('Changes to the wish successfully saved.'); ?></p>
<?php } ?>

	<section class="main section">
		<div class="subject">
		<?php if ($this->wish->reports) { ?>
			<p class="warning"><?php echo JText::_('COM_WISHLIST_NOTICE_POSTING_REPORTED'); ?></p>
		</div><!-- / .subject -->
		<div class="aside">
		</div>
	</section><!-- / .main section -->
		<?php } else if (!$this->admin && $this->wish->status == 4) { ?>
			<p class="warning"><?php echo JText::_('This wish has been withdrawn.'); ?></p>
		</div><!-- / .subject -->
		<div class="aside">
		</div>
	</section><!-- / .main section -->
		<?php } else { ?>
			<div class="entry wish" id="w<?php echo $this->wish->id; ?>">
				<p class="entry-member-photo">
					<span class="entry-anchor"></span>
					<img src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($user, $this->wish->anonymous); ?>" alt="<?php echo JText::_('Member avatar'); ?>" />
				</p><!-- / .wish-member-photo -->

				<div class="entry-content">
					<p class="entry-voting voting" id="wish_<?php echo $this->wish->id; ?>">
						<?php
						$view = new JView(array('name'=>'rateitem'));
						$view->option  = $this->option;
						$view->item    = $this->wish;
						$view->listid  = $this->wishlist->id;
						$view->plugin  = 0;
						$view->admin   = $this->admin;
						$view->page    = 'wish';
						$view->filters = $this->filters;
						echo $view->loadTemplate();
						?>
					</p><!-- / .wish-voting -->

					<p class="entry-title">
						<strong><?php echo $name; ?></strong>
						<a class="permalink" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id); ?>" rel="bookmark" title="<?php echo JText::_('COM_WISHLIST_PERMALINK'); ?>"><span class="entry-date-at">@</span> 
							<span class="time"><time datetime="<?php echo $this->wish->proposed; ?>"><?php echo JHTML::_('date', $this->wish->proposed, JText::_('TIME_FORMAT_HZ1')); ?></time></span> <span class="entry-date-on"><?php echo JText::_('COM_WISHLIST_ON'); ?></span> 
							<span class="date"><time datetime="<?php echo $this->wish->proposed; ?>"><?php echo JHTML::_('date', $this->wish->proposed, JText::_('DATE_FORMAT_HZ1')); ?></time></span>
						</a>
					</p><!-- / .wish-title -->

					<div class="entry-subject">
						<p><?php echo $this->escape(stripslashes($this->wish->subject)); ?></p>
					</div><!-- / .wish-subject -->
				<?php if ($this->wish->about) { ?>
					<div class="entry-long">
						<?php 
						$wish = new WishlistModelWish($this->wish);

						echo $wish->content('parsed');
						?>
					</div><!-- / .wish-details -->
				<?php } ?>

					<div class="entry-tags">
						<p>Tags:</p>
					<?php if (count($this->wish->tags) > 0) { ?>
						<ol class="tags">
						<?php foreach ($this->wish->tags as $tag) { ?>
							<li><a href="<?php echo JRoute::_('index.php?option=com_tags&tag='.$tag['tag']); ?>" rel="tag"><?php echo $this->escape($tag['raw_tag']); ?></a></li>
						<?php } ?>
						</ol>
					<?php } else { ?>
						<?php echo JText::_('COM_WISHLIST_NONE'); ?>
					<?php } ?>
					</div><!-- / .wish-tags -->
				</div><!-- / .wish-content -->
					
				<?php
					if ($this->admin) {
						$eligible = array_merge($this->wishlist->owners, $this->wishlist->advisory);
						$eligible = array_unique($eligible);
						$voters = ($this->wish->num_votes <= count($eligible)) ? count($eligible) : $this->wish->num_votes;
						$html = '';
						if ($due && $this->wish->status!=1) 
						{
							$html .= ($this->wish->due <= JFactory::getDate()->toSql()) ? '<span class="overdue"><a href="'.JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id).'?action=editplan#plan">'.JText::_('COM_WISHLIST_OVERDUE') : '<span class="due"><a href="'.JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id) . '?action=editplan#plan">' . JText::_('COM_WISHLIST_WISH_DUE_IN') . ' ' . WishlistHTML::nicetime($this->wish->due);
							$html .= '</a></span>';
						}
						//$html .= '</div>'."\n";
						echo $html;
						$html = '';
					}
				?>
			<ul class="wish-options">
				<?php if ($this->admin && $this->admin!=3) { ?>
					<?php if ($this->wish->status!=1) { ?>
						<li>
							<a class="changestatus" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id.'&action=changestatus#action'); ?>">
								<?php echo JText::_('COM_WISHLIST_ACTION_CHANGE_STATUS'); ?>
							</a>
						</li>
					<?php } ?>
						<li>
							<a class="transfer" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id.'&action=move#action'); ?>">
								<?php echo JText::_('COM_WISHLIST_MOVE'); ?>
							</a>
						</li>
					<?php if($this->wish->private) { ?>
						<li>
							<a class="makepublic" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=editprivacy&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id.'&private=0'); ?>">
								<?php echo JText::_('COM_WISHLIST_MAKE_PUBLIC'); ?>
							</a>
						</li>
					<?php } else { ?>
						<li>
							<a class="makeprivate" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=editprivacy&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id.'&private=1'); ?>">
								<?php echo JText::_('COM_WISHLIST_MAKE_PRIVATE'); ?>
							</a>
						</li>	
					<?php } ?>
				<?php } ?>
				<?php if ($this->admin || $this->juser->get('id') == $this->wish->proposed_by) { ?>
					<li>
						<a class="edit" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=editwish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id); ?>">
							<?php echo ucfirst(JText::_('COM_WISHLIST_ACTION_EDIT')); ?>
						</a>
					</li>
				<?php } ?>
					<li>
						<a href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=wish&id='.$this->wish->id.'&parent='.$this->wishlist->id); ?>" class="abuse">
							<?php echo JText::_('COM_WISHLIST_REPORT_ABUSE'); ?>
						</a>
					</li>
				<?php if ($this->juser->get('id') == $this->wish->proposed_by && $this->wish->status==0) { ?>
					<li>
						<a class="delete" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id.'&action=delete#action'); ?>">
							<?php echo JText::_('COM_WISHLIST_ACTION_WITHDRAW_WISH'); ?>
						</a>
					</li>
				<?php } ?>
			</ul>

		<?php if ($this->admin && !in_array($this->wish->status, array(4, 2))) { ?>
							<div class="container">
								<form method="post" action="index.php?option=<?php echo $this->option; ?>" class="rankingform" id="rankForm">
									<table class="wish-priority" id="priority">
										<caption>
											<!-- <a name="priority"></a> -->
											<?php echo JText::_('COM_WISHLIST_PRIORITY'); ?>: <strong><?php echo $this->wish->ranking; ?></strong> 
											<span>(<?php echo $this->wish->num_votes.' '.JText::_('COM_WISHLIST_NOTICE_OUT_OF').' '.$voters.' '.JText::_('COM_WISHLIST_VOTES'); ?>)</span>
										</caption>
										<thead>
											<tr>
												<th></th>
											<?php if ($this->admin==2 || $this->admin==3) { // My opinion is available for list owners/advisory committee only ?>
												<th><?php echo JText::_('COM_WISHLIST_MY_OPINION'); ?></th>
											<?php } ?>
												<th><?php echo JText::_('COM_WISHLIST_CONSENSUS'); ?></th>
												<th><?php echo JText::_('COM_WISHLIST_COMMUNITY_VOTE'); ?></th>
											</tr>
										</thead>
										<?php
										// My opinion is available for list owners/advisory committee only
										if ($this->admin==2 || $this->admin==3) 
										{
										?>
										<tfoot>
											<tr>
												<td></td>
												<td>
													<input type="hidden" name="task" value="savevote" />
													<input type="hidden" name="category" value="general" />
													<input type="hidden" name="rid" value="1" />
													<input type="hidden" name="wishid" value="282" />
													<input type="submit" value="Save" />
												</td>
												<td></td>
												<td></td>
											</tr>
										</tfoot>
										<?php
											}
										?>
										<tbody>
											<tr>
												<th><?php echo JText::_('COM_WISHLIST_IMPORTANCE'); ?></th>
											<?php
											// My opinion is available for list owners/advisory committee only
											if ($this->admin==2 || $this->admin==3) 
											{
												$importance = array(
													''=>JText::_('COM_WISHLIST_SELECT_IMP'),
													'0.0'=>'0 -'.JText::_('COM_WISHLIST_RUBBISH'),
													'1'=>'1 - '.JText::_('COM_WISHLIST_MAYBE'),
													'2'=>'2 - '.JText::_('COM_WISHLIST_INTERESTING'),
													'3'=>'3 - '.JText::_('COM_WISHLIST_GOODIDEA'), 
													'4'=>'4 - '.JText::_('COM_WISHLIST_IMPORTANT'), 
													'5'=>'5 - '.JText::_('COM_WISHLIST_CRITICAL')
												);
												?>
												<td>
													<?php echo WishlistHtml::formSelect('importance', $importance, $this->wish->myvote_imp, 'rankchoices'); ?>
												</td>
												<?php
											}
											if ((isset($this->wish->num_votes) && $this->wish->num_votes==0) 
											 || !isset($this->wish->num_votes)) 
											{
											?>
												<td><?php echo JText::_('COM_WISHLIST_NA'); ?></td>
											<?php
											}
											else 
											{ 
												?>
												<td><?php echo WishlistHtml::convertVote($this->wish->average_imp, 'importance'); ?></td>	
												<?php
											}
											?>
												<td class="voting">
													<?php
													$view = new JView(array('name'=>'rateitem'));
													$view->option  = $this->option;
													$view->item    = $this->wish;
													$view->listid  = $this->wishlist->id;
													$view->plugin  = 0;
													$view->admin   = $this->admin;
													$view->page    = 'wish';
													$view->filters = $this->filters;
													echo $view->loadTemplate();
													?>
												</td>
											</tr>
											<tr>
												<th><?php echo JText::_('COM_WISHLIST_EFFORT'); ?></th>
											<?php
											// My opinion is available for list owners/advisory committee only
											if ($this->admin==2 || $this->admin==3) 
											{
												$effort = array(
													''=>JText::_('COM_WISHLIST_SELECT_EFFORT'),
													'5'=>JText::_('COM_WISHLIST_FOURHOURS'),
													'4'=>JText::_('COM_WISHLIST_ONEDAY'),
													'3'=>JText::_('COM_WISHLIST_TWODAYS'),
													'2'=>JText::_('COM_WISHLIST_ONEWEEK'),
													'1'=>JText::_('COM_WISHLIST_TWOWEEKS'),
													'0.0'=>JText::_('COM_WISHLIST_TWOMONTHS'), 
													'6'=>JText::_('COM_WISHLIST_DONT_KNOW')
												);
												?>
												<td>
													<?php echo WishlistHtml::formSelect('effort', $effort, $this->wish->myvote_effort, 'rankchoices'); ?>
												</td>
												<?php 
											}

											if ((isset($this->wish->num_votes) && $this->wish->num_votes==0) 
											 || !isset($this->wish->num_votes)) 
											{
												?>
												<td><?php echo JText::_('COM_WISHLIST_NA'); ?></td>
												<?php
											}
											else 
											{ 
												if (isset($this->wish->num_votes) 
												 && isset($this->wish->num_skipped_votes)
												 && $this->wish->num_votes==$this->wish->num_skipped_votes) 
												{
													$this->wish->average_effort = 7;
												}
												?>
												<td><?php echo WishlistHtml::convertVote($this->wish->average_effort,'effort'); ?></td>
												<?php
											}
											?>
												<td class="reward">
												<?php if ($this->wishlist->banking) { ?>
													<span class="entry-reward">
														<?php if(isset($this->wish->bonus) && $this->wish->bonus > 0 && ($this->wish->status==0 or $this->wish->status==6)) { ?>
														<a class="bonus tooltips" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id.'&action=addbonus#action'); ?>" title="<?php echo JText::_('COM_WISHLIST_WISH_ADD_BONUS'); ?>">+ <?php echo $this->wish->bonus; ?></a>
														<?php } else if($this->wish->status==0 or $this->wish->status==6) { ?>
														<a class="no-bonus tooltips" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id.'&action=addbonus#action'); ?>" title="<?php echo JText::_('COM_WISHLIST_WISH_ADD_BONUS'); ?>">0</a>
														<?php } else { ?>
														<span class="bonus-inactive" title="<?php echo JText::_('COM_WISHLIST_WISH_BONUS_NOT_ACCEPTED'); ?>">&nbsp;</span>
														<?php } ?>
													</span>
												<?php } ?>
												</td>
											</tr>
										</tbody>
									</table>

									<input type="hidden" name="task" value="savevote" />
									<input type="hidden" name="category" value="<?php echo $this->wishlist->category; ?>" />
									<input type="hidden" name="rid" value="<?php echo $this->wishlist->referenceid; ?>" />
									<input type="hidden" name="wishid" value="<?php echo $this->wish->id; ?>" />
								</form>
							</div><!-- / .container -->
		<?php } //if ($this->admin) { ?>

		<?php if ($this->wish->action == 'delete') { ?>
			<div class="warning" id="action">
				<h4><?php echo JText::_('COM_WISHLIST_ARE_YOU_SURE_DELETE_WISH'); ?></h4>
				<p>
					<span class="say_yes">
						<a class="btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=withdraw&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id); ?>">
							<?php echo strtoupper(JText::_('COM_WISHLIST_YES')); ?>
						</a>
					</span> 
					<span class="say_no">
						<a class="btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id); ?>">
							<?php echo strtoupper(JText::_('COM_WISHLIST_NO')); ?>
						</a>
					</span>
				</p>
			</div><!-- / .error -->
		<?php } ?>

		<?php if ($this->wish->action == 'changestatus') { ?>
			<div class="takeaction" id="action">
				<form class="edit-form" id="changeStatus" method="post" action="index.php?option=<?php echo $this->option; ?>">
					<h4><?php echo JText::_('COM_WISHLIST_ACTION_CHANGE_STATUS_TO'); ?></h4>
					<fieldset>
						<div class="sidenote">
							<p><?php echo JText::_('COM_WISHLIST_WISH_STATUS_INFO'); ?></p>
						</div>

						<input type="hidden" name="task" value="editwish" />
						<input type="hidden" id="wishlist" name="wishlist" value="<?php echo $this->wishlist->id; ?>" />
						<input type="hidden" id="category" name="category" value="<?php echo $this->wishlist->category; ?>" />
						<input type="hidden" id="rid" name="rid" value="<?php echo $this->wishlist->referenceid; ?>" />
						<input type="hidden" id="wishid" name="wishid" value="<?php echo $this->wish->id; ?>" />
						
						<label>
							<input type="radio" name="status" value="pending" <?php echo ($this->wish->status == 0) ? 'checked="checked"' : ''; ?> /> 
							<?php echo JText::_('COM_WISHLIST_WISH_STATUS_PENDING'); ?>
						</label>

						<label>
							<input type="radio" name="status" value="accepted" <?php echo ($this->wish->status == 6) ? 'checked="checked"' : ''; ?> />
							<?php echo JText::_('COM_WISHLIST_WISH_STATUS_ACCEPTED'); ?>
						</label>

						<label>
							<input type="radio" name="status" value="rejected" <?php echo ($this->wish->status == 3) ? 'checked="checked"' : ''; ?> /> 
							<?php echo JText::_('COM_WISHLIST_WISH_STATUS_REJECTED'); ?>
						</label>

						<label<?php if ($this->wishlist->category=='resource') { echo ' class="grantstatus"'; } ?>>
							<input type="radio" name="status" value="granted" <?php echo ($this->wish->status == 1) ? 'checked="checked"' : ''; echo ($this->wish->assigned && $this->wish->assigned!=$this->juser->get('id')) ? 'disabled="disabled"' : ''; ?> /> 
							<?php echo JText::_('COM_WISHLIST_WISH_STATUS_GRANTED'); ?>
						<?php if ($this->wish->assigned && $this->wish->assigned!=$this->juser->get('id')) { ?>
							<span class="forbidden"> - <?php echo JText::_('COM_WISHLIST_WISH_STATUS_GRANTED_WARNING'); ?>
						<?php } else if ($this->wishlist->category=='resource' && isset($this->wish->versions)) { ?>
							<label class="doubletab">
								<?php echo JText::_('COM_WISHLIST_IN'); ?>
								<select name="vid" id="vid">
							<?php
							foreach ($this->wish->versions as $v) {
								$v_label = $v->state == 3 ? JText::_('COM_WISHLIST_NEXT_TOOL_RELEASE') : JText::_('COM_WISHLIST_VERSION').' '.$v->version.' ('.JText::_('COM_WISHLIST_REVISION').' '.$v->revision.')';
							?>
									<option value="<?php echo $v->id; ?>"><?php echo $v_label; ?></option>
							<?php
							}
							?>
								</select>
							</label>
						<?php } ?>
						</label>
						
						<p>
							<input type="submit" value="<?php echo strtolower(JText::_('COM_WISHLIST_ACTION_CHANGE_STATUS')); ?>" /> 
						
							<span class="cancelaction">
								<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id); ?>">
									<?php echo JText::_('COM_WISHLIST_CANCEL'); ?>
								</a>
							</span>
						</p>
					</fieldset>
				</form>
			</div><!-- / .takeaction -->
		<?php } ?>

	<?php if (!in_array($this->wish->status, array(4, 2))) { ?>
		<?php if ($this->wish->action == 'addbonus' && $this->wish->status!=1 && $this->wishlist->banking) { ?>
			<div class="addbonus" id="action">
				<form class="edit-form" id="addBonus" method="post" action="index.php?option=<?php echo $this->option; ?>">
					<h4><?php echo JText::_('COM_WISHLIST_WISH_ADD_BONUS'); ?></h4>
					<fieldset>
						<div class="sidenote">
							<p><?php echo JText::_('COM_WISHLIST_WHY_ADDBONUS'); ?></p>
						</div>

						<p class="summary">
							<strong>
								<?php $bonus = $this->wish->bonus ? $this->wish->bonus : 0; ?>
								<?php echo $this->wish->bonusgivenby.' '.JText::_('user(s)').' '.JText::_('COM_WISHLIST_WISH_BONUS_CONTRIBUTED_TOTAL').' '.$bonus.' '.JText::_('COM_WISHLIST_POINTS').' '.JText::_('COM_WISHLIST_WISH_BONUS_AS_BONUS'); ?>
							</strong>
						</p>

						<input type="hidden"  name="task" value="addbonus" />
						<input type="hidden" id="wishlist" name="wishlist" value="<?php echo $this->wishlist->id; ?>" />
						<input type="hidden" id="wish" name="wish" value="<?php echo $this->wish->id; ?>" />

						<label>
							<?php echo JText::_('COM_WISHLIST_ACTION_ADD'); ?>
							<span class="price"></span>
							<input class="option" type="text" maxlength="4" name="amount" value=""<?php echo ($this->wish->funds <= 0) ? ' disabled="disabled"' : ''; ?> />
							<span>
								(<?php echo JText::_('COM_WISHLIST_NOTICE_OUT_OF'); ?> <?php echo $this->wish->funds; ?> <?php echo JText::_('COM_WISHLIST_NOTICE_POINTS_AVAILABLE'); ?> 
								<a href="<?php echo JRoute::_('members'.DS.$this->juser->get('id').DS.'points'); ?>"><?php echo JText::_('COM_WISHLIST_ACCOUNT'); ?></a>)
							</span>
						</label>

						<p>
					<?php if ($this->wish->funds > 0) { ?>
							<input type="submit" class="process" value="<?php echo strtolower(JText::_('COM_WISHLIST_ACTION_ADD_POINTS')); ?>" />
					<?php } ?>
							<span class="cancelaction">
								<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id); ?>">
									<?php echo JText::_('COM_WISHLIST_CANCEL'); ?>
								</a>
							</span>
						</p>
					</fieldset>
				</form>
			<?php if ($this->wish->funds <= 0) { ?>
				<p class="nofunds"><?php echo JText::_('COM_WISHLIST_SORRY_NO_FUNDS'); ?></p>
			<?php } ?>
				<div class="clear"></div>
			</div><!-- / .addbonus -->
		<?php } ?>

		<?php if ($this->wish->action=='move') { ?>
			<div class="moveitem" id="action">
				<form class="edit-form" id="moveWish" method="post" action="<?php echo JRoute::_('index.php?option='.$this->option . '&id=' . $this->wishlist->id ); ?>">
					<?php if ($error) { echo '<p class="error">' . $error . '</p>'; } ?>

					<h4><?php echo JText::_('COM_WISHLIST_WISH_BELONGS_TO'); ?>:</h4>

					<fieldset>
						<input type="hidden"  name="task" value="movewish" />
						<input type="hidden"  name="option" value="<?php echo $this->option ?>" />
						<input type="hidden" id="wishlist" name="wishlist" value="<?php echo $this->wishlist->id; ?>" />
						<input type="hidden" id="wish" name="wish" value="<?php echo $this->wish->id; ?>" />
						
						<label>
							<input class="option" type="radio" name="type" value="general" <?php echo ($this->wishlist->category=='general') ? 'checked="checked"' : ''; ?> /> 
							<?php echo JText::_('COM_WISHLIST_MAIN_NAME'); ?>
						</label>

						<label>
							<input class="option" type="radio" name="type" value="resource" <?php echo ($this->wishlist->category=='resource') ? 'checked="checked"' : ''; ?> /> 
							<?php echo JText::_('COM_WISHLIST_RESOURCE_NAME'); ?>
						</label>
						<label>
							<input class="secondary_option" type="text" name="resource" id="acresource" value="<?php echo ($this->wishlist->category=='resource') ? $this->wishlist->referenceid : ''; ?>" autocomplete="off" />
						</label>

					<?php if (isset($this->wish->cats) && preg_replace("/group/", '', $this->wish->cats) != $this->wish->cats) { ?>
						<label>
							<input class="option" type="radio" name="type" value="group" <?php if ($this->wishlist->category=='group') { echo 'checked="checked"'; } ?> /> 
							<?php echo JText::_('COM_WISHLIST_GROUP_NAME'); ?>
						</label>

						<label>
						<?php 
						if (!JPluginHelper::isEnabled('system', 'jquery'))
						{
							$document = JFactory::getDocument();
							$document->addScript('/components/com_wishlist/assets/js/observer.js');
							$document->addScript('/components/com_wishlist/assets/js/autocompleter.js');
							$document->addStyleSheet('/components/com_wishlist/assets/js/autocompleter.css');
						}
						?>
							<?php 
							/*JPluginHelper::importPlugin('hubzero');
							$dispatcher = JDispatcher::getInstance();
						$gc = $dispatcher->trigger('onGetMultiEntry', array(array('groups', 'ticket[group]', 'acgroup', '', ($this->wishlist->category=='group' ? $this->wishlist->cn : ''), '', 'ticketowner')));
						if (count($gc) > 0) {
							echo $gc[0];
						} else {*/ ?>
							<input type="text" name="group" value="<?php if ($this->wishlist->category=='group') { echo $this->wishlist->cn; } ?>" id="acgroup" class="secondary_option" autocomplete="off" />
						<?php //} ?>
						</label>
					<?php } ?>
						<fieldset>
							<legend><?php echo JText::_('COM_WISHLIST_TRANSFER_OPTIONS'); ?>:</legend>
							<label>
								<input class="option" type="checkbox" name="keepcomments" value="1" checked="checked" /> 
								<?php echo JText::_('COM_WISHLIST_TRANSFER_OPTIONS_PRESERVE_COMMENTS'); ?>
							</label>
							<label>
								<input class="option" type="checkbox" name="keepplan" value="1" checked="checked" /> 
								<?php echo JText::_('COM_WISHLIST_TRANSFER_OPTIONS_PRESERVE_PLAN'); ?>
							</label>
							<label>
								<input class="option" type="checkbox" name="keepstatus" value="1" checked="checked" /> 
								<?php echo JText::_('COM_WISHLIST_TRANSFER_OPTIONS_PRESERVE_STATUS'); ?>
							</label>
							<label>
								<input class="option" type="checkbox" name="keepfeedback" value="1" checked="checked" /> 
								<?php echo JText::_('COM_WISHLIST_TRANSFER_OPTIONS_PRESERVE_VOTES'); ?>
							</label>
						</fieldset>

						<p>
							<input type="submit" value="<?php echo strtolower(JText::_('COM_WISHLIST_ACTION_MOVE_THIS_WISH')); ?>" /> 
							<span class="cancelaction">
								<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id); ?>">
									<?php echo JText::_('COM_WISHLIST_CANCEL'); ?>
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
				<p class="<?php echo strtolower($status); ?>">
				<?php if ($this->admin==2) { ?>
					<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id.'?action=changestatus#action'); ?>">
				<?php } ?>
					<strong><?php echo strtolower($status); ?></strong>
				<?php if ($this->admin==2) { ?>
					</a>
				<?php } ?>
				</p>
			<?php if ($this->admin==2) { ?>
				<p class="note">
					<?php echo $statusnote; ?>
				</p>
			<?php } ?>
			</div><!-- / .wish-status -->
			<?php 
			/*if (!$this->admin) 
			{
				// Points
				if ($this->wishlist->banking) 
				{
				?>
				<div class="assign_bonus">
					<?php if (isset($this->wish->bonus) && $this->wish->bonus > 0 && ($this->wish->status==0 or $this->wish->status==6)) { ?>
					<a class="bonus tooltips" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id.'?action=addbonus#action'); ?>" title="<?php echo JText::_('WISH_ADD_BONUS').' ::'.$this->wish->bonusgivenby.' '.JText::_('MULTIPLE_USERS').' '.JText::_('WISH_BONUS_CONTRIBUTED_TOTAL').' '.$this->wish->bonus.' '.JText::_('POINTS').' '.JText::_('WISH_BONUS_AS_BONUS'); ?>">
						+ <?php echo $this->wish->bonus; ?>
					</a>
					<?php } else if($this->wish->status==0 or $this->wish->status==6) { ?>
					<a class="nobonus tooltips" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id.'?action=addbonus#action'); ?>" title="<?php echo JText::_('WISH_ADD_BONUS').' :: '.JText::_('WISH_BONUS_NO_USERS_CONTRIBUTED'); ?>">
						&nbsp;
					</a>
					<?php } else { ?>
					<span class="bonus_inactive" title="<?php echo JText::_('WISH_BONUS_NOT_ACCEPTED'); ?>">
						&nbsp;
					</span>
					<?php } ?>
				</div><!-- / .assign_bonus -->
				<?php
				}
			}*/
			?>
		</aside><!-- / .aside -->
	</section><!-- / .main section -->

<?php if (!in_array($this->wish->status, array(4, 2))) { ?>
	<section class="below section" id="section-comments">
		<div class="subject">
			<h3>
				<?php echo JText::_('COM_WISHLIST_COMMENTS');?> (<?php echo $this->wish->numreplies; ?>)
			</h3>
			<?php 
			if (isset($this->wish->replies)) 
			{
				$view = new JView(
					array(
						'name'    => 'wish',
						'layout'  => '_list'
					)
				);
				$view->parent     = 0;
				$view->cls        = 'odd';
				$view->depth      = 0;
				$view->option     = $this->option;
				$view->wish       = $this->wish;
				$view->comments   = $this->wish->replies;
				$view->listid     = $this->wishlist->id;
				$view->wishid     = $this->wish->id;
				$view->listcategory = $this->wishlist->category;
				$view->listreference = $this->wishlist->referenceid;
				$view->base       = 'index.php?option='.$this->option.'&task=reply&cat=wish&id='.$this->wishlist->id.'&refid='.$this->wish->id.'&wishid='.$this->wish->id;
				$view->config     = JComponentHelper::getParams('com_wishlist');
				$view->display();
			} 
			else 
			{ ?>
			<p>
				<?php echo JText::_('COM_WISHLIST_NO_COMMENTS'); ?> <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=reply&cat=wish&id='.$this->wishlist->id.'&refid='.$this->wish->id.'&wishid='.$this->wish->id); ?>"><?php echo JText::_('COM_WISHLIST_MAKE_A_COMMENT'); ?></a>.
			</p>
			<?php } ?>
		</div><!-- / .subject -->
		<div class="aside">
			<p>
				<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=reply&cat=wish&id='.$this->wishlist->id.'&refid='.$this->wish->id.'&wishid='.$this->wish->id.'#commentform');?>">
					<?php echo JText::_('COM_WISHLIST_ADD_A_COMMENT'); ?>
				</a>
			</p>
		</div><!-- / .aside -->
	</section><!-- / .below section -->

	<?php if (is_object($this->addcomment) && $this->addcomment->item_id == $this->wish->id) { ?>
		<section class="below section">
			<div class="subject">
				<form action="<?php echo JRoute::_('index.php?option='.$this->option); ?>" method="post" id="commentform" enctype="multipart/form-data">
					<h3>
						<?php echo JText::_('COM_WISHLIST_ACTION_ADD_COMMENT'); ?>
					</h3>
					<p class="comment-member-photo">
						<span class="comment-anchor"></span>
						<?php
							$jxuser = \Hubzero\User\Profile::getInstance($this->juser->get('id'));
						?>
						<img src="<?php echo $jxuser->getPicture(); ?>" alt="" />
					</p>
					<fieldset>
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="listid" value="<?php echo $this->wishlist->id; ?>" />
						<input type="hidden" name="wishid" value="<?php echo $this->wish->id; ?>" />
						<input type="hidden" name="task" value="savereply" />
						<input type="hidden" name="referenceid" value="<?php echo $this->wish->id; ?>" />
						<input type="hidden" name="cat" value="wish" />

						<input type="hidden" name="item_id" value="<?php echo $this->wish->id; ?>" />
						<input type="hidden" name="item_type" value="wish" />
						<input type="hidden" name="parent" value="" />

						<label for="comment<?php echo $this->wish->id; ?>">
							<?php echo JText::_('COM_WISHLIST_ENTER_COMMENTS'); ?>
							<?php
							echo JFactory::getEditor()->display('content', '', '', '', 35, 4, false, 'comment' . $this->wish->id, null, null, array('class' => 'minimal no-footer'));
							?>
						</label>

						<fieldset>
							<div class="grouping">
								<label>
									 <?php echo JText::_('COM_WISHLIST_ACTION_ATTACH_FILE'); ?>
									<input type="file" name="upload" />
								</label>
								<label>
									 <?php echo JText::_('COM_WISHLIST_ACTION_ATTACH_FILE_DESC'); ?>
									<input type="text" name="description" value="" />
								</label>
							</div>
						</fieldset>

						<label id="comment-anonymous-label">
							<input class="option" type="checkbox" name="anonymous" value="1" id="comment-anonymous" /> 
							<?php echo JText::_('COM_WISHLIST_POST_COMMENT_ANONYMOUSLY'); ?>
						</label>

						<p class="submit">
							<input type="submit" value="<?php echo JText::_('COM_WISHLIST_POST_COMMENT'); ?>" />
						</p>

						<div class="sidenote">
							<p>
								<strong><?php echo JText::_('COM_WISHLIST_COMMENT_KEEP_POLITE'); ?></strong>
							</p>
						</div>
					</fieldset>
				</form>
			</div><!-- / .subject -->
			<div class="aside">
			</div><!-- / .aside -->
		</section><!-- / .below section -->
	<?php } ?>

	<?php if ($this->admin) {  // let advisory committee view this too ?>
		<section class="below section" id="section-plan">
			<div class="subject" id="full_plan">
				<h3>
					<?php echo JText::_('COM_WISHLIST_IMPLEMENTATION_PLAN'); ?>
					<?php if ($this->wish->plan) { ?>
						(<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id.'&action=editplan#plan'); ?>"><?php echo JText::_('COM_WISHLIST_ACTION_EDIT'); ?></a>)
					<?php } else { ?>
						(<?php echo JText::_('COM_WISHLIST_PLAN_NOT_STARTED'); ?>)
					<?php } ?>
				</h3>
				<form action="index.php" method="post" id="planform" enctype="multipart/form-data">
					<p class="plan-member-photo">
						<span class="plan-anchor"></span>
						<img src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($this->juser, 0); ?>" alt="<?php echo JText::_('Member avatar'); ?>" />
					</p>
					<fieldset>
				<?php if ($this->wish->action=='editplan') { ?>
						<div class="grid">
							<div class="col span6">
								<label>
									<?php echo JText::_('COM_WISHLIST_WISH_ASSIGNED_TO'); ?>:
									<?php echo $this->wish->assignlist; ?>
								</label>
							</div>
							<div class="col span6 omega">
								<label for="publish_up" id="publish_up-label">
									<?php echo JText::_('COM_WISHLIST_DUE'); ?> (<?php echo JText::_('COM_WISHLIST_OPTIONAL'); ?>)
									<input class="option" type="text" name="publish_up" id="publish_up" size="10" maxlength="10" value="<?php echo $due ? JHTML::_('date', $this->wish->due, $dateFormat, $tz) : ''; ?>" />
								</label>
							</div>
						</div>

						<?php if ($this->wish->plan) { ?>
						<label class="newrev" for="create_revision">
							<input type="checkbox" class="option" name="create_revision" id="create_revision" value="1" />
							<?php echo JText::_('COM_WISHLIST_PLAN_NEW_REVISION'); ?>
						</label>
						<?php } else { ?>
						<input type="hidden" name="create_revision" value="0" />
						<?php } ?>
						<label>
							<?php echo JText::_('COM_WISHLIST_ACTION_INSERT_TEXT'); ?> 
							<?php
							$plan = new WishlistModelPlan($this->wish->plan);
							echo JFactory::getEditor()->display('pagetext', $this->escape($plan->content('raw')), '', '', 35, 40, false, 'pagetext', null, null, array('class' => 'minimal no-footer'));
							?>
						</label>

						<input type="hidden" name="pageid" value="<?php echo isset($this->wish->plan->id) ? $this->wish->plan->id : ''; ?>" />
						<input type="hidden" name="version" value="<?php echo isset($this->wish->plan->version) ? $this->wish->plan->version : 1; ?>" />
						<input type="hidden" name="wishid" value="<?php echo $this->wish->id; ?>" />
						<input type="hidden" name="option" value="'<?php echo $this->option; ?>" />
						<input type="hidden" name="created_by" value="<?php echo $this->juser->get('id'); ?>" />
						<input type="hidden" name="task" value="saveplan" />

						<p class="submit">
							<input type="submit" name="submit" value="<?php echo JText::_('COM_WISHLIST_SAVE'); ?>" />
							<span class="cancelaction">
								<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id); ?>">
								<?php echo JText::_('COM_WISHLIST_CANCEL'); ?></a>
							</span>
						</p>
						
						<div class="sidenote">
							<p>
								<?php echo JText::_('COM_WISHLIST_PLAN_FORMATTING_HELP'); ?>
							</p>
						</div>
				<?php } else if (!$this->wish->plan) { ?>
						<p>
							<?php echo JText::_('COM_WISHLIST_THERE_IS_NO_PLAN'); ?>
							<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id.'&action=editplan#plan'); ?>">
								<?php echo JText::_('COM_WISHLIST_START_PLAN'); ?>
							</a>.
						</p>
						<?php if ($this->wish->status==0 or $this->wish->status==6) { ?>
							<p>
								<?php echo JText::_('COM_WISHLIST_PLAN_IS_ASSIGNED'); ?> 
								<?php echo $assigned; ?>

								<?php echo JText::_('COM_WISHLIST_PLAN_IS_DUE'); ?> 
								<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id.'&action=editplan#plan'); ?>">
									<?php echo ($due) ? $due : JText::_('COM_WISHLIST_DUE_NEVER'); ?>
								</a>
							</p>
						<?php } ?>
				<?php } else { ?>
					<?php if ($this->wish->status==0 or $this->wish->status==6) { ?>
						<p>
							<?php echo JText::_('COM_WISHLIST_PLAN_IS_ASSIGNED'); ?> 
							<?php echo $assigned; ?>
							<?php echo JText::_('COM_WISHLIST_PLAN_IS_DUE'); ?>
							<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id.'&action=editplan#plan'); ?>">
								<?php echo ($due) ? $due : JText::_('COM_WISHLIST_DUE_NEVER'); ?>
							</a>.
						</p>
					<?php } ?>
						<div class="planbody">
							<p class="plannote">
								<?php echo JText::_('COM_WISHLIST_PLAN_LAST_EDIT').' '.JHTML::_('date', $this->wish->plan->created, JText::_('DATE_FORMAT_HZ1')).' at '.JHTML::_('date',$this->wish->plan->created, JText::_('TIME_FORMAT_HZ1')).' '.JText::_('by').' '.$this->wish->plan->authorname;?>
							</p>
							<?php
								$plan = new WishlistModelPlan($this->wish->plan);
								echo $plan->content('parsed');
							?>
						</div>
				<?php } ?>
					</fieldset>
				</form>
			</div><!-- / .subject -->
			<aside class="aside">
			<?php if ($this->wish->action != 'editplan') { ?>
				<p>
					<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$this->wish->id.'&action=editplan#plan'); ?>">
						<?php echo JText::_('COM_WISHLIST_ADD_TO_THE_PLAN'); ?>
					</a>
				</p>
			<?php } else { ?>
				<p><?php echo JText::_('COM_WISHLIST_PLAN_DEADLINE_EXPLANATION'); ?></p>
			<?php } ?>
			</aside><!-- / .aside -->
		</section><!-- / .below section -->
	<?php } // if ($this->admin) ?>

<?php } // if not withdrawn ?>

<?php } // end if not abusive ?>
<?php }	// end if not private	
	} else {
		// throw error, shouldn't be here
		echo '<p class="error">' . JText::_('COM_WISHLIST_ERROR_WISH_NOT_FOUND') . '</p>';
	}
