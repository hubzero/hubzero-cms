<?php
defined('_JEXEC') or die('Restricted access');

	$juser = JFactory::getUser();

	$cls = isset($this->cls) ? $this->cls : 'odd';

	$name = JText::_('PLG_RESOURCES_REVIEWS_ANONYMOUS');
	$huser = new \Hubzero\User\Profile;
	if (!$this->comment->get('anonymous'))
	{
		$huser = $this->comment->creator();
		if (is_object($huser) && $huser->get('name'))
		{
			$name = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $huser->get('uidNumber')) . '">' . $this->escape(stripslashes($huser->get('name'))) . '</a>';
		}
	}

	$this->comment->set('item_type', 'review');
	//$this->comment->set('parent', $this->comment->get('id'));

	if ($this->comment->isReported())
	{
		$comment = '<p class="warning">' . JText::_('PLG_RESOURCES_REVIEWS_NOTICE_POSTING_REPORTED') . '</p>';
	}
	else
	{
		$comment  = $this->comment->content('parsed');
	}

	if ($this->comment->get('resource_id'))
	{
		$this->comment->set('created_by', $this->comment->get('user_id'));
		$this->comment->set('item_id', $this->comment->get('id'));
		$this->comment->set('parent', 0);

		switch ($this->comment->get('rating', 0))
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
	}

