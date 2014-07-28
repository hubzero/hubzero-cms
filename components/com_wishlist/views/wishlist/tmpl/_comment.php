<?php
defined('_JEXEC') or die('Restricted access');

	$juser = JFactory::getUser();

	$cls = isset($this->cls) ? $this->cls : 'odd';

	if ($this->wish->get('proposed_by') == $this->comment->get('created_by'))
	{
		$cls .= ' author';
	}

	$name = JText::_('COM_WISHLIST_ANONYMOUS');
	$huser = new \Hubzero\User\Profile;
	if (!$this->comment->get('anonymous'))
	{
		$huser = $this->comment->creator();
		if (is_object($huser) && $huser->get('name'))
		{
			$name = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $huser->get('uidNumber')) . '">' . $this->escape(stripslashes($huser->get('name'))) . '</a>';
		}
	}

	if ($this->comment->isReported())
	{
		$cls .= ' abusive';
		$comment = '<p class="warning">' . JText::_('COM_WISHLIST_COMMENT_REPORTED_AS_ABUSIVE') . '</p>';
	}
	else
	{
		$comment  = $this->comment->content('parsed');
	}

	$this->comment->set('listcategory', $this->wishlist->get('category'));
	$this->comment->set('listreference', $this->wishlist->get('referenceid'));
