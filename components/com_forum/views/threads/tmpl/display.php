<?php 
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

ximport('Hubzero_User_Profile_Helper');
?>
<div id="content-header">
	<h2><?php echo $this->escape(stripslashes($this->post->title)); ?></h2>
</div>
<div id="content-header-extra">
	<p><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&section=' . $this->filters['section'] . '&category=' . $this->category->alias); ?>"><?php echo JText::_('&larr; All discussions'); ?></a></p>
</div>
<div class="clear"></div>

<?php
	foreach($this->notifications as $notification) {
		echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
	}
?>
<div class="main section">
	<div class="aside">
		<div class="container">
			<h3><?php echo JText::_('COM_FORUM_ALL_TAGS'); ?><span class="starter-point"></span></h3>
<?php if ($this->tags) { ?>
			<?php echo $this->tags; ?>
<?php } else { ?>
			<p><?php echo JText::_('COM_FORUM_NONE'); ?></p>
<?php } ?>
		</div><!-- / .container -->
<?php if ($this->participants) { ?>
		<div class="container">
			<h3><?php echo JText::_('COM_FORUM_PARTICIPANTS'); ?><span class="starter-point"></span></h3>
			<ul>
<?php 
	$anon = false;
	foreach ($this->participants as $participant) 
	{ 
		if (!$participant->anonymous) { 
?>
				<li><a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $participant->created_by); ?>"><?php echo $this->escape(stripslashes($participant->name)); ?></a></li>
<?php 
		} else if (!$anon) {
			$anon = true;
?>
				<li><?php echo JText::_('COM_FORUM_ANONYMOUS'); ?></li>
<?php
		}
	}
?>
			</ul>
		</div><!-- / .container -->
