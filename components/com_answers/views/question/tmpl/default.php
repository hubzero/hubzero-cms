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

$name = JText::_('COM_ANSWERS_ANONYMOUS');
if ($this->question->anonymous == 0) {			
	$user =& JUser::getInstance( $this->question->created_by );
	if (is_object($user)) {
		$name = $user->get('name');
	} else {
		$name = JText::_('COM_ANSWERS_UNKNOWN');
	}
}

$this->question->created = Hubzero_View_Helper_Html::mkt($this->question->created);
$when = Hubzero_View_Helper_Html::timeAgo($this->question->created);

$reports = (isset($this->question->reports)) ? $this->question->reports: '0';
$votes = ($this->question->helpful) ? $this->question->helpful: '0';

?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<ul id="useroptions">
		<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=myquestions'); ?>" class="myquestions"><span><?php echo JText::_('COM_ANSWERS_MY_QUESTIONS'); ?></span></a></li>
		<li class="last"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=search'); ?>"><span><?php echo JText::_('COM_ANSWERS_ALL_QUESTIONS'); ?></span></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="warning"><?php echo $this->getError(); ?></p>
<?php } ?>

<?php if ($this->question->state == 0 && $this->id!=0) { ?>
	<h3><?php echo JText::_('Open Question'); ?></h3>
<?php } else if ($this->question->state == 2 or $this->id==0) { ?>
	<h3><?php echo JText::_('COM_ANSWERS_ERROR_QUESTION_NOT_FOUND'); ?></h3>		
	<?php if ($this->note['msg']!='') { ?>
	<p class="help"><?php echo urldecode($this->note['msg']); ?></p>
	<?php } else { ?>
	<p class="error"><?php echo JText::_('COM_ANSWERS_NOTICE_QUESTION_REMOVED'); ?></p>
	<?php } ?>
</div><!-- / .main section -->
<?php } else { ?>
	<h3><?php echo JText::_('COM_ANSWERS_CLOSED_QUESTION'); ?></h3>
<?php } ?>

	<div class="aside">
<?php if ($this->question->state == 0 && $this->responding!=1 && $reports == 0) { ?>
		<p id="primary-document" ><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=answer&id='.$this->question->id); ?>"><?php echo JText::_('COM_ANSWERS_ANSWER_THIS'); ?></a></p>
<?php } ?>
		<div class="status_display">
			<p class="intro">
				<?php echo JText::_('COM_ANSWERS_STATUS'); ?>:
<?php if ($this->question->state == 0 && $reports == 0) { ?>
				<span class="open"><?php echo JText::_('COM_ANSWERS_STATUS_ACCEPTING_ANSWERS'); ?></span>
<?php } else if ($reports > 0) { ?>
				<span class="underreview"><?php echo JText::_('COM_ANSWERS_STATUS_UNDER_REVIEW'); ?></span>
<?php } else { ?>
				<span class="closed"><?php echo JText::_('COM_ANSWERS_STATUS_CLOSED'); ?></span></p>
<?php } ?>
			</p>
<?php if ($this->reward > 0 && $this->question->state == 0 && $this->banking) { ?>
			<p class="intro"><?php echo JText::_('COM_ANSWERS_BONUS'); ?>: <span class="pointvalue"><a href="<?php $this->infolink; ?>" title="<?php echo JText::_('COM_ANSWERS_WHAT_ARE_POINTS'); ?>"><?php echo JText::_('COM_ANSWERS_WHAT_ARE_POINTS'); ?></a><?php echo JText::sprintf('COM_ANSWERS_NUMBER_POINTS', $this->reward); ?></span></p>
<?php } ?>
<?php if (isset($this->question->maxaward) && $this->question->state == 0 && $this->banking) { ?>
			<p class="youcanearn">
				<?php echo JText::sprintf('COM_ANSWERS_EARN_UP_TO_FOR_BEST_ANSWER', $this->question->maxaward); ?> <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=math&id='.$this->question->id); ?>"><?php echo JText::_('COM_ANSWERS_DETAILS'); ?></a>
			</p>
<?php } ?>
		</div><!-- / .status_display -->
	</div><!-- / .aside -->
	
	<div class="subject">
		<div id="questionwrap">
			<div id="question">
				<div>
