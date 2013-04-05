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

$base = 'index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum';
?>
<ul>
	<li>
		<a class="comments" href="<?php echo JRoute::_($base . '&unit=' . $this->category->alias); ?>">
			<?php echo JText::_('All discussions'); ?>
		</a>
	</li>
</ul>

<div class="main section">
	<h3 class="thread-title">
		<?php echo $this->escape(stripslashes($this->post->title)); ?>
	</h3>

<?php foreach ($this->notifications as $notification) { ?>
	<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
<?php } ?>

	<div class="aside">
		<div class="container">
			<h4><?php echo JText::_('PLG_COURSES_FORUM_ALL_TAGS'); ?><span class="starter-point"></span></h4>
<?php if ($this->tags) { ?>
			<?php echo $this->tags; ?>
<?php } else { ?>
			<p><?php echo JText::_('PLG_COURSES_FORUM_NONE'); ?></p>
<?php } ?>
		</div><!-- / .container -->
		<div class="container">
			<h4><?php echo JText::_('PLG_COURSES_FORUM_PARTICIPANTS'); ?><span class="starter-point"></span></h4>
<?php if ($this->participants) { ?>
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
				<li><?php echo JText::_('PLG_COURSES_FORUM_ANONYMOUS'); ?></li>
<?php
		}
	}
?>
			</ul>
<?php } ?>
		</div><!-- / .container -->
		<div class="container">
			<h4><?php echo JText::_('PLG_COURSES_FORUM_ATTACHMENTS'); ?><span class="starter-point"></span></h4>
<?php if ($this->attachments) { ?>
			<ul class="attachments">
<?php 
			foreach ($this->attachments as $attachment) 
			{
				$title = ($attachment->description) ? $attachment->description : $attachment->filename;
?>
				<li><a href="<?php echo JRoute::_($base . '&unit=' . $this->category->alias . '&b=' . $attachment->parent . '&c=' . $attachment->post_id . '/' . $attachment->filename); ?>"><?php echo $this->escape($title); ?></a></li>
<?php 		} ?>
			</ul>
<?php } ?>
		</div><!-- / .container -->
	</div><!-- / .aside  -->

	<div class="subject">
		<!-- <h4 class="comments-title">
			<?php echo JText::_('PLG_COURSES_FORUM_COMMENTS'); ?>
		</h4> -->
		<form action="<?php echo JRoute::_($base . '&unit=' . $this->category->alias . '&b=' . $this->post->id); ?>" method="get">
			<?php if (!$this->post->object_id) {
				/*$view = new Hubzero_Plugin_View(
					array(
						'folder'  => 'courses',
						'element' => 'forum',
						'name'    => 'threads',
						'layout'  => 'comment'
					)
				);
				$view->option     = $this->option;
				$view->comment    = $this->post;
				$view->post       = $this->post;
				$view->unit       = $this->unit;
				$view->lecture    = $this->lecture;
				$view->config     = $this->config;
				$view->depth      = 0;
				$view->cls        = 'even';
				$view->base       = $base;
				$view->parser     = $p;
				$view->wikiconfig = $wikiconfig;
				$view->attach     = $this->attach;
				$view->course     = $this->course;
				$view->display();*/
				
			} ?>
			<?php
			if ($this->rows) {
				$view = new Hubzero_Plugin_View(
					array(
						'folder'  => 'courses',
						'element' => 'forum',
						'name'    => 'threads',
						'layout'  => 'list'
					)
				);
				$view->option     = $this->option;
				$view->comments   = $this->rows;
				$view->post       = $this->post;
				$view->unit       = $this->category->alias;//$this->unit->get('alias');
				$view->lecture    = $this->post->id;
				$view->config     = $this->config;
				$view->depth      = 0;
				$view->cls        = 'odd';
				$view->base       = $base;
				$view->parser     = $p;
				$view->wikiconfig = $wikiconfig;
				$view->attach     = $this->attach;
				$view->course     = $this->course;
				$view->display();
			} else { ?>
				<p><?php echo JText::_('PLG_COURSES_FORUM_NO_REPLIES_FOUND'); ?></p>
		<?php } ?>
<?php 
		$this->pageNav->setAdditionalUrlParam('gid', $this->course->get('alias'));
		$this->pageNav->setAdditionalUrlParam('offering', $this->offering->get('alias'));
		$this->pageNav->setAdditionalUrlParam('active', 'forum');
		$this->pageNav->setAdditionalUrlParam('unit', $this->category->alias);
		$this->pageNav->setAdditionalUrlParam('b', $this->post->id);

		echo $this->pageNav->getListFooter();
