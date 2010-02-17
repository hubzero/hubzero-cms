<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_resources_reviews' );
	
//-----------

class plgResourcesReviews extends JPlugin
{
	function plgResourcesReviews(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'reviews' );
		$this->_params = new JParameter( $this->_plugin->params );
		
		
		$this->infolink = '/kb/points/';
		$upconfig =& JComponentHelper::getParams( 'com_userpoints' );
		$this->banking = $upconfig->get('bankAccounts');
	}
	
	//-----------
	
	function &onResourcesAreas( $resource )
	{
		$areas = array(
			'reviews' => JText::_('REVIEWS')
		);
		return $areas;
	}

	//-----------

	function onResources( $resource, $option, $areas, $rtrn='all' )
	{
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onResourcesAreas( $resource ) ) 
			&& !array_intersect( $areas, array_keys( $this->onResourcesAreas( $resource ) ) )) {
				$rtrn = 'metadata';
			}
		}
		
		$database =& JFactory::getDBO();

		// Instantiate a helper object and perform any needed actions
		$h = new PlgResourcesReviewsHelper();
		$h->resource = $resource;
		$h->option = $option;
		$h->_option = $option;
		$h->execute();

		// Get reviews for this resource
		$r = new ResourcesReview( $database );
		$reviews = $r->getRatings( $resource->id );

		$out = '';
		if ($rtrn == 'all' || $rtrn == 'html') {
			$html = '';
			$juser =& JFactory::getUser();

			// Did we get any results back?
			if ($reviews) {
				$admin = false;
				// Check if they're a site admin (from Joomla)
				if ($juser->authorize($option, 'manage')) {
					$admin = true;
				}
				
				$voting = $this->_params->get('voting');
				if ($voting) {
					// Thumbs voting CSS & JS
					$this->getStyles('com_answers', 'vote.css');
				}
				
				// Set the abuse flag
				// Determines if we're using abuse reports or not
				$abuse = false;
				if (is_file(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'support.reportabuse.php')) {
					include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'support.reportabuse.php' );
					$abuse = true;
				}
				
				// Set the reply flag
				// Determines if we're allowing replies to reviews
				$reply = false;
				if (is_file(JPATH_ROOT.DS.'plugins'.DS.'xhub'.DS.'xlibraries'.DS.'xcomment.php')) {
					include_once( JPATH_ROOT.DS.'plugins'.DS.'xhub'.DS.'xlibraries'.DS.'xcomment.php' );
					$reply = true;
				
					// See if we have a comment (comeone clicked a "reply" link)
					$addcomment = new XComment( $database );
					$addcomment->referenceid = JRequest::getInt( 'refid', 0 );
					$addcomment->category = JRequest::getVar( 'category', '' );
				}
				
				$o = 'even';

				$html .= t.'<ol class="comments">'.n;
				foreach ($reviews as $review)
				{
					// Get the tags
					//$t = new ResourcesTags( $database );
					//$tags = $t->getTags( $resource->id, $review->user_id );

					// Get the rating
					$class = ResourcesHtml::getRatingClass($review->rating);

					// Set the name of the reviewer
					$name = JText::_('REVIEWS_ANONYMOUS');
					if ($review->anonymous != 1) {
						$name = JText::_('REVIEWS_UNKNOWN');
						$ruser =& JUser::getInstance($review->user_id);
						if (is_object($ruser)) {
							$name = $ruser->get('name');
						}
					}
					
					$replies = null;
					if ($reply) {
						// Get the replies to this review
						$replies = $this->getComments($review, 'review', 0, $abuse);
					}

					// Get abuse reports
					if ($abuse) {
						$abuse_reports = $this->getAbuseReports($review->id, 'review');
					}
					
					$o = ($o == 'odd') ? 'even' : 'odd';

					// Build the list item
					$html .= t.'<li class="comment '.$o.'" id="c'.$review->id.'">';
					$html .= t.t.'<dl class="comment-details">'.n;
					$html .= t.t.t.'<dt class="type"><span class="avgrating'.$class.'"><span>'.JText::sprintf('OUT_OF_5_STARS',$review->rating).'</span></span></dt>'.n;
					$html .= t.t.t.'<dd class="date">'.JHTML::_('date',$review->created, '%d %b %Y').'</dd>'.n;
					$html .= t.t.t.'<dd class="time">'.JHTML::_('date',$review->created, '%I:%M %p').'</dd>'.n;
					$html .= t.t.'</dl>'.n;
					$html .= t.t.'<div class="cwrap">'.n;
					$html .= t.t.t.'<p class="name"><strong>'.$name.'</strong> '.JText::_('SAID').':</p>'.n;
					
					if ($abuse && $abuse_reports > 0) {
						$html .= t.t.t.ResourcesHtml::warning( JText::_('COMMENT_REPORTED_AS_ABUSIVE') ).n;
						$html .= t.t.'</div>'.n;
					} else {
						
						if ($review->comment) {
							if ($voting) {
								// Display thumbs voting
								$html .= t.t.'<p id="reviews_'.$review->id.'" class="'.$option.'">'.n;
								$html .= $this->rateitem($review, $juser, $option, $resource->id).n;					
								$html .= t.t.'</p>'.n;
							} else {
								$html .= t.t.t.'<p>'.stripslashes($review->comment).'</p>'.n;
							}
						} else {
							$html .= t.t.t.'<p>'.JText::_('NO_COMMENT').'</p>'.n;
						}
						
						/*if ($tags) {
							$html .= t.t.t.'<p>'.JText::_('REVIEW_TAGS').'</p>'.n;
							$html .= $this->tagcloud( $tags );
						}*/
						
						//$html .= t.t.t.'<p id="review_'.$review->id.'" class="condensed">';
						//$html .= $this->ratereview($review, $juser, $juser, $option, $resource->id);					
						//$html .= t.t.t.'</p>'.n;
						
						if ((($abuse || $reply) && $review->comment) || $admin) {
							$html .= t.t.t.'<p class="comment-options">'.n;
							if ($abuse && $review->comment) {
								$html .= t.t.t.t.'<a class="abuse" href="'.JRoute::_('index.php?option=com_support'.a.'task=reportabuse'.a.'category=review'.a.'id='.$review->id.a.'parent='.$resource->id).'">'.JText::_('REPORT_ABUSE').'</a> | '.n;
							}
							if ($reply && $review->comment) {
								$html .= t.t.t.t.'<a class="';
								if (!$juser->get('guest')) {
									$html .= 'reply';
								}
								$html .= '" href="'.JRoute::_('index.php?option='.$option.a.'id='.$resource->id.a.'active=reviews'.a.'action=reply'.a.'refid='.$review->id.a.'category=review').'" id="rep_'.$review->id.'">'.JText::sprintf('REPLY_TO_USER', $name).'</a>'.n;
							}
							if ($admin) {
								$html .= t.t.t.t.' | <a class="deletereview" href="'.JRoute::_('index.php?option='.$option.a.'id='.$resource->id.a.'active=reviews'.a.'action=deletereview'.a.'reviewid='.$review->id).'">'.JText::_('DELETE').'</a>'.n;
							}
							$html .= t.t.t.'</p>'.n;
						}
											
						
						// Add the comment form
						if ($reply) {
							$html .= $this->addcomment($review->id, 0, $juser, $option, $addcomment, $resource->id);
						}
						$html .= t.t.'</div>'.n;
						
											
						// Display comments
						if ($replies) {
							$html .= $this->comments($replies, $review->id, $juser, $resource->id, $option, $addcomment, $abuse, $admin).n;
						}
					}
					$html .= t.'</li>'.n;
				}
				$html .= t.'</ol>'.n;
			} else {
				$html .= t.'<p>'.JText::_('NO_REVIEWS_FOUND').'</p>'.n;
			}

			// Display any errors
			if ($h->getError()) {
				$html .= ResourcesHtml::error( $h->getError() );
			}

			// Display the review form if needed
			$juser =& JFactory::getUser();
			if ($juser->get('guest')) {
				//$html .= ResourcesHtml::warning( JText::_('REVIEWS_LOGIN_NOTICE') );
			} else {
				$myreview = $h->myreview;
				if (is_object($myreview)) {
					$html .= PlgResourcesReviewsHelper::reviewForm( $h->myreview, $option );
				}
			}
			
			$out  = ResourcesHtml::hed(3,'<span><a href="'.JRoute::_('index.php?option='.$option.a.'id='.$resource->id.a.'active=reviews'.a.'action=addreview#reviewform').'"  class="add">'.JText::_('WRITE_A_REVIEW').'</a></span><a name="reviews"></a>'.JText::_('REVIEWS')).n;
			
			// Did they perform an action?
			// If so, they need to be logged in first.
			if (!$h->loggedin) {
				ximport('xmodule');

				$html  = ResourcesHtml::warning( JText::_('RESOURCES_LOGIN_NOTICE') );
				$html .= XModuleHelper::renderModules('force_mod');
				
				$out = $html;
			} else {
				// Build the final HTML
				/*$out .= ResourcesHtml::aside(
						'<p>'.JText::_('REVIEWS_EXPLANATION').'</p>'.
						'<p class="add"><a href="'.JRoute::_('index.php?option='.$option.a.'id='.$resource->id.a.'active=reviews'.a.'action=addreview#reviewform').'">'.JText::_('WRITE_A_REVIEW').'</a></p>'
					);*/
				//$out .= ResourcesHtml::subject($html
				$out .= $html;
			}
		}
		
		// Build the HTML meant for the "about" tab's metadata overview
		$metadata = '';
		if ($rtrn == 'all' || $rtrn == 'metadata') {
			if ($resource->alias) {
				$url = JRoute::_('index.php?option='.$option.a.'alias='.$resource->alias.a.'active=reviews');
				$url2 = JRoute::_('index.php?option='.$option.a.'alias='.$resource->alias.a.'active=reviews'.a.'action=addreview#reviewform');
			} else {
				$url = JRoute::_('index.php?option='.$option.a.'id='.$resource->id.a.'active=reviews');
				$url2 = JRoute::_('index.php?option='.$option.a.'id='.$resource->id.a.'active=reviews'.a.'action=addreview#reviewform');
			}
			
			$metadata  = '<p class="review"><a href="'.$url.'">'.JText::sprintf('NUM_REVIEWS',count($reviews));
			$metadata .= '</a> (<a href="'.$url2.'">'.JText::_('REVIEW_THIS').'</a>)</p>'.n;
		}

		$arr = array(
				'html'=>$out,
				'metadata'=>$metadata
			);

		return $arr;
	}
	//-----------

	private function getStyles($option='', $css='')
	{
		ximport('xdocument');
		if ($option) {
			XDocument::addComponentStylesheet($option, $css);
		} else {
			XDocument::addComponentStylesheet($this->_option);
		}

	}
	
	//-----------
	
	function getComments($item, $category, $level, $abuse=false)
	{
		$database =& JFactory::getDBO();
		
		$level++;

		$hc = new XComment( $database );
		$comments = $hc->getResults( array('id'=>$item->id, 'category'=>$category) );
		
		if ($comments) {
			foreach ($comments as $comment) 
			{
				$comment->replies = $this->getComments($comment, 'reviewcomment', $level, $abuse);
				if ($abuse) {
					$comment->abuse_reports = $this->getAbuseReports($comment->id, 'reviewcomment');
				}
			}
		}
		return $comments;
	}
	
	//-----------
	
	function getAbuseReports($item, $category)
	{
		$database =& JFactory::getDBO();

		$ra = new ReportAbuse( $database );
		return $ra->getCount( array('id'=>$item, 'category'=>$category) );
	}
	
	//-----------

	function comments($replies, $revid, $juser, $id, $option, $addcomment, $abuse=false, $admin=false) 
	{
		$o = 'even';
		
		$html = '';
		if (count($replies) > 0) {
			$html .= t.t.t.'<ol class="comments pass2">'.n;
			foreach ($replies as $reply) 
			{
				$o = ($o == 'odd') ? 'even' : 'odd';
				
				// Comment
				$html .= t.'<li class="comment '.$o;
				if ($abuse && $reply->abuse_reports > 0) {
					$html .= ' abusive';
				}
				$html .= '" id="c'.$reply->id.'r">';
				$html .= $this->comment($reply, $juser, $option, $id, $addcomment, 1, $abuse, $o, $admin).n;
				// Another level? 
				if (count($reply->replies) > 0) {
					$html .= t.t.t.'<ol class="comments pass3">'.n;
					foreach ($reply->replies as $r) 
					{
						$o = ($o == 'odd') ? 'even' : 'odd';
						
						$html .= t.'<li class="comment '.$o;
						if ($abuse && $r->abuse_reports > 0) {
							$html .= ' abusive';
						}
						$html .= '" id="c'.$r->id.'r">';
						$html .= $this->comment($r, $juser, $option, $id, $addcomment, 2, $abuse, $o, $admin).n;
		
						// Yet another level?? 
						if (count($r->replies) > 0) {
							$html .= t.t.t.'<ol class="comments pass4">'.n;
							foreach ($r->replies as $rr) 
							{
								$o = ($o == 'odd') ? 'even' : 'odd';
								
								$html .= t.'<li class="comment '.$o;
								if ($abuse && $rr->abuse_reports > 0) {
									$html .= ' abusive';
								}
								$html .= '" id="c'.$rr->id.'r">';
								$html .= $this->comment($rr, $juser, $option, $id, $addcomment, 3, $abuse, $o, $admin).n;
								$html .= t.'</li>'.n;
							}
							$html .= t.t.t.'</ol><!-- end pass4 -->'.n;
						}
						$html .= t.'</li>'.n;
					}
					$html .= t.t.t.'</ol><!-- end pass3 -->'.n;
				}
				$html .= t.'</li>'.n;
			}
			$html .= t.t.t.'</ol><!-- end pass2 -->'.n;
		}
		return $html;
	
	}
	
	//-----------

	function comment($reply, $juser, $option, $id, $addcomment, $level, $abuse, $o='', $admin=false) 
	{
		// Set the name of the reviewer
		$name = JText::_('REVIEWS_ANONYMOUS');
		if ($reply->anonymous != 1) {
			$name = JText::_('REVIEWS_UNKNOWN');
			$ruser =& JUser::getInstance($reply->added_by);
			if (is_object($ruser)) {
				$name = $ruser->get('name');
			}
		}
		
		/*$html  = t.'<li class="comment '.$o;
		if ($abuse && $reply->abuse_reports > 0) {
			$html .= ' abusive';
		}
		$html .= '" id="c'.$reply->id.'r">';*/
		$html  = t.t.'<dl class="comment-details">'.n;
		$html .= t.t.t.'<dt class="type"><span class="plaincomment"><span>'.JText::sprintf('COMMENT').'</span></span></dt>'.n;
		$html .= t.t.t.'<dd class="date">'.JHTML::_('date',$reply->added, '%d %b %Y').'</dd>'.n;
		$html .= t.t.t.'<dd class="time">'.JHTML::_('date',$reply->added, '%I:%M %p').'</dd>'.n;
		$html .= t.t.'</dl>'.n;
		$html .= t.t.'<div class="cwrap">'.n;
		$html .= t.t.t.'<p class="name"><strong>'.$name.'</strong> '.JText::_('SAID').':</p>'.n;

		if ($abuse && $reply->abuse_reports > 0) {
			$html .= t.t.t.ResourcesHtml::warning( JText::_('COMMENT_REPORTED_AS_ABUSIVE') ).n;
		} else {
			// Add the comment
			if ($reply->comment) {
				$html .= t.t.t.'<p>'.stripslashes($reply->comment).'</p>'.n;
			} else {
				$html .= t.t.t.'<p>'.JText::_('NO_COMMENT').'</p>'.n;
			}
			
			$html .= t.t.t.'<p class="comment-options">'.n;
			// Add the "report abuse" link if the abuse component exist
			if ($abuse) {
				$html .= t.t.t.t.'<a class="abuse" href="'.JRoute::_('index.php?option=com_support'.a.'task=reportabuse'.a.'category=comment'.a.'id='.$reply->id.a.'parent='.$id).'">'.JText::_('REPORT_ABUSE').'</a> | '.n;
			}
			// Cannot reply at third level
			if ($level < 3) {
				$html .= t.t.t.t.'<a class="';
				if (!$juser->get('guest')) {
					$html .= 'reply';
				}
				$html .= '" href="'.JRoute::_('index.php?option='.$option.a.'id='.$id.a.'active=reviews'.a.'action=reply'.a.'category=reviewcomment'.a.'refid='.$reply->id).'" id="rep_'.$reply->id.'">'.JText::sprintf('REPLY_TO_USER', $name).'</a>'.n;
			}
			if ($admin) {
				$html .= t.t.t.t.' | <a class="deletereview" href="'.JRoute::_('index.php?option='.$option.a.'id='.$id.a.'active=reviews'.a.'action=deletereply'.a.'refid='.$reply->id).'">'.JText::_('DELETE').'</a>'.n;
			}
			$html .= t.t.t.'</p>'.n;
			
			// Add the reply form if needed
			if ($level < 3 && !$juser->get('guest')) {
				$html .= $this->addcomment($reply->id, $level, $juser, $option, $addcomment, $id);
			}
		}
		
		$html .= t.t.'</div>'.n;
		//$html .= t.'</li>'.n;
		
		return $html;
	}
	
	//-----------
	
	function addcomment($refid, $level, $juser, $option, $addcomment, $id) 
	{
		$html = '';
		if (!$juser->get('guest')) {
			$category = ($level==0) ? 'review': 'reviewcomment';
			
			$class = ' hide';
			if (is_object($addcomment)) {
				$class = ($addcomment->referenceid == $refid && $addcomment->category==$category) ? '' : ' hide';
			}
			
			$html .= t.t.t.'<div class="addcomment'.$class.'">'.n;
			$html .= t.t.t.t.'<form action="index.php" method="post" id="commentform_'.$refid.'">'.n;
			$html .= t.t.t.t.t.'<fieldset>'.n;
			$html .= t.t.t.t.t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
			$html .= t.t.t.t.t.t.'<input type="hidden" name="rid" value="'. $id .'" />'.n;
			//$html .= t.t.t.t.t.t.'<input type="hidden" name="task" value="savereply" />'.n;
			$html .= t.t.t.t.t.t.'<input type="hidden" name="active" value="reviews" />'.n;
			$html .= t.t.t.t.t.t.'<input type="hidden" name="action" value="savereply" />'.n;
			$html .= t.t.t.t.t.t.'<input type="hidden" name="referenceid" value="'.$refid.'" />'.n;
			$html .= t.t.t.t.t.t.'<input type="hidden" name="category" value="'.$category.'" />'.n;
			$html .= t.t.t.t.t.t.'<label><input class="option" type="checkbox" name="anonymous" value="1" /> '.JText::_('POST_COMMENT_ANONYMOUSLY').'</label>'.n;
			$html .= t.t.t.t.t.t.'<label><textarea name="comment" rows="4" cols="50" class="commentarea">'.JText::_('ENTER_COMMENTS').'</textarea></label>'.n;
			$html .= t.t.t.t.t.'</fieldset>'.n;
			$html .= t.t.t.t.t.'<p><input type="submit" value="'.JText::_('POST_COMMENT').'" /> <a href="'.JRoute::_('index.php?option='.$option.a.'id='.$id.a.'active=reviews').'" class="cancelreply">'.JText::_('CANCEL').'</a></p>'.n;
			$html .= t.t.t.t.'</form>'.n;
			$html .= t.t.t.'</div>'.n;
		}
		
		return $html;
	}
	
	//-----------
	
	public function rateitem($item, $juser, $option, $id) 
	{
		$pclass = (isset($item->vote) && $item->vote=="yes") ? 'yes' : 'zero';
		$nclass = (isset($item->vote) && $item->vote=="no") ? 'no' : 'zero';
		$item->helpful = ($item->helpful > 0) ? '+'.$item->helpful: '&nbsp;&nbsp;'.$item->helpful;
		$item->nothelpful = ($item->nothelpful > 0) ? '-'.$item->nothelpful: '&nbsp;&nbsp;'.$item->nothelpful;
		
		$html  = n.t.t.t.'<span class="thumbsvote" id="rev'.$item->id.'_'.$id.'">'.n;
		$html .= t.t.t.t.'<span class="'.$pclass.'">'.$item->helpful.'</span>'.n;
			
		if ($juser->get('guest')) {
			$html .= t.t.t.t.'<span class="gooditem r_disabled"><a href="'.JRoute::_('index.php?option='.$option.a.'task=reviews'.a.'id='.$id.a.'action=rateitem'.a.'refid='.$item->id.a.'vote=yes').'" >&nbsp;</a></span>'.n;
			$html .= t.t.t.t.'<span class="'.$nclass.'">'.$item->nothelpful.'</span>'.n;
			$html .= t.t.t.t.'<span class="baditem r_disabled"><a href="'.JRoute::_('index.php?option='.$option.a.'task=reviews'.a.'id='.$id.a.'action=rateitem'.a.'refid='.$item->id.a.'vote=no').'" >&nbsp;</a></span>'.n;	
			$html .= t.t.t.t.'<span class="votinghints"><span>Login to vote</span></span>'.n;				
		} else {					
			$html .= t.t.t.t.'<span class="gooditem">'.n;
			if ($item->vote && $item->vote=="no" or  $juser->get('username') == $item->user_id) {
				$html .= t.t.t.t.'<span class="dis">&nbsp;</span>'.n;
			} else if ($item->vote) {
				$html .= t.t.t.t.'<span>&nbsp;</span>'.n;
			} else {
				$html .= t.t.t.t.t.'<a href="'.JRoute::_('index.php?option='.$option.a.'task=reviews'.a.'id='.$id.a.'action=rateitem'.a.'refid='.$item->id.a.'vote=yes').'" class="revvote" title="'.JText::_('THIS_HELPFUL').'">&nbsp;</a>'.n;
			}
			$html .= t.t.t.t.'</span>'.n;
			$html .= t.t.t.t.'<span class="'.$nclass.'">'.$item->nothelpful.'</span>'.n;
			$html .= t.t.t.t.'<span class="baditem">'.n;
			if ($item->vote && $item->vote=="yes" or $juser->get('username') == $item->user_id) {
				$html .= t.t.t.t.'<span class="dis">&nbsp;</span>'.n;
			} else if($item->vote) {
				$html .= t.t.t.'<span>&nbsp;</span>'.n;
			} else {
				$html .= t.t.t.t.t.'<a href="'.JRoute::_('index.php?option='.$option.a.'task=reviews'.a.'id='.$id.a.'action=rateitem'.a.'refid='.$item->id.a.'vote=no').'" class="revvote" title="'.JText::_('THIS_NOT_HELPFUL').'">&nbsp;</a>'.n;
			}
			$html .= t.t.t.t.'</span>'.n;
			$html .= t.t.t.t.'<span class="votinghints"><span></span></span>'.n;
		}
				
		$html .= t.t.t.'</span>'.n;
		$html .= t.t.t.'<span class="itemtxt">'.stripslashes($item->comment).'</span>'.n;
		
		return $html;
	}
}

