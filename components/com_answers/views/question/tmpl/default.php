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

ximport('Hubzero_User_Profile');

$wikiconfig = array(
	'option'   => $this->option,
	'scope'    => 'answer',
	'pagename' => $this->question->id,
	'pageid'   => $this->question->id,
	'filepath' => '',
	'domain'   => ''
);
ximport('Hubzero_Wiki_Parser');
$parser =& Hubzero_Wiki_Parser::getInstance();

$name = JText::_('COM_ANSWERS_ANONYMOUS');
$user = new Hubzero_User_Profile();
$user->load( $this->question->created_by );
if ($this->question->anonymous == 0) {
	//$user =& JUser::getInstance( $this->question->created_by );
	if (is_object($user)) {
		$name = $user->get('name');
	} else {
		$name = JText::_('COM_ANSWERS_UNKNOWN');
	}
}

//$this->question->created = Hubzero_View_Helper_Html::mkt($this->question->created);
//$when = Hubzero_View_Helper_Html::timeAgo($this->question->created);

$reports = (isset($this->question->reports)) ? $this->question->reports: '0';
$votes = ($this->question->helpful) ? $this->question->helpful: '0';

?>
<div id="content-header">
<?php if ($this->question->state == 0 && $this->id!=0) { ?>
	<h2><?php echo JText::_(strtoupper($this->option)).': '.JText::_('Open Question'); ?></h2>
<?php } else if ($this->question->state == 2 or $this->id==0) { ?>
	<h2><?php echo JText::_(strtoupper($this->option)).': '.JText::_('COM_ANSWERS_ERROR_QUESTION_NOT_FOUND'); ?></h2>
<?php } else { ?>
	<h2><?php echo JText::_(strtoupper($this->option)).': '.JText::_('COM_ANSWERS_CLOSED_QUESTION'); ?></h2>
<?php } ?>
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
	<!-- <h3><?php echo JText::_('Open Question'); ?></h3> -->
<?php } else if ($this->question->state == 2 or $this->id==0) { ?>
	<h3><?php echo JText::_('COM_ANSWERS_ERROR_QUESTION_NOT_FOUND'); ?></h3>		
	<?php if ($this->note['msg']!='') { ?>
	<p class="help"><?php echo urldecode($this->note['msg']); ?></p>
	<?php } else { ?>
	<p class="error"><?php echo JText::_('COM_ANSWERS_NOTICE_QUESTION_REMOVED'); ?></p>
	<?php } ?>
</div><!-- / .main section -->
<?php } else { ?>
	<!-- <h3><?php echo JText::_('COM_ANSWERS_CLOSED_QUESTION'); ?></h3> -->
<?php } ?>

	<div class="aside">
<?php if ($this->question->state == 0 && $this->responding != 1 && $reports == 0 && $this->question->created_by != $this->juser->get('username')) { ?>
		<p class="answer-question"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=answer&id='.$this->question->id); ?>"><?php echo JText::_('COM_ANSWERS_ANSWER_THIS'); ?></a></p>
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
			<p class="intro"><?php echo JText::_('COM_ANSWERS_BONUS'); ?>: <span class="pointvalue"><a href="<?php echo $this->infolink; ?>" title="<?php echo JText::_('COM_ANSWERS_WHAT_ARE_POINTS'); ?>"><?php echo JText::_('COM_ANSWERS_WHAT_ARE_POINTS'); ?></a><?php echo JText::sprintf('COM_ANSWERS_NUMBER_POINTS', $this->reward); ?></span></p>
<?php } ?>
<?php if (isset($this->question->maxaward) && $this->question->state == 0 && $this->banking) { ?>
			<p class="youcanearn">
				<?php echo JText::sprintf('COM_ANSWERS_EARN_UP_TO_FOR_BEST_ANSWER', $this->question->maxaward); ?> <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=math&id='.$this->question->id); ?>"><?php echo JText::_('COM_ANSWERS_DETAILS'); ?></a>
			</p>
<?php } ?>
		</div><!-- / .status_display -->
	</div><!-- / .aside -->
	
	<div class="subject">

			<div class="question" id="q<?php echo $this->question->id; ?>">
				<p class="question-member-photo">
					<span class="question-anchor"><a name="q<?php echo $this->question->id; ?>"></a></span>
					<img src="<?php echo AnswersHelperMember::getMemberPhoto($user, $this->question->anonymous); ?>" alt="" />
				</p><!-- / .question-member-photo -->
				<div class="question-content">