<?php if ($reports > 0) { ?>
					<h4><?php echo JText::_('COM_ANSWERS_NOTICE_QUESTION_REPORTED'); ?></h4>
					<p class="details">
						<?php echo JText::sprintf('COM_ANSWERS_ASKED_BY', $name); ?> - <?php echo JText::sprintf('COM_ANSWERS_TIME_AGO', $when); ?>
					</p>
<?php } else { ?>
					<h4><?php echo $this->question->subject; ?></h4>
	<?php if ($this->question->question) { ?>
					<p><?php echo stripslashes($this->question->question); ?></p>
	<?php } ?>
					<p class="details">
						<?php echo JText::sprintf('COM_ANSWERS_ASKED_BY', $name); ?> - <?php echo JText::sprintf('COM_ANSWERS_TIME_AGO', $when); ?> - <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=question&id='.$question->id.'#answers'); ?>'" title="<?php echo JText::_('COM_ANSWERS_READ_RESPONSES'); ?>"><?php echo (count($this->responses) == 1) ? JText::sprintf('COM_ANSWERS_NUMBER_RESPONSES', count($this->responses)) : JText::sprintf('COM_ANSWERS_NUMBER_RESPONSES', count($this->responses)); ?></a>
					</p>
	<?php if (count($this->tags) > 0) { ?>
					<p class="details tagged">
						<?php echo JText::_('COM_ANSWERS_TAGS'); ?>:			
						<ol class="tags">
						<?php
						$tagarray = array();
						$tagarray[] = '';
						foreach ($this->tags as $tag)
						{
							$tag['raw_tag'] = str_replace( '&amp;', '&', $tag['raw_tag'] );
							$tag['raw_tag'] = str_replace( '&', '&amp;', $tag['raw_tag'] );
							$tagarray[] = '<li><a href="'.JRoute::_('index.php?option=com_tags&tag='.$tag['tag']).'" rel="tag">'.$tag['raw_tag'].'</a></li>';
						}
						$tagarray[] = '';

						$alltags = implode( "\n", $tagarray );
						echo $alltags;
						?>
						</ol>
					</p>
	<?php } ?>
<?php } ?>
				</div>
			</div><!-- / #question -->

			<p id="questionstatus">
<?php
if (!$this->juser->get('guest')) {
	$addon =' title="'.JText::_('COM_ANSWERS_CLICK_TO_RECOMMEND').'"';
	if ($this->voted) {
		$addon =' class="voted" title="'.JText::_('COM_ANSWERS_NOTICE_ALREADY_RECOMMENDED').'"';
	}
} else {
	$addon =' title="'.JText::_('COM_ANSWERS_LOGIN_TO_RECOMMEND_QUESTION').'"';
}
if ($reports == 0) { ?>
				<span class="question_vote"><?php echo $votes; ?>
	<?php if (!$this->voted) { ?>
					<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=question&id='.$this->question->id.'&vote=1'); ?>" <?php echo $addon; ?>><?php echo JText::_('COM_ANSWERS_GOOD_QUESTION'); ?></a>
	<?php } else { ?>
					<span <?php echo $addon; ?>><?php echo JText::_('COM_ANSWERS_GOOD_QUESTION'); ?></span>
	<?php } ?>
				</span>
				<span class="abuse">
					<a href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=question&id='.$this->question->id); ?>" title="<?php echo JText::_('COM_ANSWERS_TITLE_REPORT_ABUSE'); ?>"><?php echo JText::_('COM_ANSWERS_REPORT_ABUSE'); ?></a>
				</span>
			<?php if ($this->question->created_by == $this->juser->get('username') && $this->question->state == 0) { ?>
				<span class="deleteq">
					<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=delete&id='.$this->question->id); ?>" title="<?php echo JText::_('COM_ANSWERS_DELETE_QUESTION'); ?>"><?php echo JText::_('COM_ANSWERS_DELETE'); ?></a>
				</span>
			<?php } ?>
<?php } ?>
			</p>	