?>
		</form>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .main section -->

<div class="below section">
	<h3 class="post-comment-title">
		<?php echo JText::_('PLG_COURSES_FORUM_ADD_COMMENT'); ?>
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
		<form action="<?php echo JRoute::_($base . '&unit=' . $this->category->alias . '&b=' . $this->post->id); ?>" method="post" id="commentform" enctype="multipart/form-data">
			<p class="comment-member-photo">
				<a class="comment-anchor" name="commentform"></a>
<?php
				$anon = 1;
				$jxuser = Hubzero_User_Profile::getInstance($juser->get('id'));
				if (!$juser->get('guest')) 
				{
					$anon = 0;
				}
				$now = date('Y-m-d H:i:s', time());
?>
				<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($jxuser, $anon); ?>" alt="" />
			</p>
	
			<fieldset>
			<?php if ($juser->get('guest')) { ?>
				<p class="warning"><?php echo JText::_('PLG_COURSES_FORUM_LOGIN_COMMENT_NOTICE'); ?></p>
			<?php } else { ?>
				<p class="comment-title">
					<strong>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id')); ?>"><?php echo $this->escape($juser->get('name')); ?></a>
					</strong> 
					<span class="permalink"><span class="comment-date-at">@</span>
						<span class="time"><time datetime="<?php echo $now; ?>"><?php echo JHTML::_('date', $now, $timeFormat, $tz); ?></time></span> <span class="comment-date-on"><?php echo JText::_('PLG_COURSES_FORUM_ON'); ?></span> 
						<span class="date"><time datetime="<?php echo $now; ?>"><?php echo JHTML::_('date', $now, $dateFormat, $tz); ?></time></span>
					</span>
				</p>
				
				<label for="field_comment">
					<?php echo JText::_('PLG_COURSES_FORUM_FIELD_COMMENTS'); ?>
					<?php
					ximport('Hubzero_Wiki_Editor');
					$editor = Hubzero_Wiki_Editor::getInstance();
					echo $editor->display('fields[comment]', 'field_comment', '', 'minimal no-footer', '35', '15');
					?>
				</label>
				<!-- 
				<label>
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
				</label>
				-->
				<fieldset>
					<legend><?php echo JText::_('PLG_COURSES_FORUM_LEGEND_ATTACHMENTS'); ?></legend>
					<div class="grouping">
						<label>
							<?php echo JText::_('PLG_COURSES_FORUM_FIELD_FILE'); ?>:
							<input type="file" name="upload" id="upload" />
						</label>

						<label>
							<?php echo JText::_('PLG_COURSES_FORUM_FIELD_DESCRIPTION'); ?>:
							<input type="text" name="description" value="" />
						</label>
					</div>
				</fieldset>
		
				<label for="field-anonymous" id="comment-anonymous-label">
					<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1" /> 
					<?php echo JText::_('PLG_COURSES_FORUM_FIELD_ANONYMOUS'); ?>
				</label>
		
				<p class="submit">
					<input type="submit" value="<?php echo JText::_('PLG_COURSES_FORUM_SUBMIT'); ?>" />
				</p>
			<?php } ?>
			<!-- 
				<div class="sidenote">
					<p>
						<strong><?php echo JText::_('PLG_COURSES_FORUM_KEEP_POLITE'); ?></strong>
					</p>
					<p>
						<?php echo JText::_('PLG_COURSES_FORUM_WIKI_HINT'); ?>
					</p>
				</div>
				-->
			</fieldset>
			<input type="hidden" name="fields[category_id]" value="<?php echo $this->post->category_id; ?>" />
			<input type="hidden" name="fields[parent]" value="<?php echo $this->post->id; ?>" />
			<input type="hidden" name="fields[state]" value="1" />
			<input type="hidden" name="fields[scope]" value="course" />
			<input type="hidden" name="fields[scope_id]" value="<?php echo $this->offering->get('id'); ?>" />
			<input type="hidden" name="fields[id]" value="" />
	
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
			<input type="hidden" name="offering" value="<?php echo $this->offering->get('alias'); ?>" />
			<input type="hidden" name="active" value="forum" />
			<input type="hidden" name="action" value="savethread" />
			<input type="hidden" name="section" value="<?php echo $this->filters['section']; ?>" />
		</form>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .below section -->