?>
	<li class="comment <?php echo $cls; ?>" id="c<?php echo $this->comment->get('id'); ?>">
		<p class="comment-member-photo">
			<img src="<?php echo $huser->getPicture($this->comment->get('anonymous')); ?>" alt="" />
		</p>
		<div class="comment-content">
		<?php if (!$this->comment->isReported() && $this->comment->get('resource_id')) { ?>
			<p class="comment-voting voting" id="answers_<?php echo $this->comment->get('id'); ?>">
				<?php
				$view = $this->view('_rateitem')
							->set('option', $this->option)
							->set('item', $this->comment)
							->set('type', 'review')
							->set('vote', '')
							->set('id', '');
				if (!$juser->get('guest'))
				{
					if ($this->comment->get('created_by') == $juser->get('username'))
					{
						$view->set('vote', $this->comment->get('vote'));
						$view->set('id', $this->comment->get('id'));
					}
				}
				$view->display();
				?>
			</p><!-- / .comment-voting -->
		<?php } ?>

			<p class="comment-title">
				<strong><?php echo $name; ?></strong>
				<a class="permalink" href="<?php echo JRoute::_($this->base . '#c' . $this->comment->get('id')); ?>" title="<?php echo JText::_('PLG_RESOURCES_REVIEWS_PERMALINK'); ?>">
					<span class="comment-date-at">@</span>
					<span class="time"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('time'); ?></time></span>
					<span class="comment-date-on"><?php echo JText::_('PLG_RESOURCES_REVIEWS_ON'); ?></span>
					<span class="date"><time datetime="<?php echo $this->comment->created(); ?>"><?php echo $this->comment->created('date'); ?></time></span>
				</a>
			</p>
			<?php if ($this->comment->get('resource_id')) { ?>
			<p>
				<span class="avgrating<?php echo $class; ?>"><span><?php echo JText::sprintf('PLG_RESOURCES_REVIEWS_OUT_OF_5_STARS', $this->comment->get('rating', 0)); ?></span></span>
			</p>
			<?php } ?>

	<?php if (JRequest::getWord('action') == 'edit' && JRequest::getInt('comment') == $this->comment->get('id')) { ?>
			<form id="cform<?php echo $this->comment->get('id'); ?>" class="comment-edit" action="<?php echo JRoute::_($this->base); ?>" method="post" enctype="multipart/form-data">
				<fieldset>
					<legend><span><?php echo JText::_('PLG_RESOURCES_REVIEWS_EDIT'); ?></span></legend>

					<input type="hidden" name="comment[id]" value="<?php echo $this->comment->get('id'); ?>" />
					<input type="hidden" name="comment[item_type]" value="<?php echo $this->comment->get('item_type'); ?>" />
					<input type="hidden" name="comment[item_id]" value="<?php echo $this->comment->get('item_id'); ?>" />
					<input type="hidden" name="comment[parent]" value="<?php echo $this->comment->get('parent'); ?>" />
					<input type="hidden" name="comment[created]" value="<?php echo $this->comment->get('created'); ?>" />
					<input type="hidden" name="comment[created_by]" value="<?php echo $this->comment->get('created_by'); ?>" />

					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="id" value="<?php echo $this->resource->id; ?>" />
					<input type="hidden" name="active" value="reviews" />
					<input type="hidden" name="action" value="savereply" />

					<?php echo JHTML::_('form.token'); ?>

					<label for="comment_<?php echo $this->comment->get('id'); ?>_content">
						<span class="label-text"><?php echo JText::_('PLG_RESOURCES_REVIEWS_ENTER_COMMENTS'); ?></span>
						<?php
						echo JFactory::getEditor()->display('comment[content]', $this->comment->content('raw'), '', '', 35, 4, false, 'comment_' . $this->comment->get('id') . '_content', null, null, array('class' => 'minimal no-footer'));
						?>
					</label>

					<label id="comment-anonymous-label" for="comment-anonymous">
						<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" <?php if ($this->comment->get('anonymous')) { echo ' checked="checked"'; } ?> />
						<?php echo JText::_('PLG_RESOURCES_REVIEWS_POST_COMMENT_ANONYMOUSLY'); ?>
					</label>

					<p class="submit">
						<input type="submit" value="<?php echo JText::_('PLG_RESOURCES_REVIEWS_SUBMIT'); ?>" />
					</p>
				</fieldset>
			</form>
	<?php } else { ?>
			<div class="comment-body">
				<?php echo $comment; ?>
			</div>

			<p class="comment-options">
		<?php if (!$this->comment->isReported() && !stristr($comment, 'class="warning"')) { ?>
			<?php if ($juser->get('id') == $this->comment->get('created_by')) { ?>
					<a class="icon-delete delete" data-txt-confirm="<?php echo JText::_('PLG_RESOURCES_REVIEWS_CONFIRM_DELETE'); ?>" href="<?php echo JRoute::_($this->base . '&action=delete' . ($this->comment->get('resource_id') ? 'review' : 'reply') . '&comment=' . $this->comment->get('id')); ?>"><!--
						--><?php echo JText::_('PLG_RESOURCES_REVIEWS_DELETE'); ?><!--
					--></a>
					<a class="icon-edit edit" href="<?php echo JRoute::_($this->base . '&action=edit' . ($this->comment->get('resource_id') ? 'review' : '') . '&comment=' . $this->comment->get('id') . ($this->comment->get('resource_id') ? '#commentform' : '')); ?>"><!--
						--><?php echo JText::_('PLG_RESOURCES_REVIEWS_EDIT'); ?><!--
					--></a>
			<?php } ?>
			<?php if (!$this->comment->get('reports')) { ?>
				<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
					<?php if (JRequest::getInt('reply', 0) == $this->comment->get('id')) { ?>
					<a class="icon-reply reply active" data-txt-active="<?php echo JText::_('PLG_RESOURCES_REVIEWS_CANCEL'); ?>" data-txt-inactive="<?php echo JText::_('PLG_RESOURCES_REVIEWS_REPLY'); ?>" href="<?php echo JRoute::_($this->comment->link()); ?>" data-rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
					--><?php echo JText::_('PLG_RESOURCES_REVIEWS_CANCEL'); ?><!--
				--></a>
					<?php } else { ?>
					<a class="icon-reply reply" data-txt-active="<?php echo JText::_('PLG_RESOURCES_REVIEWS_CANCEL'); ?>" data-txt-inactive="<?php echo JText::_('PLG_RESOURCES_REVIEWS_REPLY'); ?>" href="<?php echo JRoute::_($this->comment->link('reply')); ?>" data-rel="comment-form<?php echo $this->comment->get('id'); ?>"><!--
					--><?php echo JText::_('PLG_RESOURCES_REVIEWS_REPLY'); ?><!--
				--></a>
					<?php } ?>
				<?php } ?>
					<a class="icon-abuse abuse" data-txt-flagged="<?php echo JText::_('PLG_RESOURCES_REVIEWS_NOTICE_POSTING_REPORTED'); ?>" href="<?php echo JRoute::_($this->comment->link('report')); ?>"><!--
					--><?php echo JText::_('PLG_RESOURCES_REVIEWS_REPORT_ABUSE'); ?><!--
				--></a>
			<?php } ?>
		<?php } ?>
			</p>

		<?php if ($this->depth < $this->config->get('comments_depth', 3)) { ?>
			<div class="addcomment comment-add<?php if (JRequest::getInt('reply', 0) != $this->comment->get('id')) { echo ' hide'; } ?>" id="comment-form<?php echo $this->comment->get('id'); ?>">
				<?php if ($juser->get('guest')) { ?>
				<p class="warning">
					<?php echo JText::sprintf('PLG_RESOURCES_REVIEWS_PLEASE_LOGIN_TO_ANSWER', '<a href="' . JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($this->base, false, true))) . '">' . JText::_('PLG_RESOURCES_REVIEWS_LOGIN') . '</a>'); ?>
				</p>
				<?php } else { ?>
				<form id="cform<?php echo $this->comment->get('id'); ?>" action="<?php echo JRoute::_($this->base); ?>" method="post" enctype="multipart/form-data">
					<fieldset>
						<legend><span><?php echo JText::sprintf('PLG_RESOURCES_REVIEWS_REPLYING_TO', (!$this->comment->get('anonymous') ? $name : JText::_('PLG_RESOURCES_REVIEWS_ANONYMOUS'))); ?></span></legend>

						<input type="hidden" name="comment[id]" value="0" />
						<input type="hidden" name="comment[item_type]" value="<?php echo $this->comment->get('item_type'); ?>" />
						<input type="hidden" name="comment[item_id]" value="<?php echo $this->comment->get('item_id'); ?>" />
						<input type="hidden" name="comment[parent]" value="<?php echo ($this->comment->get('resource_id') ? 0 : $this->comment->get('id')); ?>" />
						<input type="hidden" name="comment[created]" value="" />
						<input type="hidden" name="comment[created_by]" value="<?php echo $juser->get('id'); ?>" />

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="id" value="<?php echo $this->resource->id; ?>" />
						<input type="hidden" name="active" value="reviews" />
						<input type="hidden" name="action" value="savereply" />

						<?php echo JHTML::_('form.token'); ?>

						<label for="comment_<?php echo $this->comment->get('id'); ?>_content">
							<span class="label-text"><?php echo JText::_('PLG_RESOURCES_REVIEWS_ENTER_COMMENTS'); ?></span>
							<?php
							echo JFactory::getEditor()->display('comment[content]', '', '', '', 35, 4, false, 'comment_' . $this->comment->get('id') . '_content', null, null, array('class' => 'minimal no-footer'));
							?>
						</label>

						<label id="comment-anonymous-label" for="comment-anonymous">
							<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
							<?php echo JText::_('PLG_RESOURCES_REVIEWS_POST_COMMENT_ANONYMOUSLY'); ?>
						</label>

						<p class="submit">
							<input type="submit" value="<?php echo JText::_('PLG_RESOURCES_REVIEWS_SUBMIT'); ?>" />
						</p>
					</fieldset>
				</form>
				<?php } ?>
			</div><!-- / .addcomment -->
		<?php } ?>
	<?php } ?>
		</div><!-- / .comment-content -->
		<?php
		if ($this->depth < $this->config->get('comments_depth', 3))
		{
			$this->view('_list')
			     ->set('parent', $this->comment->get('id'))
			     ->set('resource', $this->resource)
			     ->set('option', $this->option)
			     ->set('comments', $this->comment->replies('list'))
			     ->set('config', $this->config)
			     ->set('depth', $this->depth)
			     ->set('cls', $cls)
			     ->set('base', $this->base)
			     ->display();
		}
		?>
	</li>