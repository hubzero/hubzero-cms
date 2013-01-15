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

$juser = JFactory::getUser();

$dateFormat = '%d %b, %Y';
$timeFormat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M, Y';
	$timeFormat = 'h:i a';
	$tz = true;
}

$base = 'index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias');

ximport('Hubzero_User_Profile_Helper');
?>

<div class="below section">
	<h4 class="post-comment-title">
		<?php echo JText::_('PLG_COURSES_FORUM_ADD_COMMENT'); ?>
	</h4>
	<!-- <div class="aside">
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
	</div><- /.aside -
	<div class="subject"> -->
		<form action="<?php echo JRoute::_($base . '&active=outline&unit=' . $this->unit->get('alias') . '&b=' . $this->lecture->get('alias')); ?>" method="post" id="commentform" enctype="multipart/form-data">
			<p class="comment-member-photo">
				<a class="comment-anchor" name="commentform"></a>
				<?php
				$anone = 1;
				if (!$juser->get('guest')) 
				{
					$anon = 0;
				}
				$now = date('Y-m-d H:i:s', time());
				?>
				<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($juser, $anon); ?>" alt="<?php echo JText::_('User photo'); ?>" />
			</p>

			<fieldset>
			<?php if ($juser->get('guest')) { ?>
				<p class="warning"><?php echo JText::_('PLG_COURSES_FORUM_LOGIN_COMMENT_NOTICE'); ?></p>
			<?php } else { ?>
				<p class="comment-title">
					<strong>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id')); ?>"><?php echo $this->escape($juser->get('name')); ?></a>
					</strong> 
					<span class="permalink">
						<span class="comment-date-at">@</span>
						<span class="time"><time datetime="<?php echo $now; ?>"><?php echo JHTML::_('date', $now, $timeFormat, $tz); ?></time></span> <span class="comment-date-on"><?php echo JText::_('PLG_COURSES_FORUM_ON'); ?></span> 
						<span class="date"><time datetime="<?php echo $now; ?>"><?php echo JHTML::_('date', $now, $dateFormat, $tz); ?></time></span>
					</span>
				</p>

				<label for="field_comment">
					<span class="label-text"><?php echo JText::_('PLG_COURSES_FORUM_FIELD_COMMENTS'); ?></span>
					<?php
					ximport('Hubzero_Wiki_Editor');
					$editor = Hubzero_Wiki_Editor::getInstance();
					echo $editor->display('fields[comment]', 'field_comment', '', 'minimal no-footer', '35', '5');
					?>
				</label>
				
				<?php /* <label>
					<?php echo JText::_('PLG_COURSES_FORUM_FIELD_YOUR_TAGS'); ?>:
<?php 
		$tags = $this->tModel->get_tag_string($this->post->id, 0, 0, $juser->get('id'));
		
		JPluginHelper::importPlugin('hubzero');
		$dispatcher = JDispatcher::getInstance();
		$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $tags)) );
		if (count($tf) > 0) {
			echo $tf[0];
		} else {
			echo '<input type="text" name="tags" value="' . $tags . '" />';
		}
?>
				</label> */ ?>

				<label for="field-upload">
					<span class="label-text"><?php echo JText::_('PLG_COURSES_FORUM_LEGEND_ATTACHMENTS'); ?>:</span>
					<input type="file" name="upload" id="field-upload" />
				</label>

				<!-- <fieldset>
					<legend><?php echo JText::_('PLG_COURSES_FORUM_LEGEND_ATTACHMENTS'); ?></legend>
					<div class="grouping">
						<label for="field-upload">
							<?php echo JText::_('PLG_COURSES_FORUM_FIELD_FILE'); ?>:
							<input type="file" name="upload" id="field-upload" />
						</label>

						<label for="field-description">
							<?php echo JText::_('PLG_COURSES_FORUM_FIELD_DESCRIPTION'); ?>:
							<input type="text" name="description" id="field-description" value="" />
						</label>
					</div>
				</fieldset> -->

				<label for="field-anonymous" id="comment-anonymous-label">
					<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1" /> 
					<?php echo JText::_('PLG_COURSES_FORUM_FIELD_ANONYMOUS'); ?>
				</label>

				<p class="submit">
					<input type="submit" value="<?php echo JText::_('PLG_COURSES_FORUM_SUBMIT'); ?>" />
				</p>
			<?php } ?>
			</fieldset>
			<input type="hidden" name="fields[category_id]" value="<?php echo $this->post->get('category_id'); ?>" />
			<input type="hidden" name="fields[parent]" value="<?php echo $this->post->get('id'); ?>" />
			<input type="hidden" name="fields[state]" value="1" />
			<input type="hidden" name="fields[scope]" value="course" />
			<input type="hidden" name="fields[scope_id]" value="<?php echo $this->post->get('scope_id'); ?>" />
			<input type="hidden" name="fields[id]" value="" />
			<input type="hidden" name="fields[object_id]" value="<?php echo $this->post->get('object_id'); ?>" />
	
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
			<input type="hidden" name="offering" value="<?php echo $this->course->offering()->get('alias'); ?>" />
			<input type="hidden" name="active" value="forum" />
			<input type="hidden" name="action" value="savethread" />
			<input type="hidden" name="section" value="<?php echo $this->filters['section']; ?>" />
			<input type="hidden" name="return" value="<?php echo base64_encode(JRoute::_($base . '&active=outline&unit=' . $this->unit->get('alias') . '&b=' . $this->lecture->get('alias'))); ?>" />
		</form>
	<!-- </div>/ .subject -->
	<div class="clear"></div>
</div><!-- / .below section -->