<?php		
if ($this->note['msg'] != '') {
?>
			<p class="<?php echo $this->note['class']; ?>"><?php echo urldecode($this->note['msg']); ?></p>
<?php
}
?>
<?php if ($this->responding == 1 && $reports == 0) { // answer form ?>
		</div><!-- / #questionwrap -->
		<div class="clear"></div>
	</div><!-- / .subject -->

	<h3><?php echo JText::_('COM_ANSWERS_YOUR_ANSWER'); ?>
	<div class="main section">
		<form action="index.php" method="post" id="hubForm">
			<div class="aside">
				<p><?php echo JText::_('COM_ANSWERS_BE_POLITE'); ?></p>
				<p><?php echo JText::_('COM_ANSWERS_NO_HTML'); ?></p>
			</div><!-- / .aside -->
			<div class="subject">
				<fieldset>
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="task" value="savea" />
					<input type="hidden" name="qid" value="<?php echo $this->question->id; ?>" />
					
					<label>
						<input class="option" type="checkbox" name="anonymous" value="1" /> 
						<?php echo JText::_('COM_ANSWERS_POST_ANON'); ?>
					</label>
					<label>
						<?php echo JText::_('COM_ANSWERS_YOUR_RESPONSE'); ?>:<br />
						<textarea name="answer" rows="10" cols="50"></textarea>
					</label>
				</fieldset>
				<p class="submit"><input type="submit" value="<?php echo JText::_('COM_ANSWERS_SUBMIT'); ?>" /></p>
			</div><!-- / .subject -->
			<div class="clear"></div>
		</form>
	</div><!-- / .section -->
	<div class="clear"></div>
</div><!-- / .main section -->
<?php } else if ($this->responding == 4 && $this->question->state == 0 && $reports == 0) { // delete question ?>
		</div><!-- / #questionwrap -->
		<div class="clear"></div>
	</div><!-- / .subject -->
	<div class="main section">
		<div class="subject">
			<p class="error"><?php echo JText::_('COM_ANSWERS_NOTICE_CONFIRM_DELETE'); ?></p>
			<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=delete_q&qid='.$$this->question->id); ?>"><?php echo JText::_('COM_ANSWERS_YES_DELETE'); ?></a> | <a  href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=question&id='.$question->id); ?>"><?php echo JText::_('COM_ANSWERS_NO_DELETE'); ?></a></p>
		</div><!-- / .subject -->
		<div class="clear"></div>
	</div><!-- / .section -->
	<div class="clear"></div>