?>
	<li class="comment <?php echo $cls; ?>" id="c<?php echo $this->comment->get('id'); ?>">
		<p class="comment-member-photo">
			<img src="<?php echo $this->comment->creator()->getPicture($this->comment->get('anonymous')); ?>" alt="" />
		</p>
		<div class="comment-content">
			<p class="comment-title">
				<strong><?php echo $name; ?></strong>
				<a class="permalink" href="<?php echo JRoute::_($this->wish->link() . '#c' . $this->comment->get('id')); ?>" title="<?php echo JText::_('COM_WISHLIST_PERMALINK'); ?>">
					<span class="comment-date-at"><?php echo JText::_('COM_WISHLIST_AT'); ?></span>
					<span class="time"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('time'); ?></time></span>
					<span class="comment-date-on"><?php echo JText::_('COM_WISHLIST_ON'); ?></span>
					<span class="date"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('date'); ?></time></span>
				</a>
			</p>

			<div class="comment-body">
				<?php echo $comment; ?>
			</div>

			<?php if ($attachment = $this->comment->get('attachment')) { ?>
			<p class="comment-attachment icon-attachment">
				<?php echo $attachment; ?>
			</p>
			<?php } ?>

			<p class="comment-options">
			<?php /*if ($this->config->get('access-edit-thread')) { // || $juser->get('id') == $this->comment->created_by ?>
				<?php if ($this->config->get('access-delete-thread')) { ?>
					<a class="icon-delete delete" href="<?php echo JRoute::_($this->wish->link() . '&action=delete&comment=' . $this->comment->get('id')); ?>"><!--
						--><?php echo JText::_('COM_WISHLIST_DELETE'); ?><!--
					--></a>
				<?php } ?>
				<?php if ($this->config->get('access-edit-thread')) { ?>
					<a class="icon-edit edit" href="<?php echo JRoute::_($this->wish->link() . '&action=edit&comment=' . $this->comment->get('id')); ?>"><!--
						--><?php echo JText::_('COM_WISHLIST_EDIT'); ?><!--
					--></a>
				<?php } ?>
			<?php }*/ ?>
			<?php if (!$this->comment->isReported()) { ?>
				<?php if ($this->depth < $this->wish->config()->get('comments_depth', 3)) { ?>
					<?php if (JRequest::getInt('reply', 0) == $this->comment->get('id')) { ?>
					<a class="icon-reply reply active" data-txt-active="<?php echo JText::_('COM_WISHLIST_CANCEL'); ?>" data-txt-inactive="<?php echo JText::_('COM_WISHLIST_REPLY'); ?>" href="<?php echo JRoute::_($this->comment->link()); ?>" data-rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
					--><?php echo JText::_('COM_WISHLIST_CANCEL'); ?><!--
				--></a>
					<?php } else { ?>
					<a class="icon-reply reply" data-txt-active="<?php echo JText::_('COM_WISHLIST_CANCEL'); ?>" data-txt-inactive="<?php echo JText::_('COM_WISHLIST_REPLY'); ?>" href="<?php echo JRoute::_($this->comment->link('reply')); ?>" data-rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
					--><?php echo JText::_('COM_WISHLIST_REPLY'); ?><!--
				--></a>
					<?php } ?>
				<?php } ?>
					<a class="icon-abuse abuse" data-txt-flagged="<?php echo JText::_('COM_WISHLIST_COMMENT_REPORTED_AS_ABUSIVE'); ?>" href="<?php echo JRoute::_($this->comment->link('report')); ?>"><!--
					--><?php echo JText::_('COM_WISHLIST_REPORT_ABUSE'); ?><!--
				--></a>
			<?php } ?>
			</p>

		<?php if ($this->depth < $this->wish->config()->get('comments_depth', 3)) { ?>
			<div class="addcomment comment-add<?php if (JRequest::getInt('reply', 0) != $this->comment->get('id')) { echo ' hide'; } ?>" id="comment-form<?php echo $this->comment->get('id'); ?>">
				<?php if ($juser->get('guest')) { ?>
				<p class="warning">
					<?php echo JText::sprintf('COM_WISHLIST_PLEASE_LOGIN_TO_COMMENT', '<a href="' . JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($this->wish->link(), false, true))) . '">' . JText::_('COM_WISHLIST_LOGIN') . '</a>'); ?>
				</p>
				<?php } else { ?>
				<form id="cform<?php echo $this->comment->get('id'); ?>" action="<?php echo JRoute::_($this->wish->link()); ?>" method="post" enctype="multipart/form-data">
					<fieldset>
						<legend><span><?php echo JText::sprintf('COM_WISHLIST_REPLYING_TO', (!$this->comment->get('anonymous') ? $name : JText::_('COM_WISHLIST_ANONYMOUS'))); ?></span></legend>

						<input type="hidden" name="item_type" value="<?php echo $this->comment->get('item_type') ?>" />
						<input type="hidden" name="item_id" value="<?php echo $this->comment->get('item_id'); ?>" />
						<input type="hidden" name="parent" value="<?php echo $this->comment->get('id'); ?>" />

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="listid" value="<?php echo $this->escape($this->wish->get('wishlist')); ?>" />
						<input type="hidden" name="wishid" value="<?php echo $this->escape($this->wish->get('id')); ?>" />
						<input type="hidden" name="task" value="savereply" />
						<input type="hidden" name="referenceid" value="<?php echo $this->wishlist->get('referenceid'); ?>" />
						<input type="hidden" name="cat" value="wish" />

						<?php echo JHTML::_('form.token'); ?>

						<label for="comment_<?php echo $this->comment->get('id'); ?>_content">
							<span class="label-text"><?php echo JText::_('COM_WISHLIST_ENTER_COMMENTS'); ?></span>
							<?php
							echo JFactory::getEditor()->display('content', '', '', '', 35, 4, false, 'comment_' . $this->comment->get('id') . '_content', null, null, array('class' => 'minimal no-footer'));
							?>
						</label>

						<label class="comment-anonymous-label" for="comment-<?php echo $this->comment->get('id'); ?>-anonymous">
							<input class="option" type="checkbox" name="anonymous" id="comment-<?php echo $this->comment->get('id'); ?>-anonymous" value="1" />
							<?php echo JText::_('COM_WISHLIST_POST_COMMENT_ANONYMOUSLY'); ?>
						</label>

						<p class="submit">
							<input type="submit" value="<?php echo JText::_('COM_WISHLIST_SUBMIT'); ?>" />
						</p>
					</fieldset>
				</form>
				<?php } ?>
			</div><!-- / .addcomment -->
		<?php } ?>
		</div><!-- / .comment-content -->
		<?php
		if ($this->depth < $this->wish->config()->get('comments_depth', 3))
		{
			$this->view('_list')
			     ->set('parent', $this->comment->get('id'))
			     ->set('cls', $cls)
			     ->set('depth', $this->depth)
			     ->set('option', $this->option)
			     ->set('comments', $this->comment->replies('list'))
			     ->set('wishlist', $this->wishlist)
			     ->set('wish', $this->wish)
			     ->display();
		}
		?>
	</li>