<?php } ?>
<?php if ($this->attachments) { ?>
		<div class="container">
			<h3><?php echo JText::_('COM_FORUM_ATTACHMENTS'); ?><span class="starter-point"></span></h3>
			<ul class="attachments">
<?php 
			foreach ($this->attachments as $attachment) 
			{
				$title = ($attachment->description) ? $attachment->description : $attachment->filename;
?>
				<li><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&section=' . $this->filters['section'] . '&category=' . $this->category->alias . '&thread=' . $attachment->parent . '&post=' . $attachment->post_id . '&file=' . $attachment->filename); ?>"><?php echo $this->escape($title); ?></a></li>
<?php 		} ?>
			</ul>
		</div><!-- / .container -->
<?php } ?>
	</div><!-- / .aside -->
	
	<div class="subject">
		<h3 class="comments-title">
			<?php echo JText::_('COM_FORUM_COMMENTS'); ?>
		</h3>
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&section=' . $this->filters['section'] . '&category='.$this->category->alias.'&thread='.$this->post->id); ?>" method="get">
		<ol class="comments">
			<?php
			if ($this->rows) {
				ximport('Hubzero_User_Profile');
				ximport('Hubzero_Wiki_Parser');

				$wikiconfig = array(
					'option'   => $this->option,
					'scope'    => 'forum',
					'pagename' => 'forum',
					'pageid'   => $this->post->id,
					'filepath' => '',
					'domain'   => $this->post->id
				);

				$p =& Hubzero_Wiki_Parser::getInstance();

				foreach ($this->rows as $row)
				{
					$name = JText::_('COM_FORUM_ANONYMOUS');
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

					$comment  = $p->parse("\n" . stripslashes($row->comment), $wikiconfig);
					$comment .= $this->attach->getAttachment(
						$row->id, 
						'index.php?option=' . $this->option . '&section=' . $this->filters['section'] . '&category=' . $this->category->alias . '&thread=' . $this->post->id . '&post=' . $row->id . '&file=', 
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
							<a class="permalink" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&section=' . $this->filters['section'] . '&category=' . $this->category->alias . '&thread=' . $this->post->id . '#c' . $row->id); ?>" title="<?php echo JText::_('COM_FORUM_PERMALINK'); ?>">@
								<span class="time"><?php echo JHTML::_('date', $row->created, $timeFormat, $tz); ?></span> <?php echo JText::_('COM_FORUM_ON'); ?> 
								<span class="date"><?php echo JHTML::_('date', $row->created, $dateFormat, $tz); ?></span>
								<?php if ($row->modified && $row->modified != '0000-00-00 00:00:00') { ?>
									&mdash; <?php echo JText::_('COM_FORUM_EDITED'); ?>
									<span class="time"><?php echo JHTML::_('date', $row->modified, $timeFormat, $tz); ?></span> <?php echo JText::_('COM_FORUM_ON'); ?> 
									<span class="date"><?php echo JHTML::_('date', $row->modified, $dateFormat, $tz); ?></span>
								<?php } ?>
							</a>
						</p>
						<?php echo $comment; ?>
						<?php if (
									$this->config->get('access-manage-thread')
									||
									(!$row->parent && $row->created_by == $juser->get('id') && 
										(
											$this->config->get('access-delete-thread') ||
											$this->config->get('access-edit-thread')
										) 
									)
									|| 
									($row->parent && $row->created_by == $juser->get('id') && 
										(
											$this->config->get('access-delete-post') ||
											$this->config->get('access-edit-post')
										)
									)
								) { ?>
						<p class="comment-options">
							<?php if ((!$row->parent && $this->config->get('access-delete-thread')) || ($row->parent && $this->config->get('access-delete-post'))) { ?>
							<a class="delete" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&section=' . $this->filters['section'] . '&category=' . $this->category->alias . '&thread=' . $row->id . '&task=delete'); ?>">
								<?php echo JText::_('COM_FORUM_DELETE'); ?>
							</a>
							<?php } ?>
							<?php if ((!$row->parent && $this->config->get('access-edit-thread')) || ($row->parent && $this->config->get('access-edit-post'))) { ?>
							<a class="edit" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&section=' . $this->filters['section'] . '&category=' . $this->category->alias . '&thread=' . $row->id . '&task=edit'); ?>">
								<?php echo JText::_('COM_FORUM_EDIT'); ?>
							</a>
							<?php } ?>
						</p>
						<?php } ?>
					</div>
<?php /*if (count($tags) > 0) { ?>
					<div class="comment-tags">
						<p><?php echo JText::_('COM_FORUM_TAGS'); ?>:</p>
						<?php echo $this->tag->buildCloud($tags); ?>
					</div><!-- / .comment-tags -->
<?php }*/ ?>
				</li>
			<?php } ?>
		<?php } else { ?>
			<li><p><?php echo JText::_('COM_FORUM_NO_REPLIES_FOUND'); ?></p></li>
		<?php } ?>
		</ol>
	    <?php 
            // @FIXME: Nick's Fix Based on Resources View
            //echo '<input type="hidden" name="topic" value="' . $this->post->id . '" />';
            $pf = $this->pageNav->getListFooter();
            //$nm = str_replace('com_','',$this->option);
            $pf = str_replace('?controller=threads&amp;', '/' . $this->category->alias . '/' . $this->post->id . '?', $pf);
			$pf = str_replace('?&amp;', '?', $pf);
            echo $pf;
            //echo $this->pageNav->getListFooter(); 
            // @FIXME: End Nick's Fix
        ?>
		</form>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .main section -->
<?php if ($this->config->get('access-create-post')) { ?>
<div class="below section">
	<h3 class="post-comment-title">
		<?php echo JText::_('COM_FORUM_ADD_COMMENT'); ?>
	</h3>			
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
	</div><!-- /.aside -->
	<div class="subject">
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&category='.$this->category->alias.'&thread='.$this->post->id); ?>" method="post" id="commentform" enctype="multipart/form-data">
			<p class="comment-member-photo">
				<a class="comment-anchor" name="commentform"></a>
<?php
				if (!$juser->get('guest')) 
				{
					$jxuser = new Hubzero_User_Profile();
					$jxuser->load($juser->get('id'));
					$thumb = Hubzero_User_Profile_Helper::getMemberPhoto($jxuser, 0);
				} 
				else 
				{
					$config = JComponentHelper::getParams('com_members');
					$thumb = $config->get('defaultpic');
					if (substr($thumb, 0, 1) != DS) 
					{
						$thumb = DS . $dfthumb;
					}
					$thumb = Hubzero_User_Profile_Helper::thumbit($thumb);
				}
?>
				<img src="<?php echo $thumb; ?>" alt="" />
			</p>
	
			<fieldset>
			<?php if ($juser->get('guest')) { ?>
				<p class="warning"><?php echo JText::_('COM_FORUM_LOGIN_COMMENT_NOTICE'); ?></p>
			<?php } else { ?>
				<p class="comment-title">
					<strong>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id')); ?>"><?php echo $this->escape($juser->get('name')); ?></a>
					</strong> 
					<span class="permalink">@
						<span class="time"><?php echo JHTML::_('date', date('Y-m-d H:i:s', time()), $timeFormat, $tz); ?></span> <?php echo JText::_('COM_FORUM_ON'); ?> 
						<span class="date"><?php echo JHTML::_('date', date('Y-m-d H:i:s', time()), $dateFormat, $tz); ?></span>
					</span>
				</p>
				
				<label for="fieldcomment">
					<?php echo JText::_('COM_FORUM_FIELD_COMMENTS'); ?>
					<?php
					ximport('Hubzero_Wiki_Editor');
					$editor = Hubzero_Wiki_Editor::getInstance();
					echo $editor->display('fields[comment]', 'fieldcomment', '', '', '35', '15');
					?>
				</label>
				
				<label>
					<?php echo JText::_('COM_FORUM_FIELD_YOUR_TAGS'); ?>:
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
				</label>
				
				<fieldset>
					<legend><?php echo JText::_('COM_FORUM_LEGEND_ATTACHMENTS'); ?></legend>
					<div class="grouping">
						<label>
							<?php echo JText::_('COM_FORUM_FIELD_FILE'); ?>:
							<input type="file" name="upload" id="upload" />
						</label>

						<label>
							<?php echo JText::_('COM_FORUM_FIELD_DESCRIPTION'); ?>:
							<input type="text" name="description" value="" />
						</label>
					</div>
				</fieldset>
		
				<label for="field-anonymous" id="comment-anonymous-label">
					<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1" /> 
					<?php echo JText::_('COM_FORUM_FIELD_ANONYMOUS'); ?>
				</label>
		
				<p class="submit">
					<input type="submit" value="<?php echo JText::_('COM_FORUM_SUBMIT'); ?>" />
				</p>
			<?php } ?>
		
				<div class="sidenote">
					<p>
						<strong><?php echo JText::_('COM_FORUM_KEEP_POLITE'); ?></strong>
					</p>
					<p>
						<?php echo JText::_('COM_FORUM_WIKI_HINT'); ?>
					</p>
				</div>
			</fieldset>
			<input type="hidden" name="fields[category_id]" value="<?php echo $this->post->category_id; ?>" />
			<input type="hidden" name="fields[parent]" value="<?php echo $this->post->id; ?>" />
			<input type="hidden" name="fields[state]" value="1" />
			<input type="hidden" name="fields[id]" value="" />
			<input type="hidden" name="fields[group_id]" value="0" />
	
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="threads" />
			<input type="hidden" name="task" value="save" />
		</form>
	</div><!-- / .subject -->
	<div class="clear"></div>
<?php } ?>
</div><!-- / .below section -->