</div><!-- / .main section -->	
<?php } else if ($reports == 0) { ?>
		</div><!-- / #questionwrap -->
		<div class="clear"></div>
	</div><!-- / .subject -->
</div><!-- / .main section -->
	<div class="main section">
<?php if ($this->responding == 6 && $this->question->state == 0 && $reports == 0 && $this->banking) { // show how points are awarded ?>
		<div class="subject">
			<h3><?php echo JText::_('COM_ANSWERS_POINTS_BREAKDOWN'); ?></h3>
			<p><?php echo JText::_('COM_ANSWERS_POINT_BREAKDOWN_TBL_SUMMARY'); ?></p>
			<table summary="<?php echo JText::_('COM_ANSWERS_POINTS'); ?>">
				<tbody>
					<tr>
						<th> </th>
						<td><?php echo JText::_('COM_ANSWERS_POINTS'); ?></td>
						<td><?php echo JText::_('COM_ANSWERS_DETAILS'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('COM_ANSWERS_ACTIVITY'); ?>*</th>
						<td><?php echo $this->question->marketvalue; ?></td>
						<td> </td>
					</tr>
					<tr>
						<th><?php echo JText::_('COM_ANSWERS_BONUS'); ?></th>
						<td><?php echo $this->reward; ?></td>
						<td> </td>
					</tr>
					<tr>
						<th><?php echo JText::_('COM_ANSWERS_TOTAL_MARKET_VALUE'); ?></th>
						<td><?php echo intval($this->question->marketvalue + $this->reward); ?></td>
						<td><?php echo JText::_('COM_ANSWERS_TOTAL'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('COM_ANSWERS_ASKER_WILL_EARN'); ?></th>
						<td><?php echo round($this->question->marketvalue/3); ?></td>
						<td><?php echo JText::_('COM_ANSWERS_ONE_THIRD_OF_ACTIVITY_POINTS'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('COM_ANSWERS_ASKER_WILL_PAY'); ?></th>
						<td><?php echo $this->reward; ?></td>
						<td><?php echo JText::_('COM_ANSWERS_REWARD_ASSIGNED_BY_ASKER'); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('COM_ANSWERS_BEST_ANSWER_MAY_EARN'); ?></th>
						<td><?php echo (round(($this->question->marketvalue)/3) + $this->reward).' &mdash; '.(round(2*(($this->question->marketvalue)/3)) + $this->reward); ?></td>
						<td><?php echo JText::_('COM_ANSWERS_UP_TO_TWO_THIRDS_OF_ACTIVITY_POINTS'); ?></td>
					</tr>
				</tbody>
			</table>
			<p>* <?php echo JText::_('COM_ANSWERS_ACTIVITY_POINTS_EXPLANATION'); ?> <a href="<?php echo $this->infolink; ?>"><?php echo JText::_('Read further details'); ?></a>.</p>			
		</div><!-- / .subject -->
		<div class="clear"></div>
	</div><!-- / .section -->
<?php } ?>
	<a name="answers"></a>

<?php if ($this->juser->get('username') == $this->question->created_by && $this->question->state == 0) { ?>
	<div class="aside">
		<div class="sidenote">
			<p class="info"><?php echo JText::_('COM_ANSWERS_DO_NOT_FORGET_TO_CLOSE'); ?></p>
		</div>
	</div><!-- / .aside -->
<?php } ?>

	<h3><?php echo JText::_('COM_ANSWERS_ANSWERS'); ?> (<?php echo count($this->responses); ?>)</h3>
	
	<div class="subject">
<?php if ($this->responses) { ?>
		<ol class="comments">
<?php
		$cls = 'even';
		foreach ($this->responses as $row) 
		{
			// Set the name of the reviewer
			$name = JText::_('COM_ANSWERS_ANONYMOUS');
			if ($row->anonymous != 1) {
				$name = JText::_('COM_ANSWERS_UNKNOWN');
				$ruser =& JUser::getInstance($row->created_by);
				if (is_object($ruser)) {
					$name = $ruser->get('name');
				}
			}
			
			$abuse = isset($row->reports) ? $row->reports : 0;
			
			$cls  = ($cls == 'odd') ? 'even' : 'odd';
			$cls .= ($abuse) ? ' abusive' : '';
			if ($this->question->state == 1 && $row->state == 1) {
				$cls .= ' chosen';
			}
?>
			<li class="comment <?php echo $cls; ?>">
				<dl class="comment-details">
					<dt class="type_answer"><span class="<?php echo ($this->question->state == 1 && $row->state == 1) ? 'accepted' : 'regular'; ?>"></span></dt>
					<dd class="date"><?php echo JHTML::_('date',$row->created, '%d %b, %Y'); ?></dd>
					<dd class="time"><?php echo JHTML::_('date',$row->created, '%I:%M %p'); ?></dd>
				</dl>
				<div class="cwrap">
					<p class="name"><strong><?php echo $name; ?></strong> <?php echo JText::_('COM_ANSWERS_SAID'); ?>:</p>
<?php if (!$abuse) { ?>
					<p id="answers_<?php echo $row->id; ?>" class="<?php echo $this->option; ?>">
						<?php
						$view = new JView( array('name'=>'rateitem') );
						$view->option = $this->option;
						$view->item = $row;
						//$view->qid = $question->id;
						$view->display();
						?>					
					</p>
					<p class="comment-options">
						<a href="<?php echo (!$this->juser->get('guest')) ? 'javascript:void(0);' : JRoute::_('index.php?option='.$this->option.'&task=reply&category=answer&id='.$this->question->id.'&refid='.$row->id); ?>" class="showreplyform" id="rep_<?php echo $row->id; ?>"><?php echo JText::_('COM_ANSWERS_REPLY'); ?></a> 
						<span class="abuse"><a href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=answer&id='.$row->id.'&parent='.$this->question->id); ?>"><?php echo JText::_('COM_ANSWERS_REPORT_ABUSE'); ?></a></span>
<?php if ($this->juser->get('username') == $this->question->created_by && $this->question->state == 0) { ?>
						<span class="accept"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=accept&id='.$this->question->id.'&rid='.$row->id); ?>"><?php echo JText::_('COM_ANSWERS_ACCEPT_ANSWER'); ?></a></span>
<?php } ?>
					</p>
<?php
$view = new JView( array('name'=>'question', 'layout'=>'addcomment') );
$view->option = $this->option;
$view->row = $row;
$view->juser = $this->juser;
$view->level = 0;
$view->question = $this->question;
$view->addcomment = $this->addcomment;
$view->display();
?>
<?php if ($this->showcomments && isset($row->replies)) {				
				$o = 'even';
				$html = '';
				if (count($row->replies) > 0) {
					$html .= '<ol class="comments pass2">';
					foreach ($row->replies as $reply) 
					{
						$o = ($o == 'odd') ? 'even' : 'odd';

						// Comment
						$html .= '<li class="comment '.$o;
						if ($this->abuse && $reply->reports > 0) {
							$html .= ' abusive';
						}
						$html .= '" id="c'.$reply->id.'r">';

						$view = new JView( array('name'=>'question', 'layout'=>'comment') );
						$view->option = $this->option;
						$view->reply = $reply;
						$view->juser = $this->juser;
						$view->id = $this->question->id;
						$view->level = 1;
						$view->abuse = $this->abuse;
						$view->question = $this->question;
						$view->addcomment = $this->addcomment;
						$html .= $view->loadTemplate();
						
						// Another level? 
						if (count($reply->replies) > 0) {
							$html .= '<ol class="comments pass3">';
							foreach ($reply->replies as $r) 
							{
								$o = ($o == 'odd') ? 'even' : 'odd';

								$html .= '<li class="comment '.$o;
								if ($this->abuse && $r->reports > 0) {
									$html .= ' abusive';
								}
								$html .= '" id="c'.$r->id.'r">';
								
								$view = new JView( array('name'=>'question', 'layout'=>'comment') );
								$view->option = $this->option;
								$view->reply = $r;
								$view->juser = $this->juser;
								$view->id = $this->question->id;
								$view->level = 2;
								$view->abuse = $this->abuse;
								$view->question = $this->question;
								$view->addcomment = $this->addcomment;
								$html .= $view->loadTemplate();

								// Yet another level?? 
								if (count($r->replies) > 0) {
									$html .= '<ol class="comments pass4">';
									foreach ($r->replies as $rr) 
									{
										$o = ($o == 'odd') ? 'even' : 'odd';

										$html .= t.'<li class="comment '.$o;
										if ($this->abuse && $rr->reports > 0) {
											$html .= ' abusive';
										}
										$html .= '" id="c'.$rr->id.'r">';
										//$html .= AnswersHtml::comment($rr, $juser, $option, $id, $addcomment, 3, $abuse, $o).n;
										$view = new JView( array('name'=>'question', 'layout'=>'comment') );
										$view->option = $this->option;
										$view->reply = $rr;
										$view->juser = $this->juser;
										$view->id = $this->question->id;
										$view->level = 3;
										$view->abuse = $this->abuse;
										$view->question = $this->question;
										$view->addcomment = $this->addcomment;
										$html .= $view->loadTemplate();
										$html .= '</li>';
									}
									$html .= '</ol><!-- end pass4 -->';
								}
								$html .= '</li>';
							}
							$html .= '</ol><!-- end pass3 -->';
						}
						$html .= '</li>';
					}
					$html .= '</ol><!-- end pass2 -->';
					echo $html;
				}
} ?>
<?php } else { ?>
					<p class="condensed"><?php echo JText::_('COM_ANSWERS_NOTICE_POSTING_REPORTED'); ?></p>
<?php } ?>
				</div><!-- / .cwrap -->
			</li>
<?php
		}
?>
		</ol>
<?php } else { ?>
		<p><?php echo JText::_('COM_ANSWERS_NO_ANSWERS_BE_FIRST'); ?> <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=answer&id='.$this->question->id); ?>"><?php echo JText::_('COM_ANSWERS_BE_FIRST_ANSWER_THIS'); ?></a>.</p>
	<?php if ($this->banking) { ?>
		<p class="help">
			<strong><?php echo JText::_('COM_ANSWERS_DID_YOU_KNOW_ABOUT_POINTS'); ?></strong><br />
			<a href="<?php echo $this->infolink; ?>"><?php echo JText::_('COM_ANSWERS_LEARN_MORE'); ?></a> <?php echo JText::_('COM_ANSWERS_LEARN_HOW_POINTS_AWARDED'); ?>.
		</p>
	<?php } ?>
<?php } ?>
	</div><!-- / .subject -->
	<div class="clear"></div>
<?php } else if ($reports > 0) { ?>
		</div><!-- / #questionwrap -->
		<div class="clear"></div>
	</div><!-- / .subject -->
<?php } ?>
</div><!-- / .main section -->