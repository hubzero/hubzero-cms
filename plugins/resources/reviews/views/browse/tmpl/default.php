<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$database =& JFactory::getDBO();
$juser =& JFactory::getUser();
$html = '';
?>
<h3>
	<a name="reviews"></a>
	<span><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->resource->id.'&active=reviews&action=addreview#reviewform'); ?>" class="add"><?php echo JText::_('PLG_RESOURCES_REVIEWS_WRITE_A_REVIEW'); ?></a></span>
	<?php echo JText::_('PLG_RESOURCES_REVIEWS'); ?>
</h3>
<?php
// Did we get any results back?
if ($this->reviews) {
	$wikiconfig = array(
		'option'   => $this->option,
		'scope'    => 'review',
		'pagename' => $this->resource->id,
		'pageid'   => $this->resource->id,
		'filepath' => '',
		'domain'   => '' 
	);
	ximport('Hubzero_Wiki_Parser');
	$parser =& Hubzero_Wiki_Parser::getInstance();
	
	$admin = false;
	// Check if they're a site admin (from Joomla)
	if ($juser->authorize($this->option, 'manage')) {
		$admin = true;
	}
	
	// Set the abuse flag
	// Determines if we're using abuse reports or not
	$abuse = false;
	if (is_file(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'tables'.DS.'reportabuse.php')) {
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'tables'.DS.'reportabuse.php' );
		$abuse = true;
	}
	
	// Set the reply flag
	// Determines if we're allowing replies to reviews
	$reply = false;
	if (is_file(JPATH_ROOT.DS.'libraries'.DS.'Hubzero'.DS.'Comment.php')) {
		include_once( JPATH_ROOT.DS.'libraries'.DS.'Hubzero'.DS.'Comment.php' );
		$reply = true;
	
		// See if we have a comment (comeone clicked a "reply" link)
		$addcomment = new Hubzero_Comment( $database );
		$addcomment->referenceid = JRequest::getInt( 'refid', 0 );
		$addcomment->category = JRequest::getVar( 'category', '' );
	}
	
	$o = 'even';

	$html = "\t".'<ol class="comments">'."\n";
	foreach ($this->reviews as $review)
	{
		// Get the rating
		switch ($review->rating) 
		{
			case 0.5: $class = ' half-stars';      break;
			case 1:   $class = ' one-stars';       break;
			case 1.5: $class = ' onehalf-stars';   break;
			case 2:   $class = ' two-stars';       break;
			case 2.5: $class = ' twohalf-stars';   break;
			case 3:   $class = ' three-stars';     break;
			case 3.5: $class = ' threehalf-stars'; break;
			case 4:   $class = ' four-stars';      break;
			case 4.5: $class = ' fourhalf-stars';  break;
			case 5:   $class = ' five-stars';      break;
			case 0:
			default:  $class = ' no-stars';      break;
		}

		// Set the name of the reviewer
		$name = JText::_('PLG_RESOURCES_REVIEWS_ANONYMOUS');
		$ruser = new Hubzero_User_Profile();
		$ruser->load( $review->user_id );
		if ($review->anonymous != 1) {
			$name = JText::_('PLG_RESOURCES_REVIEWS_UNKNOWN');
			//$ruser =& JUser::getInstance($review->user_id);
			if (is_object($ruser)) {
				$name = $ruser->get('name');
			}
		}
		
		$replies = null;
		if ($reply) {
			// Get the replies to this review
			$replies = plgResourcesReviews::getComments($review, 'review', 0, $abuse);
		}

		// Get abuse reports
		if ($abuse) {
			$abuse_reports = plgResourcesReviews::getAbuseReports($review->id, 'review');
		}
		
		$o = ($o == 'odd') ? 'even' : 'odd';

		// Build the list item
		$html .= "\t".'<li class="comment '.$o.'" id="c'.$review->id.'">';
		$html .= "\t\t".'<p class="comment-member-photo">'."\n";
		$html .= "\t\t".'	<span class="comment-anchor"><a name="c'.$review->id.'"></a></span>'."\n";
		$html .= "\t\t".'	<img src="'.plgResourcesReviews::getMemberPhoto($ruser, $review->anonymous).'" alt="" />'."\n";
		$html .= "\t\t".'</p><!-- / .comment-member-photo -->'."\n";
		$html .= "\t\t".'<div class="comment-content">'."\n";
		$html .= "\t\t\t".'<p><span class="avgrating'.$class.'"><span>'.JText::sprintf('PLG_RESOURCES_REVIEWS_OUT_OF_5_STARS',$review->rating).'</span></span></p>'."\n";
		$html .= "\t\t".'<p class="comment-title">'."\n";
		$html .= "\t\t".'	<strong>'. $name.'</strong> '."\n";
		$html .= "\t\t".'	<a class="permalink" href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->resource->id.'&active=reviews#c'.$review->id).'" title="'. JText::_('PLG_RESOURCES_REVIEWS_PERMALINK').'">@ <span class="time">'. JHTML::_('date',$review->created, '%I:%M %p', 0).'</span> on <span class="date">'.JHTML::_('date',$review->created, '%d %b, %Y', 0).'</span></a>'."\n";
		$html .= "\t\t".'</p><!-- / .comment-title -->'."\n";
		if ($abuse && $abuse_reports > 0) {
			$html .= "\t\t\t".'<p class="warning">'.JText::_('PLG_RESOURCES_REVIEWS_COMMENT_REPORTED_AS_ABUSIVE').'</p>';
			$html .= "\t\t".'</div>'."\n";
		} else {
			if ($review->comment) {
				if ($this->voting) {
					// Display thumbs voting
					$html .= "\t\t".'<p id="reviews_'.$review->id.'" class="'.$this->option.'">'."\n";
					//$html .= $this->rateitem($review, $juser, $this->option, $this->resource->id)."\n";
					$view = new Hubzero_Plugin_View(
						array(
							'folder'=>'resources',
							'element'=>'reviews',
							'name'=>'browse',
							'layout'=>'rateitem'
						)
					);
					$view->option = $this->option;
					$view->item = $review;
					$view->rid = $this->resource->id;
					
					$html .= $view->loadTemplate();
					$html .= "\t\t".'</p>'."\n";
				}
				$html .= $parser->parse(stripslashes($review->comment), $wikiconfig);
			} else {
				$html .= "\t\t\t".'<p class="comment-none">'.JText::_('PLG_RESOURCES_REVIEWS_NO_COMMENT').'</p>'."\n";
			}
			
			
			//if ((($abuse || $reply) && $review->comment) || $admin) {
			if (($abuse || $reply) || $admin) {
				$html .= "\t\t\t".'<p class="comment-options">'."\n";
				//if ($abuse && $review->comment) {
				if ($abuse) {
					$html .= "\t\t\t\t".'<a class="abuse" href="'.JRoute::_('index.php?option=com_support&task=reportabuse&category=review&id='.$review->id.'&parent='.$this->resource->id).'">'.JText::_('PLG_RESOURCES_REVIEWS_REPORT_ABUSE').'</a> | '."\n";
				}
				//if ($reply && $review->comment) {
				if ($reply) {
					$html .= "\t\t\t\t".'<a class="';
					//if (!$juser->get('guest')) {
						$html .= 'reply';
					//}
					$html .= '" href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->resource->id.'&active=reviews&action=reply&refid='.$review->id.'&category=review').'" id="rep_'.$review->id.'" title="'.JText::sprintf('PLG_RESOURCES_REVIEWS_REPLY_TO_USER', $name).'"">'.JText::_('PLG_RESOURCES_REVIEWS_REPLY').'</a>'."\n";
				}
				if ($admin) {
					$html .= "\t\t\t\t".' | <a class="deletereview" href="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->resource->id.'&active=reviews&action=deletereview&reviewid='.$review->id).'">'.JText::_('PLG_RESOURCES_REVIEWS_DELETE').'</a>'."\n";
				}
				$html .= "\t\t\t".'</p>'."\n";
			}
			
			// Add the comment form
			if ($reply) {
				//$html .= $this->addcomment($review->id, 0, $juser, $this->option, $addcomment, $this->resource->id);
				$view = new Hubzero_Plugin_View(
					array(
						'folder'=>'resources',
						'element'=>'reviews',
						'name'=>'browse',
						'layout'=>'addcomment'
					)
				);
				$view->option = $this->option;
				$view->resource = $this->resource;
				$view->row = $review;
				$view->juser = $juser;
				$view->level = 0;
				$view->addcomment = $addcomment;
				
				$html .= $view->loadTemplate();
			}
			$html .= "\t\t".'</div>'."\n";
			
			// Display comments
			if ($replies) {
				//$html .= $this->comments($replies, $review->id, $juser, $this->resource->id, $this->option, $addcomment, $abuse, $admin)."\n";
				$html .= "\t\t".'<ol class="comments pass2">'."\n";
				foreach ($replies as $reply) 
				{
					$o = ($o == 'odd') ? 'even' : 'odd';

					// Comment
					$html .= "\t\t\t".'<li class="comment '.$o;
					if ($abuse && $reply->abuse_reports > 0) {
						$html .= ' abusive';
					}
					$html .= '" id="c'.$reply->id.'r">';
					//$html .= $this->comment($reply, $juser, $this->option, $this->resource->id, $addcomment, 1, $abuse, $o, $admin)."\n";
					$view = new Hubzero_Plugin_View(
						array(
							'folder'=>'resources',
							'element'=>'reviews',
							'name'=>'browse',
							'layout'=>'comment'
						)
					);
					$view->option = $this->option;
					$view->reply = $reply;
					$view->juser = $juser;
					$view->id = $this->resource->id;
					$view->level = 1;
					$view->abuse = $abuse;
					$view->resource = $this->resource;
					$view->addcomment = $addcomment;
					$view->parser = $parser;
					$html .= $view->loadTemplate();
					// Another level? 
					if (count($reply->replies) > 0) {
						$html .= "\t\t\t".'<ol class="comments pass3">'."\n";
						foreach ($reply->replies as $r) 
						{
							$o = ($o == 'odd') ? 'even' : 'odd';

							$html .= "\t\t\t\t".'<li class="comment '.$o;
							if ($abuse && $r->abuse_reports > 0) {
								$html .= ' abusive';
							}
							$html .= '" id="c'.$r->id.'r">';
							//$html .= $this->comment($r, $juser, $this->option, $this->resource->id, $addcomment, 2, $abuse, $o, $admin)."\n";
							$view = new Hubzero_Plugin_View(
								array(
									'folder'=>'resources',
									'element'=>'reviews',
									'name'=>'browse',
									'layout'=>'comment'
								)
							);
							$view->option = $this->option;
							$view->reply = $r;
							$view->juser = $juser;
							$view->id = $this->resource->id;
							$view->level = 2;
							$view->abuse = $abuse;
							$view->resource = $this->resource;
							$view->addcomment = $addcomment;
							$view->parser = $parser;
							$html .= $view->loadTemplate();
							
							// Yet another level?? 
							if (count($r->replies) > 0) {
								$html .= "\t\t\t".'<ol class="comments pass4">'."\n";
								foreach ($r->replies as $rr) 
								{
									$o = ($o == 'odd') ? 'even' : 'odd';

									$html .= "\t\t\t\t".'<li class="comment '.$o;
									if ($abuse && $rr->abuse_reports > 0) {
										$html .= ' abusive';
									}
									$html .= '" id="c'.$rr->id.'r">';
									//$html .= $this->comment($rr, $juser, $this->option, $this->resource->id, $addcomment, 3, $abuse, $o, $admin)."\n";
									$view = new Hubzero_Plugin_View(
										array(
											'folder'=>'resources',
											'element'=>'reviews',
											'name'=>'browse',
											'layout'=>'comment'
										)
									);
									$view->option = $this->option;
									$view->reply = $rr;
									$view->juser = $juser;
									$view->id = $this->resource->id;
									$view->level = 3;
									$view->abuse = $abuse;
									$view->resource = $this->resource;
									$view->addcomment = $addcomment;
									$view->parser = $parser;
									$html .= $view->loadTemplate();
									
									$html .= "\t\t\t\t".'</li>'."\n";
								}
								$html .= "\t\t\t".'</ol><!-- end pass4 -->'."\n";
							}
							$html .= "\t\t\t".'</li>'."\n";
						}
						$html .= "\t\t\t".'</ol><!-- end pass3 -->'."\n";
					}
					$html .= "\t\t".'</li>'."\n";
				}
				$html .= "\t\t".'</ol><!-- end pass2 -->'."\n";
			}
		}
		$html .= "\t".'</li>'."\n";
	}
	$html .= "\t".'</ol>'."\n";
} else {
	$html = "\t".'<p>'.JText::_('PLG_RESOURCES_REVIEWS_NO_REVIEWS_FOUND').'</p>'."\n";
}
echo $html;
if ($this->getError()) { ?>
	<p class="warning"><?php echo $this->getError(); ?></p>
<?php }

// Display the review form if needed
if (!$juser->get('guest')) {
	$myreview = $this->h->myreview;
	if (is_object($myreview)) {
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'resources',
				'element'=>'reviews',
				'name'=>'review'
			)
		);
		$view->option = $this->option;
		$view->review = $this->h->myreview;
		$view->banking = $this->banking;
		$view->infolink = $this->infolink;
		$view->resource = $this->resource;
		$view->juser = $juser;
		$view->display();
	}
}
?>