<div class="below section">

<?php foreach ($this->notifications as $notification) { ?>
	<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
<?php } ?>

	<div class="comments-wrap">
		<h4 class="comments-title">
			<?php echo JText::_('PLG_COURSES_FORUM_COMMENTS'); ?> <span class="comment-count">(<?php echo $this->total - 1; ?>)</span>
		</h4>
		<form action="<?php echo JRoute::_($base . '&active=outline&unit=' . $this->unit->get('alias') . '&b=' . $this->lecture->get('alias')); ?>" method="get">
			<ol class="comments">
			<?php
			if ($this->rows && count($this->rows) > 1) {
				ximport('Hubzero_User_Profile');
				ximport('Hubzero_Wiki_Parser');

				$wikiconfig = array(
					'option'   => $this->option,
					'scope'    => 'forum',
					'pagename' => 'forum',
					'pageid'   => $this->post->get('id'),
					'filepath' => '',
					'domain'   => $this->post->get('id')
				);

				$p =& Hubzero_Wiki_Parser::getInstance();

				foreach ($this->rows as $row)
				{
					if (!$row->parent)
					{
						continue;
					}
					$name = JText::_('PLG_COURSES_FORUM_ANONYMOUS');
					$huser = '';
					if (!$row->anonymous) 
					{
						$huser = new Hubzero_User_Profile();
						$huser->load($row->created_by);
						if (is_object($huser) && $huser->get('name')) 
						{
							$name = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $row->created_by) . '">' . $this->escape(stripslashes($huser->get('name'))) . '</a>';
						}
					}

					$comment  = $p->parse(stripslashes($row->comment), $wikiconfig);
					$comment .= $this->attach->getAttachment(
						$row->id, 
						$base . '&active=outline&unit=' . $this->unit->get('alias') . '&b=' . $row->id, 
						$this->config
					);
					
					//$tags = $this->tag->get_tags_on_object($row->id, 0, 0, $row->created_by);
			?>
				<li class="comment<?php if (!$row->parent) { echo ' start'; } ?>" id="c<?php echo $row->id; ?>">
					<p class="comment-member-photo">
						<a class="comment-anchor" name="c<?php echo $row->id; ?>"></a>
						<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($huser, $row->anonymous); ?>" alt="" />
					</p>
					<div class="comment-content">
						<p class="comment-title">
							<strong><?php echo $name; ?></strong> 
							<a class="permalink" href="<?php echo JRoute::_($base . '&active=forum&unit=' . $this->unit->get('alias') . '&b=' . $this->post->id . '#c' . $row->id); ?>" title="<?php echo JText::_('PLG_COURSES_FORUM_PERMALINK'); ?>">
								<span class="comment-date-at">@</span>
								<span class="time"><time datetime="<?php echo $row->created; ?>"><?php echo JHTML::_('date', $row->created, $timeFormat, $tz); ?></time></span> 
								<span class="comment-date-on"><?php echo JText::_('PLG_COURSES_FORUM_ON'); ?></span> 
								<span class="date"><time datetime="<?php echo $row->created; ?>"><?php echo JHTML::_('date', $row->created, $dateFormat, $tz); ?></time></span>
								<?php if ($row->modified && $row->modified != '0000-00-00 00:00:00') { ?>
									&mdash; <?php echo JText::_('PLG_COURSES_FORUM_EDITED'); ?>
									<span class="time"><time datetime="<?php echo $row->modified; ?>"><?php echo JHTML::_('date', $row->modified, $timeFormat, $tz); ?></time></span> <span class="comment-date-on"><?php echo JText::_('PLG_COURSES_FORUM_ON'); ?></span> 
									<span class="date"><time datetime="<?php echo $row->modified; ?>"><?php echo JHTML::_('date', $row->modified, $dateFormat, $tz); ?></time></span>
								<?php } ?>
							</a>
						</p>
						<?php echo $comment; ?>
						<?php if ($this->config->get('access-edit-thread') || $juser->get('id') == $row->created_by) { ?>
						<p class="comment-options">
							<?php if ($this->config->get('access-delete-thread')) { ?>
							<a class="delete" href="<?php echo JRoute::_($base . '&active=forum&unit=' . $this->unit->get('alias') . '&b=' . $row->id . '&c=delete'); ?>">
								<?php echo JText::_('PLG_COURSES_FORUM_DELETE'); ?>
							</a>
							<?php } ?>
							<?php if ($this->config->get('access-edit-thread')) { ?>
							<a class="edit" href="<?php echo JRoute::_($base . '&active=forum&unit=' . $this->unit->get('alias') . '&b=' . $row->id . '&c=edit'); ?>">
								<?php echo JText::_('PLG_COURSES_FORUM_EDIT'); ?>
							</a>
							<?php } ?>
						</p>
						<?php } ?>
					</div>
				</li>
			<?php } ?>
		<?php } else { ?>
			<li><p><?php echo JText::_('PLG_COURSES_FORUM_NO_REPLIES_FOUND'); ?></p></li>
		<?php } ?>
		</ol>
		<?php 
		$this->pageNav->setAdditionalUrlParam('gid', $this->course->get('alias'));
		$this->pageNav->setAdditionalUrlParam('offering', $this->course->offering()->get('alias'));
		$this->pageNav->setAdditionalUrlParam('active', 'outline');
		$this->pageNav->setAdditionalUrlParam('unit', $this->unit->get('alias'));
		$this->pageNav->setAdditionalUrlParam('b', $this->lecture->get('alias'));

		echo $this->pageNav->getListFooter();
		?>
		</form>
 	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .main section -->