<?php
if (!$this->juser->get('guest')) {
	$addon =' title="'.JText::_('COM_ANSWERS_CLICK_TO_RECOMMEND').'"';
	if ($this->voted) {
		$addon =' class="voted" title="'.JText::_('COM_ANSWERS_NOTICE_ALREADY_RECOMMENDED').'"';
	}
} else {
	$addon =' title="'.JText::_('COM_ANSWERS_LOGIN_TO_RECOMMEND_QUESTION').'"';
}
if ($reports == 0) {
?>
					<p class="voting">
						<span class="vote-like">
	<?php if (!$this->voted) { ?>
							<a class="vote-button <?php echo ($votes > 0) ? 'like' : 'neutral'; ?> tooltips" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=question&id='.$this->question->id.'&vote=1'); ?>" title="Vote this up :: <?php echo $votes; ?> people liked this"><?php echo $votes; ?><span> Like</span></a>
	<?php } else { ?>
							<span class="vote-button <?php echo ($votes > 0) ? 'like' : 'neutral'; ?> tooltips" title="Vote this up :: Please login to vote."><?php echo $votes; ?><span> Like</span></span>
	<?php } ?>
						</span>
					</p><!-- / .question-voting -->
<?php } ?>
					<p class="question-title">
						<strong><?php echo $name; ?></strong> 
						<a class="permalink" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=question&id='.$this->question->id); ?>" title="<?php echo JText::_('COM_ANSWERS_PERMALINK'); ?>">@ <span class="time"><?php echo JHTML::_('date',$this->question->created, '%I:%M %p', 0); ?></span> on <span class="date"><?php echo JHTML::_('date',$this->question->created, '%d %b, %Y', 0); ?></span></a>
					</p><!-- / .question-title -->

<?php if ($reports > 0) { ?>
					<p class="warning">
						<?php echo JText::_('COM_ANSWERS_NOTICE_QUESTION_REPORTED'); ?>
					</p>
<?php } else { ?>
					<div class="question-subject">
						<?php echo $parser->parse(stripslashes($this->question->subject), $wikiconfig); ?>
					</div><!-- / .question-subject -->
	<?php if ($this->question->question) { ?>
					<div class="question-long">
						<?php echo $parser->parse(stripslashes($this->question->question), $wikiconfig); ?>
					</div><!-- / .question-long -->
	<?php } ?>
					<?php /* <p class="details">
						<?php echo JText::sprintf('COM_ANSWERS_ASKED_BY', $name); ?> - <?php echo JText::sprintf('COM_ANSWERS_TIME_AGO', $when); ?> - <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=question&id='.$this->question->id.'#answers'); ?>" title="<?php echo JText::_('COM_ANSWERS_READ_RESPONSES'); ?>"><?php echo (count($this->responses) == 1) ? JText::sprintf('COM_ANSWERS_NUMBER_RESPONSES', count($this->responses)) : JText::sprintf('COM_ANSWERS_NUMBER_RESPONSES', count($this->responses)); ?></a>
					</p> */ ?>
	<?php if (count($this->tags) > 0) { ?>
					<div class="question-tags">
						<p><?php echo JText::_('COM_ANSWERS_TAGS'); ?>:</p>			
						<ol class="tags">
						<?php
						$tagarray = array();
						$tagarray[] = '';
						foreach ($this->tags as $tag)
						{
							$tag['raw_tag'] = str_replace( '&amp;', '&', $tag['raw_tag'] );
							$tag['raw_tag'] = str_replace( '&', '&amp;', $tag['raw_tag'] );
							$tagarray[] = '<li><a href="'.JRoute::_('index.php?option=com_tags&tag='.$tag['tag']).'" rel="tag">'.stripslashes($tag['raw_tag']).'</a></li>';
						}
						$tagarray[] = '';

						$alltags = implode( "\n", $tagarray );
						echo $alltags;
						?>
						</ol>
					</div><!-- / .question-tags -->
	<?php } ?>
<?php } ?>
				</div><!-- / .question-content -->
				<p class="question-status">