//-----------

class PlgResourcesReviewsHelper extends JObject
{
	private $_data  = array();
	
	//-----------

	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}
	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message, $this->_messageType );
		}
	}
	
	//-----------
	
	public function execute()
	{
		// Incoming action
		$action = JRequest::getVar( 'action', '' );

		$this->loggedin = true;
		
		if ($action) {
			// Check the user's logged-in status
			$juser =& JFactory::getUser();
			if ($juser->get('guest')) {
				$this->loggedin = false;
				return;
			}
		}
		
		// Perform an action
		switch ( $action ) 
		{
			case 'addreview':    $this->editreview();   break;
			case 'editreview':   $this->editreview();   break;
			case 'savereview':   $this->savereview();   break;
			case 'deletereview': $this->deletereview(); break;		
			case 'savereply': 	 $this->savereply(); 	break;
			case 'deletereply':  $this->deletereply();  break;	
			case 'rateitem':   	 $this->rateitem();  	break;
		}
	}
	
	//-----------
	
	private function savereply()
	{	
		$juser =& JFactory::getUser();
		
		// Is the user logged in?
		if ($juser->get('guest')) {
			$this->setError( JText::_('RESOURCES_LOGIN_NOTICE') );
			return;
		}
		
		// Incoming
		$id       = JRequest::getInt('referenceid', 0 );
		$rid      = JRequest::getInt('rid', 0 );
		$category = JRequest::getVar('category', '' );
		$when     = date( 'Y-m-d H:i:s');
		
		// Trim and addslashes all posted items
		$_POST = array_map('trim',$_POST);
		
		if (!$id) {
			// Cannot proceed
			$this->setError( JText::_('COMMENT_ERROR_NO_REFERENCE_ID') );
			return;
		}
		
		if (!$category) {
			// Cannot proceed
			$this->setError( JText::_('COMMENT_ERROR_NO_CATEGORY') );
			return;
		}
		
		$database =& JFactory::getDBO();
		ximport( 'xcomment' );
			
		$row = new XComment( $database );
		if (!$row->bind( $_POST )) {
			echo ResourcesHtml::alert( $row->getError() );
			exit();
		}
			
		// Perform some text cleaning, etc.
		$row->comment   = $this->purifyText($row->comment);
		$row->comment   = nl2br($row->comment);
		$row->anonymous = ($row->anonymous == 1 || $row->anonymous == '1') ? $row->anonymous : 0;
		$row->added   	= $when;
		$row->state     = 0;
		$row->added_by 	= $juser->get('id');
			
		// Check for missing (required) fields
		if (!$row->check()) {
			echo ResourcesHtml::alert( $row->getError() );
			exit();
		}
		// Save the data
		if (!$row->store()) {
			echo ResourcesHtml::alert( $row->getError() );
			exit();
		}
	}
	
	//-----------
	
	public function deletereply()
	{
		$database =& JFactory::getDBO();
		$resource =& $this->resource;
		
		// Incoming
		$replyid = JRequest::getInt( 'refid', 0 );
		
		// Do we have a review ID?
		if (!$replyid) {
			echo ResourcesHtml::error( JText::_('COMMENT_ERROR_NO_REFERENCE_ID') );
			return;
		}
		
		// Do we have a resource ID?
		if (!$resource->id) {
			echo ResourcesHtml::error( JText::_('REVIEWS_NO_RESOURCE_ID') );
			return;
		}
		
		// Delete the review
		ximport( 'xcomment' );
		$reply = new XComment( $database );
		
		$comments = $reply->getResults( array('id'=>$replyid, 'category'=>'reviewcomment') );
		if (count($comments) > 0) {
			foreach ($comments as $comment) 
			{
				$reply->delete( $comment->id );
			}
		}
		$reply->delete( $replyid );
	}
	
	//-----------
	
	public function rateitem()
	{		
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
				
		$id   = JRequest::getInt( 'refid', 0 );
		$ajax = JRequest::getInt( 'ajax', 0 );
		$cat  = JRequest::getVar( 'category', 'review' );
		$vote = JRequest::getVar( 'vote', '' );
		$ip   = PlgResourcesReviewsHelper::ip_address();
		$rid  = JRequest::getInt( 'id', 0 );
				
		if (!$id) {
			// Cannot proceed		
			return;
		}
	
		// Is the user logged in?
		if ($juser->get('guest')) {
			$this->login( JText::_('PLEASE_LOGIN_TO_VOTE') );
			return;
			//ximport('xmodule');
			//echo XModuleHelper::renderModules('force_mod');
		} else {
			// Load answer
			$rev = new ResourcesReview( $database );
			$rev->load($id );
			$voted = $rev->getVote ($id, $cat, $juser->get('id'));
	
			if (!$voted && $vote && $rev->user_id != $juser->get('id')) {
				require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'vote.class.php' );
				$v = new Vote( $database );
				$v->referenceid = $id;
				$v->category = $cat;
				$v->voter = $juser->get('id');
				$v->ip = $ip;
				$v->voted = date( 'Y-m-d H:i:s', time() );
				$v->helpful = $vote;
				if (!$v->check()) {
					$this->setError( $v->getError() );
					return;
				}
				if (!$v->store()) {
					$this->setError( $v->getError() );
					return;
				}
			}
						
			// update display
			if ($ajax) {
				$response = $rev->getRating( $id, $juser->get('id'));
				echo plgResourcesReviews::rateitem($response[0], $juser, $this->_option, $rid);
			} else {				
				$this->_redirect = JRoute::_('index.php?option='.$this->_option.'&task=reviews&id='.$rid);
			}
		}
	}
	
	//-----------
	
	public function editreview()
	{
		// Is the user logged in?
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->setError( JText::_('RESOURCES_LOGIN_NOTICE') );
			return;
		}
		
		$resource =& $this->resource;
		
		// Do we have an ID?
		if (!$resource->id) {
			// No - fail! Can't do anything else without an ID
			$this->setError( JText::_('REVIEWS_NO_RESOURCE_ID') );
			return;
		}
		
		// Incoming
		$myr = JRequest::getInt( 'myrating', 0 );

		$database =& JFactory::getDBO();
		
		$review = new ResourcesReview( $database );
		$review->loadUserReview( $resource->id, $juser->get('id') );
		if (!$review->id) {
			// New review, get the user's ID
			$review->user_id = $juser->get('id');
			$review->resource_id = $resource->id;
			$review->tags = '';
		} else {
			// Editing a review, do some prep work
			$review->comment = str_replace('<br />','',$review->comment);

			$RE = new ResourceExtended($resource->id, $database);
			$RE->getTagsForEditing($review->user_id);
			$review->tags = ($RE->tagsForEditing) ? $RE->tagsForEditing : '';
		}
		$review->rating = ($myr) ? $myr : $review->rating;

		// Store the object in our registry
		$this->myreview = $review;
		return;
	}
	
	//-----------

	public function savereview()
	{
		// Incoming
		$resource_id = JRequest::getInt( 'resource_id', 0 );
		
		// Do we have a resource ID?
		if (!$resource_id) {
			// No ID - fail! Can't do anything else without an ID
			echo ResourcesHtml::alert( JText::_('REVIEWS_NO_RESOURCE_ID') );
			exit();
		}
		
		$database =& JFactory::getDBO();
		
		// Bind the form data to our object
		$row = new ResourcesReview( $database );
		if (!$row->bind( $_POST )) {
			echo ResourcesHtml::alert( $row->getError() );
			exit();
		}
		
		// Perform some text cleaning, etc.
		$row->id        = JRequest::getInt( 'reviewid', 0 );
		$row->comment   = $this->purifyText($row->comment);
		$row->comment   = nl2br($row->comment);
		$row->anonymous = ($row->anonymous == 1 || $row->anonymous == '1') ? $row->anonymous : 0;
		$row->created   = ($row->created) ? $row->created : date( "Y-m-d H:i:s" );
		
		// Check for missing (required) fields
		if (!$row->check()) {
			echo ResourcesHtml::alert( $row->getError() );
			exit();
		}
		// Save the data
		if (!$row->store()) {
			echo ResourcesHtml::alert( $row->getError() );
			exit();
		}
		
		// Calculate the new average rating for the parent resource
		$resource =& $this->resource;
		$resource->calculateRating();
		$resource->updateRating();
		
		// Process tags
		$tags = trim(JRequest::getVar( 'review_tags', '' ));
		if ($tags) {
			$rt = new ResourcesTags( $database );
			$rt->tag_object($row->user_id, $resource_id, $tags, 1, 0);
		}
		
		// Instantiate a helper object and get all the contributor IDs
		$helper = new ResourcesHelper( $resource->id, $database );
		$helper->getContributorIDs();
		$users = $helper->contributorIDs;
		
		// Get the HUB configuration
		$xhub =& XFactory::getHub();
		
		$juri =& JURI::getInstance();
		$sef = JRoute::_('index.php?option='.$this->_option.a.'id='.$resource->id.a.'active=reviews');
		if (substr($sef,0,1) == '/') {
			$sef = substr($sef,1,strlen($sef));
		}
		
		// Build the subject
		$subject = 'Contributions';
		
		// Message
		$message  = "Someone has posted a review to a resource you are listed as a contributor of:\r\n\r\n";
		$message .= stripslashes($resource->title)."\r\n\r\n";
		$message .= "To view the comment, go to:\r\n";
		$message .= $juri->base().$sef . "\r\n";
		
		// Build the "from" data for the e-mail
		$from = array();
		$from['name']  = $xhub->getCfg('hubShortName').' Contributions';
		$from['email'] = $xhub->getCfg('hubSupportEmail');
		
		// Send message
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger( 'onSendMessage', array( 'resources_new_comment', $subject, $message, $from, $users, $this->_option ))) {
			$this->setError( JText::_('Failed to message users.') );
		}
	}

	//-----------
	
	public function deletereview()
	{
		$database =& JFactory::getDBO();
		$resource =& $this->resource;
		
		// Incoming
		$reviewid = JRequest::getInt( 'reviewid', 0 );
		
		// Do we have a review ID?
		if (!$reviewid) {
			echo ResourcesHtml::error( JText::_('REVIEWS_NO_ID') );
			return;
		}
		
		// Do we have a resource ID?
		if (!$resource->id) {
			echo ResourcesHtml::error( JText::_('REVIEWS_NO_RESOURCE_ID') );
			return;
		}
		
		$review = new ResourcesReview( $database );
		
		// Delete the review's comments
		ximport( 'xcomment' );
		$reply = new XComment( $database );
		
		$comments1 = $reply->getResults( array('id'=>$reviewid, 'category'=>'review') );
		if (count($comments1) > 0) {
			foreach ($comments1 as $comment1) 
			{
				$comments2 = $reply->getResults( array('id'=>$comment1->id, 'category'=>'reviewcomment') );
				if (count($comments2) > 0) {
					foreach ($comments2 as $comment2) 
					{
						$comments3 = $reply->getResults( array('id'=>$comment2->id, 'category'=>'reviewcomment') );
						if (count($comments3) > 0) {
							foreach ($comments3 as $comment3) 
							{
								$reply->delete( $comment3->id );
							}
						}
						$reply->delete( $comment2->id );
					}
				}
				$reply->delete( $comment1->id );
			}
		}
		
		// Delete the review
		$review->delete( $reviewid );

		// Recalculate the average rating for the parent resource
		$resource->calculateRating();
		$resource->updateRating();
	}
	
	//-----------
	
	public function reviewForm( $review, $option ) 
	{
		if ($review->id) {
			$title = JText::_('EDIT_YOUR_REVIEW');
		} else {
			$title = JText::_('WRITE_A_REVIEW');
		}
		
		/*if (!$review->rating) {
			$review->rating = 5;
		}*/
		
		$app =& JFactory::getApplication();
		$html  = '</div><div class="clear"></div>'.n;	
		//$html .= '<div class="main section">'.n;
		if($this->banking) {	
		$html .= t.'<div class="aside">'.n;
		$html .= t.'<p class="help">'.JText::_('DID_YOU_KNOW_YOU_CAN').' <a href="'.$this->infolink.'">'.JText::_('EARN_POINTS').'</a> '.JText::_('FOR_REVIEWS').'?';
		$html .=' '.JText::_('EARN_POINTS_EXP');
		$html .='</p>'.n;
		$html .= t.'</div><!-- / .aside -->'.n;
		}
		$html .= t.'<div class="subject">'.n;
		$html .= t.'<a name="reviewform"></a>'.n;
		$html .= '<form action="index.php" method="post" id="hubForm">'.n;	
		$html .= t.'<fieldset style="padding-top:2em;">'.n;
		$html .= ResourcesHtml::hed(4,$title).n;
		$html .= t.t.'<fieldset>'.n;
		
		$html .= t.t.t.'<legend>'.JText::_('REVIEW_FORM_RATING').':</legend>'.n;
		$html .= t.t.t.'<label><input class="option" id="review_rating_1" name="rating" type="radio" value="1"';
		if ($review->rating == 1) { $html .= ' checked="checked"'; } 
		$html .= ' /> <img src="templates/'. $app->getTemplate() .'/images/stars/1.gif" alt="'.JText::_('RATING_1_STAR').'" /> '.JText::_('RATING_POOR').'</label>'.n;
		$html .= t.t.t.'<label><input class="option" id="review_rating_2" name="rating" type="radio" value="2"';
		if ($review->rating == 2) { $html .= ' checked="checked"'; }
		$html .= ' /> <img src="templates/'. $app->getTemplate() .'/images/stars/2.gif" alt="'.JText::_('RATING_2_STARS').'" /> '.JText::_('RATING_FAIR').'</label>'.n;
		$html .= t.t.t.'<label><input class="option" id="review_rating_3" name="rating" type="radio" value="3"';
		if ($review->rating == 3) { $html .= ' checked="checked"'; }
		$html .= ' /> <img src="templates/'. $app->getTemplate() .'/images/stars/3.gif" alt="'.JText::_('RATING_3_STARS').'" /> '.JText::_('RATING_GOOD').'</label>'.n;
		$html .= t.t.t.'<label><input class="option" id="review_rating_4" name="rating" type="radio" value="4"';
		if ($review->rating == 4) { $html .= ' checked="checked"'; }
		$html .= ' /> <img src="templates/'. $app->getTemplate() .'/images/stars/4.gif" alt="'.JText::_('RATING_4_STARS').'" /> '.JText::_('RATING_VERY_GOOD').'</label>'.n;
		$html .= t.t.t.'<label><input class="option" id="review_rating_5" name="rating" type="radio" value="5"';
		if ($review->rating == 5) { $html .= ' checked="checked"'; }
		$html .= ' /> <img src="templates/'. $app->getTemplate() .'/images/stars/5.gif" alt="'.JText::_('RATING_5_STARS').'" /> '.JText::_('RATING_EXCELLENT').'</label>'.n;
		$html .= t.t.'</fieldset>'.n;
		
		$html .= t.t.'<label for="review_anon">'.n;
		$html .= t.t.t.'<input class="option" type="checkbox" name="anonymous" id="review_anon" value="1"';
		if ($review->anonymous != 0) { $html .= ' checked="checked"'; }
		$html .= '/>'.n;
		$html .= t.t.t.JText::_('REVIEW_FORM_ANONYMOUS').n;
		$html .= t.t.'</label>'.n;

		$html .= t.t.'<label for="review_comments">'.n;
		$html .= t.t.t.JText::_('REVIEW_FORM_COMMENTS').' ';
		if($this->banking) {
		$html .= '( <span class="required">'.JText::_('REQUIRED').'</span> '.JText::_('FOR_ELIGIBILITY').' <a href="'.$this->infolink.'">'.JText::_('EARN_POINTS').'</a> )'.n;
		}
		$html .= t.t.t.'<textarea name="comment" id="review_comments" rows="7" cols="35">'. $review->comment .'</textarea>'.n;
		$html .= t.t.'</label>'.n;

		/*$html .= t.t.'<label for="tags-men">'.n;
		$html .= t.t.t.JText::_('REVIEW_FORM_TAGS').':'.n;
		$html .= t.t.t.'<input type="text" name="review_tags" id="review_tags" value="'. $review->tags .'" size="38" />'.n;
		$html .= t.t.'</label>'.n;
		$html .= t.t.'<p class="hint">'.JText::_('REVIEW_FORM_TAGS_EXPLANATION').'</p>'.n;*/

		$html .= t.t.'<input type="hidden" name="created" value="'. $review->created .'" />'.n;
		$html .= t.t.'<input type="hidden" name="reviewid" value="'. $review->id .'" />'.n;
		$html .= t.t.'<input type="hidden" name="user_id" value="'. $review->user_id .'" />'.n;
		$html .= t.t.'<input type="hidden" name="resource_id" value="'. $review->resource_id .'" />'.n;
		$html .= t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
		$html .= t.t.'<input type="hidden" name="task" value="view" />'.n;
		$html .= t.t.'<input type="hidden" name="id" value="'. $review->resource_id .'" />'.n;
		$html .= t.t.'<input type="hidden" name="action" value="savereview" />'.n;
		$html .= t.t.'<input type="hidden" name="active" value="reviews" />'.n;

		$html .= t.t.'<p class="submit"><input type="submit" value="'.JText::_('SUBMIT').'" /></p>'.n;
		$html .= t.'</fieldset>'.n;
		$html .= '</form>'.n;
		$html .= '</div>'.n;	
		//$html .= '</div><div class="clear"></div>'.n;	

		return $html;
	}
	
	//-----------
	
	private function purifyText( &$text ) 
	{
		$text = preg_replace( '/{kl_php}(.*?){\/kl_php}/s', '', $text );
		$text = preg_replace( '/{.+?}/', '', $text );
		$text = preg_replace( "'<script[^>]*>.*?</script>'si", '', $text );
		$text = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2', $text );
		$text = preg_replace( '/<!--.+?-->/', '', $text );
		$text = preg_replace( '/&nbsp;/', ' ', $text );
		$text = preg_replace( '/&amp;/', ' ', $text );
		$text = preg_replace( '/&quot;/', ' ', $text );
		$text = strip_tags( $text );
		return $text;
	}
	
	//-----------
	
	private function valid_ip($ip)
	{
		return (!preg_match( "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip)) ? FALSE : TRUE;
	}
	
	//-----------

	private function ip_address()
	{
		if (PlgResourcesReviewsHelper::server('REMOTE_ADDR') AND PlgResourcesReviewsHelper::server('HTTP_CLIENT_IP')) {
			$ip_address = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (PlgResourcesReviewsHelper::server('REMOTE_ADDR')) {
			$ip_address = $_SERVER['REMOTE_ADDR'];
		} elseif (PlgResourcesReviewsHelper::server('HTTP_CLIENT_IP')) {
			$ip_address = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (PlgResourcesReviewsHelper::server('HTTP_X_FORWARDED_FOR')) {
			$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		
		if ($ip_address === FALSE) {
			$ip_address = '0.0.0.0';
			return $ip_address;
		}
		
		if (strstr($ip_address, ',')) {
			$x = explode(',', $ip_address);
			$ip_address = end($x);
		}
		
		if (!PlgResourcesReviewsHelper::valid_ip($ip_address)) {
			$ip_address = '0.0.0.0';
		}
				
		return $ip_address;
	}
	
	//-----------
	
	private function server($index = '')
	{		
		if (!isset($_SERVER[$index])) {
			return FALSE;
		}
		
		return $_SERVER[$index];
	}
}
