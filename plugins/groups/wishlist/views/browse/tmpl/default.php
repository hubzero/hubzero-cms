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
defined('_JEXEC') or die( 'Restricted access' );

if (!defined('n')) {

/**
 * Description for ''t''
 */
	define('t',"\t");

/**
 * Description for ''n''
 */
	define('n',"\n");

/**
 * Description for ''r''
 */
	define('r',"\r");

/**
 * Description for ''br''
 */
	define('br','<br />');

/**
 * Description for ''sp''
 */
	define('sp','&#160;');

/**
 * Description for ''a''
 */
	define('a','&amp;');
}

$title = ($this->admin) ? JText::_('WISHLIST_TITLE_PRIORITIZED') : JText::_('WISHLIST_TITLE_RECENT_WISHES');
if(count($this->wishlist->items) > 0 && $this->items > $this->filters['limit']) {
	$title .= ' <span>(<a href="'.JRoute::_('index.php?option=com_wishlist'.a.'task=wishlist'.a.'category='. $this->wishlist->category.a.'rid='.$this->wishlist->referenceid).'">'.JText::_('view all') .' '.$this->items.'</a>)</span>';
}
else {
	$title .= ' <span>('.$this->items.')</span>';
}
?>

<div class="main section">
	<div class="aside">
		<div class="container">
			<h4>Wish List Actions</h4>
			<?php if($this->admin != 0) { ?>
				<p class="add">
					<a href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'task=add'.a.'category='. $this->wishlist->category.a.'rid='.$this->wishlist->referenceid); ?>"><?php echo JText::_('ADD_NEW_WISH'); ?></a>
				</p>
			<?php } else { ?>
				<?php $return = JRoute::_('index.php?option=com_groups&gid='.$this->group->get('cn').'&active=wishlist'); ?>
				<?php if($this->juser->get('guest')) { ?>
					<p class="warning">You must <a href="/login?return=<?php echo base64_encode($return); ?>">log in</a> and be a group member to add a wish.</p>
				<?php } else { ?>
					<p class="warning">You must be a group member to add a wish.
				<?php } ?>
			<?php } ?>
		</div>
	</div><!-- /.aside -->
	
	<div class="subject">
		<div class="container">
			<table class="entries">
				<caption><?php echo $title; ?></caption>
				<tbody>
					<?php if($this->wishlist->items) { ?>
						<?php foreach($this->wishlist->items as $item) { ?>
							<?php
								$item->subject = stripslashes($item->subject);
								$item->subject = str_replace('&quote;','&quot;',$item->subject);
								$item->subject = htmlspecialchars($item->subject);
								$item->bonus = $this->config->get('banking') ? $item->bonus : 0;
								$name = $item->anonymous == 1 ? JText::_('ANONYMOUS') : $item->authorname;

								$cls  = "reg";
								$cls .= (isset($item->ranked) && !$item->ranked && $item->status!=1 && ($this->admin==2 or $this->admin==3)) ? " newish" : "";
								$cls .= ($item->private && $this->wishlist->public) ? " private" : "";
								$cls .= ($item->status==1) ? " grantedwish" : "";
							?>
							<tr class="<?php echo $cls; ?>">
								<td>
									<?php 
										$scls = 'outstanding';
										if(isset($item->ranked) && !$item->ranked && $item->status!=1 && $item->status!=3 && $item->status!=4 && ($this->admin==2 or $this->admin==3))  {
											$scls = 'unranked';
										} else if($item->status==1) {
											$scls = 'granted';
										}
									?>
									<!--
									<dl class="comment-details">
										<dt><span class="wish_<?php //echo $scls; ?>"></span><dt>
									</dl>
									-->
									<div class="ensemble_left wish_<?php echo $scls; ?>">
										<?php if(!$item->reports) { ?>
											<p class="wishcontent">
												<a href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'task=wish'.a.'category='.$this->wishlist->category.a.'rid='.$this->wishlist->referenceid.a.'wishid='.$item->id.a.'filterby='.$this->filters['filterby']); ?>" class="wishtitle" title="<?php echo htmlspecialchars(Hubzero_View_Helper_Html::xhtml($item->about)); ?>">
													<?php echo Hubzero_View_Helper_Html::shortenText($item->subject, 160, 0); ?>
												</a>
											</p>
											<p class="proposed">
												<?php echo JText::_('WISH_PROPOSED_BY').' '.$name.' '.JText::_('ON').' '.JHTML::_('date',$item->proposed, '%d %b %Y'); ?>, 
												<a href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'task=wish'.a.'category='.$this->wishlist->category.a.'rid='.$this->wishlist->referenceid.a.'wishid='.$item->id); ?>?com=1#comments">
													<?php echo $item->numreplies; ?>
													<span class="nobreak">
														<?php echo ($item->numreplies == 1) ? ' '.JText::_('COMMENT') : ' '.JText::_('COMMENTS'); ?>
													</span>
												</a>
												<?php
													if($this->admin && $this->admin != 3) {
														$assigned = $item->assignedto ? $item->assignedto : JText::_('UNKNOWN');
														echo ($item->assigned) ? '<br /> '.JText::_('WISH_ASSIGNED_TO').' '.$assigned : '';
													}
												?>
											</p>
										<?php } else { ?>
											<p class="warning adjust"><?php echo JText::_('NOTICE_POSTING_REPORTED'); ?></p>
										<?php } ?>
									</div><!-- /.ensemble_left -->
									
									<?php if(!$item->reports) { ?>
										<div class="ensemble_right">
											<?php //if(($this->admin or $item->status==1 or ($item->status==0 && $item->accepted==1) or $item->status==3 or $item->status==4) && !$item->reports) { ?>
												<div class="wishranking">
													<?php 
														if(($this->admin or $item->status==1 or ($item->status==0 && $item->accepted==1) or $item->status==3 or $item->status==4) && !$item->reports) {
															echo ($item->status==1) ?' <span class="special priority_number">'.JText::_('WISH_STATUS_GRANTED').'</span>': '';
													 		echo ($item->status==1 && $item->granted!='0000-00-00 00:00:00') ?' <span class="mini">'.strtolower(JText::_('ON')).' '.JHTML::_('date',$item->granted, '%d %b %y').'</span>': '';

															if(isset($item->ranked) && !$item->ranked && $item->status==0 && ($this->admin==2 or $this->admin==3)) {
																echo '<a class="rankit" href="index.php?option=com_wishlist'.a.'task=wish'.a.'category='.$this->wishlist->category.a.'rid='.$this->wishlist->referenceid.a.'wishid='.$item->id.a.'filterby='.$this->filters['filterby'].'">'.JText::_('WISH_RANK_THIS').'</a>';
															} else if(isset($item->ranked) && $item->ranked && $item->status==0) {
														 		echo '<span>'.JText::_('WISH_PRIORITY').': <span class="priority_number">'.$item->ranking.'</span></span>';
															}
															echo ($item->status==0 && $item->accepted==1) ? '<span class="special accepted">'.JText::_('WISH_STATUS_ACCEPTED').'</span>' : '';
															echo ($item->status==3) ? '<span class="special rejected">'.JText::_('WISH_STATUS_REJECTED').'</span>' : '';
															echo ($item->status==4) ? '<span class="special withdrawn">'.JText::_('WISH_STATUS_WITHDRAWN').'</span>' : '';
														}
													?>
												</div>
											<?php //} ?>
											
											<div id="wishlist_<?php echo $item->id; ?>" class="voting">
												<?php
													jimport('joomla.application.component.view');
													$view = new JView( array('name'=>'rateitem','base_path' => JPATH_ROOT.DS.'components'.DS.'com_wishlist') );
													$view->option = 'com_wishlist';
													$view->item = $item;
													$view->listid = $this->wishlist->id;
													$view->plugin = 1;
													$view->admin = 0;
													$view->page = 'wishlist';
													$view->filters = $this->filters;
													echo $view->loadTemplate();
												?>
											</div>
											
											<?php if($this->config->get('banking')) { ?>
												<div class="assign_bonus">	
													<?php if(isset($item->bonus) && $item->bonus > 0 && ($item->status==0 || $item->status==6)) { ?>
														<a class="bonus tooltips" href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'task=wish'.a.'category='.$this->wishlist->category.a.'rid='.$this->wishlist->referenceid.a.'wishid='.$item->id); ?>?action=addbonus#action" title="<?php echo JText::_('WISH_ADD_BONUS').' ::'.$item->bonusgivenby.' '.JText::_('MULTIPLE_USERS').' '.JText::_('WISH_BONUS_CONTRIBUTED_TOTAL').' '.$item->bonus.' '.JText::_('POINTS').' '.JText::_('WISH_BONUS_AS_BONUS'); ?>">+ <?php echo $item->bonus; ?></a>
													<?php } else if($item->status==0 || $item->status==6) { ?>
														<a class="nobonus tooltips" href="<?php echo JRoute::_('index.php?option=com_wishlist'.a.'task=wish'.a.'category='.$this->wishlist->category.a.'rid='.$this->wishlist->referenceid.a.'wishid='.$item->id); ?>?action=addbonus#action" title="<?php echo JText::_('WISH_ADD_BONUS').' :: '.JText::_('WISH_BONUS_NO_USERS_CONTRIBUTED'); ?>">&nbsp;</a>
													<?php } else { ?>
														<span class="bonus_inactive" title="<?php echo JText::_('WISH_BONUS_NOT_ACCEPTED'); ?>">&nbsp;</span>
													<?php } ?>
												</div>
											<?php } ?>
										</div>
									<?php } ?>
								</td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td><?php echo JText::_('WISHLIST_NO_WISHES_BE_FIRST'); ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div><!-- /.subject -->
</div><!-- /.main -->