<?php if ($reports == 0) { ?>
					<span>
						<a class="abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=question&id='.$this->question->id); ?>" title="<?php echo JText::_('COM_ANSWERS_TITLE_REPORT_ABUSE'); ?>"><?php echo JText::_('COM_ANSWERS_REPORT_ABUSE'); ?></a>
					</span>
			<?php if ($this->question->created_by == $this->juser->get('username') && $this->question->state == 0) { ?>
					<span>
						<a class="delete" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=delete&id='.$this->question->id); ?>" title="<?php echo JText::_('COM_ANSWERS_DELETE_QUESTION'); ?>"><?php echo JText::_('COM_ANSWERS_DELETE'); ?></a>
					</span>
			<?php } ?>
<?php } ?>
				</p><!-- / .question-status -->
			</div><!-- / .question -->
<?php if ($this->note['msg'] != '') { ?>
			<div class="subject-wrap">
				<p class="<?php echo $this->note['class']; ?>"><?php echo urldecode($this->note['msg']); ?></p>
			</div>
<?php } ?>
		<div class="clear"></div>
	</div><!-- / .subject -->
	
<?php if ($this->responding == 4 && $this->question->state == 0 && $reports == 0) { // delete question ?>

	<div class="below section">
		<div class="subject">
			<div class="subject-wrap">
			<p class="error"><?php echo JText::_('COM_ANSWERS_NOTICE_CONFIRM_DELETE'); ?></p>
			<!-- 
			<p>
				<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=delete_q&qid='.$this->question->id); ?>"><?php echo JText::_('COM_ANSWERS_YES_DELETE'); ?></a> | 
				<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=question&id='.$this->question->id); ?>"><?php echo JText::_('COM_ANSWERS_NO_DELETE'); ?></a>
			</p>
			 -->
			<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=delete'); ?>" method="post" id="deleteForm">
				<input type="hidden" name="qid" value="<?php echo $this->question->id; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="task" value="delete_q" />

				<p class="submit">
					<input type="submit" value="<?php echo JText::_('COM_ANSWERS_YES_DELETE'); ?>" />
					<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=question&id='.$this->question->id); ?>"><?php echo JText::_('COM_ANSWERS_NO_DELETE'); ?></a>
				</p>
			</form>
			</div>
		</div><!-- / .subject -->
		<div class="clear"></div>
	</div><!-- / .below section -->
	<div class="clear"></div>

<?php } else if ($reports == 0) { ?>

	<?php if ($this->responding == 6 && $this->question->state == 0 && $reports == 0 && $this->banking) { // show how points are awarded ?>
	<div class="below section">
		<h3><?php echo JText::_('COM_ANSWERS_POINTS_BREAKDOWN'); ?></h3>
		
		<div class="aside">
			<p class="info"><?php echo JText::_('COM_ANSWERS_POINT_BREAKDOWN_TBL_SUMMARY'); ?></p>
		</div><!-- / .aside -->
		
		<div class="subject">
			<div class="subject-wrap">
			<table id="pointbreakdown" summary="<?php echo JText::_('COM_ANSWERS_POINTS'); ?>">
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
							* <?php echo JText::_('COM_ANSWERS_ACTIVITY_POINTS_EXPLANATION'); ?> <a href="<?php echo $this->infolink; ?>"><?php echo JText::_('Read further details'); ?></a>.
						</td>
					</tr>
				</tfoot>
				<tbody>
					<tr>
						<th scope="row"><?php echo JText::_('COM_ANSWERS_ACTIVITY'); ?>*</th>
						<td><?php echo $this->question->marketvalue; ?></td>
						<td> </td>
					</tr>
					<tr>
						<th scope="row"><?php echo JText::_('COM_ANSWERS_BONUS'); ?></th>
						<td><?php echo $this->reward; ?></td>
						<td> </td>
					</tr>
					<tr>
						<th scope="row"><?php echo JText::_('COM_ANSWERS_TOTAL_MARKET_VALUE'); ?></th>
						<td><?php echo intval($this->question->marketvalue + $this->reward); ?></td>
						<td><?php echo JText::_('COM_ANSWERS_TOTAL'); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo JText::_('COM_ANSWERS_ASKER_WILL_EARN'); ?></th>
						<td><?php echo round($this->question->marketvalue/3); ?></td>
						<td><?php echo JText::_('COM_ANSWERS_ONE_THIRD_OF_ACTIVITY_POINTS'); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo JText::_('COM_ANSWERS_ASKER_WILL_PAY'); ?></th>
						<td><?php echo $this->reward; ?></td>
						<td><?php echo JText::_('COM_ANSWERS_REWARD_ASSIGNED_BY_ASKER'); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo JText::_('COM_ANSWERS_BEST_ANSWER_MAY_EARN'); ?></th>
						<td><?php echo (round(($this->question->marketvalue)/3) + $this->reward).' &mdash; '.(round(2*(($this->question->marketvalue)/3)) + $this->reward); ?></td>
						<td><?php echo JText::_('COM_ANSWERS_UP_TO_TWO_THIRDS_OF_ACTIVITY_POINTS'); ?></td>
					</tr>
				</tbody>
			</table>
			</div>
		</div><!-- / .subject -->
		<div class="clear"></div>
	</div><!-- / .below section -->
	<div class="clear"></div>
	<?php } ?>

<?php if ($this->responding == 1) { // answer form ?>
	
	<div class="below section">
		<h3>
			<?php echo JText::_('COM_ANSWERS_YOUR_ANSWER'); ?>
		</h3>
		<form action="<?php echo JRoute::_('index.php?option='.$this->option); ?>" method="post" id="commentform">
			<div class="aside">
				<table class="wiki-reference" summary="Wiki Syntax Reference">
					<caption>Wiki Syntax Reference</caption>
					<tbody>
						<tr>
							<td>'''bold'''</td>
							<td><b>bold</b></td>
						</tr>
						<tr>
							<td>''italic''</td>
							<td><i>italic</i></td>
						</tr>
						<tr>
							<td>__underline__</td>
							<td><span style="text-decoration:underline;">underline</span></td>
						</tr>
						<tr>
							<td>{{{monospace}}}</td>
							<td><code>monospace</code></td>
						</tr>
						<tr>
							<td>~~strike-through~~</td>
							<td><del>strike-through</del></td>
						</tr>
						<tr>
							<td>^superscript^</td>
							<td><sup>superscript</sup></td>
						</tr>
						<tr>
							<td>,,subscript,,</td>
							<td><sub>subscript</sub></td>
						</tr>
					</tbody>
				</table>
			</div><!-- / .aside -->
			<div class="subject">
				<p class="comment-member-photo">
					<span class="comment-anchor"><a name="answerform"></a></span>
				<?php
					if (!$this->juser->get('guest')) {
						$jxuser = new Hubzero_User_Profile();
						$jxuser->load( $this->juser->get('id') );
						$thumb = AnswersHelperMember::getMemberPhoto($jxuser, 0);
					} else {
						$config =& JComponentHelper::getParams( 'com_members' );
						$thumb = $config->get('defaultpic');
						if (substr($thumb, 0, 1) != DS) {
							$thumb = DS.$dfthumb;
						}
						$thumb = AnswersHelperMember::thumbit($thumb);
					}
				?>
					<img src="<?php echo $thumb; ?>" alt="" />
				</p>
				<fieldset>
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="task" value="savea" />
					<input type="hidden" name="response[qid]" value="<?php echo $this->question->id; ?>" />

					<label>
						<?php echo JText::_('COM_ANSWERS_YOUR_RESPONSE'); ?>:
						<?php
						ximport('Hubzero_Wiki_Editor');
						$editor =& Hubzero_Wiki_Editor::getInstance();
						echo $editor->display('response[answer]', 'responseanswer', '', '', '50', '10');
						?>
					</label>

					<label id="answer-anonymous-label">
						<input class="option" type="checkbox" name="response[anonymous]" value="1" id="answer-anonymous" /> 
						<?php echo JText::_('COM_ANSWERS_POST_ANON'); ?>
					</label>

					<p class="submit">
						<input type="submit" value="<?php echo JText::_('COM_ANSWERS_SUBMIT'); ?>" />
					</p>

					<div class="sidenote">
						<p>
							<strong>Please keep comments relevant to this entry. Comments deemed inappropriate may be removed.</strong>
						</p>
						<p>
							Line breaks and paragraphs are automatically converted. URLs (starting with http://) or email addresses will automatically be linked. <a href="/topics/Help:WikiFormatting" class="popup">Wiki syntax</a> is supported.
						</p>
					</div>
				</fieldset>
			</div><!-- / .subject -->
			<div class="clear"></div>
		</form>
	</div><!-- / .below section -->
	<div class="clear"></div>

<?php } ?>

<?php
$chosen = false;
if ($this->responses) {
	$cls = 'even';
	foreach ($this->responses as $row)
	{
		if ($this->question->state == 1 && $row->state == 1) {
			$chosen = true;

			// Set the name of the reviewer
			$name = JText::_('COM_ANSWERS_ANONYMOUS');
			$ruser = new Hubzero_User_Profile();
			$ruser->load( $row->created_by );
			if ($row->anonymous != 1) {
				$name = JText::_('COM_ANSWERS_UNKNOWN');
				//$ruser =& JUser::getInstance($row->created_by);
				if (is_object($ruser)) {
					$name = $ruser->get('name');
				}
			}

			$abuse = isset($row->reports) ? $row->reports : 0;

			$cls  = ($cls == 'odd') ? 'even' : 'odd';
			$cls .= ($abuse) ? ' abusive' : '';
			if ($this->question->created_by == $row->created_by) {
				$cls .= ' author';
			}

		$cls .= ' chosen';
		?>
<div class="below section">
	<h3>
		<a name="bestanswer"></a>
		<?php echo JText::_('COM_ANSWERS_CHOSEN_ANSWER'); ?>
	</h3>

	<div class="aside">
	</div><!-- / .aside -->
	
	<div class="subject">
		<ol class="comments">
			<li class="comment <?php echo $cls; ?>" id="c<?php echo $row->id; ?>">
				<p class="comment-member-photo">
					<span class="comment-anchor"><a name="c<?php echo $row->id; ?>"></a></span>
					<img src="<?php echo AnswersHelperMember::getMemberPhoto($ruser, $row->anonymous); ?>" alt="" />
				</p><!-- / .comment-member-photo -->
				<div class="comment-content">
				<?php if (!$abuse) { ?>
					<p class="comment-voting" id="answers_<?php echo $row->id; ?>">
					<?php
						$view = new JView( array('name'=>'rateitem') );
						$view->option = $this->option;
						$view->item = $row;
						$view->type = 'question';
						$view->vote = '';
						$view->id = '';
						if (!$this->juser->get('guest')) {
							if ($row->created_by == $this->juser->get('username')) {
								$view->vote = $row->vote;
								$view->id = $row->id;
							}
						}
						$view->display();
					?>
					</p><!-- / .comment-voting -->
				<?php } ?>
					<p class="comment-title">
						<strong><?php echo $name; ?></strong> 
						<a class="permalink" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=question&id='.$this->question->id.'#c'.$row->id); ?>" title="<?php echo JText::_('COM_ANSWERS_PERMALINK'); ?>">@ <span class="time"><?php echo JHTML::_('date',$row->created, '%I:%M %p', 0); ?></span> on <span class="date"><?php echo JHTML::_('date',$row->created, '%d %b, %Y', 0); ?></span></a>
					</p><!-- / .comment-title -->
				<?php if (!$abuse) { ?>
					<?php echo $parser->parse(stripslashes($row->answer), $wikiconfig); ?>
					<p class="comment-options">
						<a class="abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=answer&id='.$row->id.'&parent='.$this->question->id); ?>"><?php echo JText::_('COM_ANSWERS_REPORT_ABUSE'); ?></a>
						<?php /*<a class="showreplyform" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=reply&category=answer&id='.$this->question->id.'&refid='.$row->id.'#c'.$row->id); ?>" id="rep_<?php echo $row->id; ?>"><?php echo JText::_('COM_ANSWERS_REPLY'); ?></a> 
					<?php if ($this->juser->get('username') == $this->question->created_by && $this->question->state == 0) { ?>
						<span class="accept"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=accept&id='.$this->question->id.'&rid='.$row->id); ?>"><?php echo JText::_('COM_ANSWERS_ACCEPT_ANSWER'); ?></a></span>
					<?php }*/ ?>
					</p><!-- / .comment-options -->
				<?php
					$view = new JView( array('name'=>'question', 'layout'=>'addcomment') );
					$view->option = $this->option;
					$view->row = $row;
					$view->juser = $this->juser;
					$view->level = 4;
					$view->question = $this->question;
					$view->addcomment = $this->addcomment;
					$view->display();
				?>
				</div><!-- / .comment-content -->
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
					/*if ($this->abuse && $reply->reports > 0) {
						$html .= ' abusive';
					}*/
					if ($this->question->created_by == $reply->added_by) {
						$cls .= ' author';
					}
					$html .= '" id="c'.$reply->id.'r">';

					$view = new JView( array('name'=>'question', 'layout'=>'comment') );
					$view->option = $this->option;
					$view->reply = $reply;
					$view->juser = $this->juser;
					$view->id = $this->question->id;
					$view->level = 4;
					$view->abuse = (isset($this->abuse)) ? $this->abuse : '';
					$view->question = $this->question;
					$view->addcomment = $this->addcomment;
					$view->parser = $parser;
					$html .= $view->loadTemplate();

					// Another level? 
					if (count($reply->replies) > 0) {
						$html .= '<ol class="comments pass3">';
						foreach ($reply->replies as $r)
						{
							$o = ($o == 'odd') ? 'even' : 'odd';

							$html .= '<li class="comment '.$o;
							/*if ($this->abuse && $r->reports > 0) {
								$html .= ' abusive';
							}*/
							if ($this->question->created_by == $r->added_by) {
								$cls .= ' author';
							}
							$html .= '" id="c'.$r->id.'r">';

							$view = new JView( array('name'=>'question', 'layout'=>'comment') );
							$view->option = $this->option;
							$view->reply = $r;
							$view->juser = $this->juser;
							$view->id = $this->question->id;
							$view->level = 4;
							$view->abuse = (isset($this->abuse)) ? $this->abuse : '';
							$view->question = $this->question;
							$view->addcomment = $this->addcomment;
							$view->parser = $parser;
							$html .= $view->loadTemplate();

							// Yet another level?? 
							if (count($r->replies) > 0) {
								$html .= '<ol class="comments pass4">';
								foreach ($r->replies as $rr)
								{
									$o = ($o == 'odd') ? 'even' : 'odd';

									$html .= "\t".'<li class="comment '.$o;
									/*if ($this->abuse && $rr->reports > 0) {
										$html .= ' abusive';
									}*/
									$html .= '" id="c'.$rr->id.'r">';
									//$html .= AnswersHtml::comment($rr, $juser, $option, $id, $addcomment, 3, $abuse, $o).n;
									$view = new JView( array('name'=>'question', 'layout'=>'comment') );
									$view->option = $this->option;
									$view->reply = $rr;
									$view->juser = $this->juser;
									$view->id = $this->question->id;
									$view->level = 4;
									$view->abuse = (isset($this->abuse)) ? $this->abuse : '';
									$view->question = $this->question;
									$view->addcomment = $this->addcomment;
									$view->parser = $parser;
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
			} //foreach ($row->replies as $reply)
			} //if (count($row->replies) > 0) 
			?>
			<?php } else { ?>
				<p class="condensed"><?php echo JText::_('COM_ANSWERS_NOTICE_POSTING_REPORTED'); ?></p>
			<?php } //if ($this->showcomments && isset($row->replies)) ?>
			</li>
		</ol>
	</div><!-- / .subject -->
</div><!-- / .below section -->
<div class="clear"></div>
	<?php
		}
	}
}
?>

	<div class="below section">
		<h3>
			<a name="answers"></a>
			<?php echo JText::_('COM_ANSWERS_RESPONSES'); ?> (<?php echo ($chosen) ? (count($this->responses) - 1) : count($this->responses); ?>)
		</h3>

		<div class="aside">
		<?php if ($this->question->state == 0 && $this->responding!=1 && $reports == 0 && $this->question->created_by != $this->juser->get('username')) { ?>
			<p class="answer-question"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=answer&id='.$this->question->id); ?>"><?php echo JText::_('COM_ANSWERS_ANSWER_THIS'); ?></a></p>
		<?php } ?>
		
		<?php if ($this->juser->get('username') == $this->question->created_by && $this->question->state == 0) { ?>
			<p class="info"><?php echo JText::_('COM_ANSWERS_DO_NOT_FORGET_TO_CLOSE'); ?></p>
		<?php } ?>
		</div><!-- / .aside -->
	
		<div class="subject">
		<?php if ($this->responses && ((count($this->responses) > 1) || (count($this->responses) == 1 && !$chosen))) { ?>
			<ol class="comments"><?php
		$cls = 'even';
		foreach ($this->responses as $row)
		{
			if ($this->question->state == 1 && $row->state == 1) {
				continue;
			}

			// Set the name of the reviewer
			$name = JText::_('COM_ANSWERS_ANONYMOUS');
			$ruser = new Hubzero_User_Profile();
			$ruser->load( $row->created_by );
			if ($row->anonymous != 1) {
				$name = JText::_('COM_ANSWERS_UNKNOWN');
				//$ruser =& JUser::getInstance($row->created_by);
				if (is_object($ruser)) {
					$name = $ruser->get('name');
				}
			}

			$abuse = isset($row->reports) ? $row->reports : 0;

			$cls  = ($cls == 'odd') ? 'even' : 'odd';
			$cls .= ($abuse) ? ' abusive' : '';
			if ($this->question->created_by == $row->created_by) {
				$cls .= ' author';
			}
			/*if ($this->question->state == 1 && $row->state == 1) {
				$cls .= ' chosen';
			}*/
			?>
				<li class="comment <?php echo $cls; ?>" id="c<?php echo $row->id; ?>">
					<p class="comment-member-photo">
						<span class="comment-anchor"><a name="c<?php echo $row->id; ?>"></a></span>
						<img src="<?php echo AnswersHelperMember::getMemberPhoto($ruser, $row->anonymous); ?>" alt="" />
					</p><!-- / .comment-member-photo -->
					<div class="comment-content">
					<?php if (!$abuse) { ?>
						<p class="comment-voting" id="answers_<?php echo $row->id; ?>">
						<?php
							$view = new JView( array('name'=>'rateitem') );
							$view->option = $this->option;
							$view->item = $row;
							$view->display();
						?>			
						</p><!-- / .comment-voting -->
					<?php } ?>
						<p class="comment-title">
							<strong><?php echo $name; ?></strong> 
							<a class="permalink" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=question&id='.$this->question->id.'#c'.$row->id); ?>" title="<?php echo JText::_('COM_ANSWERS_PERMALINK'); ?>">@ <span class="time"><?php echo JHTML::_('date',$row->created, '%I:%M %p', 0); ?></span> on <span class="date"><?php echo JHTML::_('date',$row->created, '%d %b, %Y', 0); ?></span></a>
						</p><!-- / .comment-title -->
					<?php if (!$abuse) { ?>
						<?php echo $parser->parse(stripslashes($row->answer), $wikiconfig); ?>
						<p class="comment-options">
							<a class="abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=answer&id='.$row->id.'&parent='.$this->question->id); ?>"><?php echo JText::_('COM_ANSWERS_REPORT_ABUSE'); ?></a>
						<?php if (!$chosen) { ?>
							<a class="showreplyform" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=reply&category=answer&id='.$this->question->id.'&refid='.$row->id.'#c'.$row->id); ?>" id="rep_<?php echo $row->id; ?>"><?php echo JText::_('COM_ANSWERS_REPLY'); ?></a> 
						<?php } ?>
						<?php if ($this->juser->get('username') == $this->question->created_by && $this->question->state == 0) { ?>
							<span class="accept"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=accept&id='.$this->question->id.'&rid='.$row->id); ?>"><?php echo JText::_('COM_ANSWERS_ACCEPT_ANSWER'); ?></a></span>
						<?php } ?>
						</p><!-- / .comment-options -->
					<?php
						$view = new JView( array('name'=>'question', 'layout'=>'addcomment') );
						$view->option = $this->option;
						$view->row = $row;
						$view->juser = $this->juser;
						$view->level = ($chosen) ? 4 : 0;
						$view->question = $this->question;
						$view->addcomment = $this->addcomment;
						$view->display();
					?>
					</div><!-- / .comment-content -->
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
						/*if ($this->abuse && $reply->reports > 0) {
							$html .= ' abusive';
						}*/
						if ($this->question->created_by == $reply->added_by) {
							$cls .= ' author';
						}
						$html .= '" id="c'.$reply->id.'r">';

						$view = new JView( array('name'=>'question', 'layout'=>'comment') );
						$view->option = $this->option;
						$view->reply = $reply;
						$view->juser = $this->juser;
						$view->id = $this->question->id;
						$view->level = ($chosen) ? 4 : 1;
						$view->abuse = (isset($this->abuse)) ? $this->abuse : '';
						$view->question = $this->question;
						$view->addcomment = $this->addcomment;
						$view->parser = $parser;
						$html .= $view->loadTemplate();

						// Another level? 
						if (count($reply->replies) > 0) {
							$html .= '<ol class="comments pass3">';
							foreach ($reply->replies as $r)
							{
								$o = ($o == 'odd') ? 'even' : 'odd';

								$html .= '<li class="comment '.$o;
								/*if ($this->abuse && $r->reports > 0) {
									$html .= ' abusive';
								}*/
								if ($this->question->created_by == $r->added_by) {
									$cls .= ' author';
								}
								$html .= '" id="c'.$r->id.'r">';

								$view = new JView( array('name'=>'question', 'layout'=>'comment') );
								$view->option = $this->option;
								$view->reply = $r;
								$view->juser = $this->juser;
								$view->id = $this->question->id;
								$view->level = ($chosen) ? 4 : 2;
								$view->abuse = (isset($this->abuse)) ? $this->abuse : '';
								$view->question = $this->question;
								$view->addcomment = $this->addcomment;
								$view->parser = $parser;
								$html .= $view->loadTemplate();

								// Yet another level?? 
								if (count($r->replies) > 0) {
									$html .= '<ol class="comments pass4">';
									foreach ($r->replies as $rr)
									{
										$o = ($o == 'odd') ? 'even' : 'odd';

										$html .= "\t".'<li class="comment '.$o;
										/*if ($this->abuse && $rr->reports > 0) {
											$html .= ' abusive';
										}*/
										$html .= '" id="c'.$rr->id.'r">';
										//$html .= AnswersHtml::comment($rr, $juser, $option, $id, $addcomment, 3, $abuse, $o).n;
										$view = new JView( array('name'=>'question', 'layout'=>'comment') );
										$view->option = $this->option;
										$view->reply = $rr;
										$view->juser = $this->juser;
										$view->id = $this->question->id;
										$view->level = ($chosen) ? 4 : 3;
										$view->abuse = (isset($this->abuse)) ? $this->abuse : '';
										$view->question = $this->question;
										$view->addcomment = $this->addcomment;
										$view->parser = $parser;
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
				} //foreach ($row->replies as $reply)
				} //if (count($row->replies) > 0) 
				?>
				<?php } else { ?>
					<p class="warning"><?php echo JText::_('COM_ANSWERS_NOTICE_POSTING_REPORTED'); ?></p>
				<?php } //if ($this->showcomments && isset($row->replies)) ?>
			</li>
		<?php } //foreach ($this->responses as $row) ?>
		</ol>
<?php } else if ($chosen) { ?>
		<div class="subject-wrap">
			<p>No other responses made.</p>
		</div>
<?php } else { ?>
		<div class="subject-wrap">
		<p><?php echo JText::_('COM_ANSWERS_NO_ANSWERS_BE_FIRST'); ?> <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=answer&id='.$this->question->id); ?>"><?php echo JText::_('COM_ANSWERS_BE_FIRST_ANSWER_THIS'); ?></a>.</p>
	<?php if ($this->banking) { ?>
		<p class="help">
			<strong><?php echo JText::_('COM_ANSWERS_DID_YOU_KNOW_ABOUT_POINTS'); ?></strong><br />
			<a href="<?php echo $this->infolink; ?>"><?php echo JText::_('COM_ANSWERS_LEARN_MORE'); ?></a> <?php echo JText::_('COM_ANSWERS_LEARN_HOW_POINTS_AWARDED'); ?>.
		</p>
		</div>
	<?php } ?>
<?php } //if ($this->responses) { ?>
	</div><!-- / .subject -->
	
<?php } else if ($reports > 0) { ?>
		</div><!-- / #questionwrap -->
		<div class="clear"></div>
	</div><!-- / .subject -->
<?php } ?>
	<div class="clear"></div>
</div><!-- / .below section -->
</div><!-- / .main section -->
