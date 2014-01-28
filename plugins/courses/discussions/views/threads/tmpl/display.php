<?php 
defined('_JEXEC') or die('Restricted access');
$juser = JFactory::getUser();

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

$p = Hubzero_Wiki_Parser::getInstance();

$base = 'index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum';
?>
<div class="filters">
	<div class="filters-inner">
		<p>
			<a class="comments btn" href="<?php echo JRoute::_($base . '&unit=' . $this->category->alias); ?>">
				<?php echo JText::_('All discussions'); ?>
			</a>
		</p>
		<h3 class="thread-title">
			<?php echo $this->escape(stripslashes($this->post->title)); ?>
		</h3>
	</div>
</div>

<div class="main section">

<?php foreach ($this->notifications as $notification) { ?>
	<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
<?php } ?>

	<div class="aside">
		<div class="container">
			<h4><?php echo JText::_('PLG_COURSES_DISCUSSIONS_ALL_TAGS'); ?></h4>
		<?php if ($this->tags) { ?>
			<?php echo $this->tags; ?>
		<?php } else { ?>
			<p><?php echo JText::_('PLG_COURSES_DISCUSSIONS_NONE'); ?></p>
		<?php } ?>
		</div><!-- / .container -->
		<div class="container">
			<h4><?php echo JText::_('PLG_COURSES_DISCUSSIONS_PARTICIPANTS'); ?></h4>
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
				<li><?php echo JText::_('PLG_COURSES_DISCUSSIONS_ANONYMOUS'); ?></li>
		<?php
				}
			}
		?>
			</ul>
	<?php } ?>
		</div><!-- / .container -->
		<div class="container">
			<h4><?php echo JText::_('PLG_COURSES_DISCUSSIONS_ATTACHMENTS'); ?></h4>
		<?php if ($this->attachments) { ?>
			<ul class="attachments">
			<?php 
			foreach ($this->attachments as $attachment) 
			{
				$title = ($attachment->description) ? $attachment->description : $attachment->filename;
			?>
				<li><a href="<?php echo JRoute::_($base . '&unit=' . $this->category->alias . '&b=' . $attachment->parent . '&c=' . $attachment->post_id . '/' . $attachment->filename); ?>"><?php echo $this->escape($title); ?></a></li>
			<?php } ?>
			</ul>
	<?php } else { ?>
			<p><?php echo JText::_('PLG_COURSES_DISCUSSIONS_NONE'); ?></p>
	<?php } ?>
		</div><!-- / .container -->
	</div><!-- / .aside  -->

	<div class="subject">
		<!-- <h4 class="comments-title">
			<?php echo JText::_('PLG_COURSES_DISCUSSIONS_COMMENTS'); ?>
		</h4> -->
		<form action="<?php echo JRoute::_($base . '&unit=' . $this->category->alias . '&b=' . $this->post->id); ?>" method="get">
			<?php
			if ($this->rows) {
				$last = '0000-00-00 00:00:00';
				foreach ($this->rows as $row)
				{
					if ($row->created > $last)
					{
						$last = $row->created;
					}
				}
				echo '<input type="hidden" name="lastchange" id="lastchange" value="' . $last . '" />';
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
				<p><?php echo JText::_('PLG_COURSES_DISCUSSIONS_NO_REPLIES_FOUND'); ?></p>
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
		<?php echo JText::_('PLG_COURSES_DISCUSSIONS_ADD_COMMENT'); ?>
	</h3>
	<div class="aside">
		<p><?php echo JText::_('PLG_COURSES_DISCUSSIONS_EDIT_HINT'); ?></p>
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
				$now = JFactory::getDate();
				?>
				<img src="<?php echo $jxuser->getPicture($anon); ?>" alt="" />
			</p>

			<fieldset>
			<?php if ($juser->get('guest')) { ?>
				<p class="warning"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_LOGIN_COMMENT_NOTICE'); ?></p>
			<?php } else { ?>
				<p class="comment-title">
					<strong>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id')); ?>"><?php echo $this->escape($juser->get('name')); ?></a>
					</strong> 
					<span class="permalink">
						<span class="comment-date-at"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_AT'); ?><</span>
						<span class="time"><time datetime="<?php echo $now; ?>"><?php echo JHTML::_('date', $now, JText::_('TIME_FORMAt_HZ1')); ?></time></span> 
						<span class="comment-date-on"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_ON'); ?></span> 
						<span class="date"><time datetime="<?php echo $now; ?>"><?php echo JHTML::_('date', $now, JText::_('DATE_FORMAt_HZ1')); ?></time></span>
					</span>
				</p>

				<label for="field_comment">
					<?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_COMMENTS'); ?>
					<?php
					ximport('Hubzero_Wiki_Editor');
					$editor = Hubzero_Wiki_Editor::getInstance();
					echo $editor->display('fields[comment]', 'field_comment', '', 'minimal no-footer', '35', '15');
					?>
				</label>

				<fieldset>
					<legend><?php echo JText::_('PLG_COURSES_DISCUSSIONS_LEGEND_ATTACHMENTS'); ?></legend>
					<div class="grouping">
						<label>
							<?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_FILE'); ?>:
							<input type="file" name="upload" id="upload" />
						</label>

						<label>
							<?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_DESCRIPTION'); ?>:
							<input type="text" name="description" value="" />
						</label>
					</div>
				</fieldset>

				<label for="field-anonymous" id="comment-anonymous-label">
					<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1" /> 
					<?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_ANONYMOUS'); ?>
				</label>

				<p class="submit">
					<input type="submit" value="<?php echo JText::_('PLG_COURSES_DISCUSSIONS_SUBMIT'); ?>" />
				</p>
			<?php } ?>

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