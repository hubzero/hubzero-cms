<?php
defined('_JEXEC') or die( 'Restricted access' );

$ct = count($this->sections);

$base = $this->offering->link() . '&active=discussions&unit=manage';
?>
<section class="main section">
	<div class="subject">
		<?php foreach ($this->notifications as $notification) { ?>
			<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
		<?php } ?>

		<?php if ($ct > 0) { ?>

			<?php
			$ct--;
			foreach ($this->sections as $i => $section)
			{
				if ($section->id == 0 && !$section->categories[0]->posts)
				{
					continue;
				}
			?>
				<div class="container">
					<span class="ordering-controls">
					<?php if ($i != 0) { ?>
						<a class="order-up reorder" href="<?php echo Route::url($base . '&b=' . $section->alias . '&c=orderup'); ?>" title="<?php echo Lang::txt('Move up'); ?>"><?php echo Lang::txt('Move up'); ?></a>
					<?php } else { ?>
						<span class="order-up reorder"><?php echo Lang::txt('Move up'); ?></span>
					<?php } ?>

					<?php if ($i < $ct) { ?>
						<a class="order-down reorder" href="<?php echo Route::url($base . '&b=' . $section->alias . '&c=orderdown'); ?>" title="<?php echo Lang::txt('Move down'); ?>"><?php echo Lang::txt('Move down'); ?></a>
					<?php } else { ?>
						<span class="order-down reorder"><?php echo Lang::txt('Move down'); ?></span>
					<?php } ?>
					</span>

					<?php if ($this->config->get('access-edit-section') && $this->edit == $section->alias && $section->id) { ?>
					<form action="<?php echo Route::url($base); ?>" method="post">
					<?php } ?>
					<table class="entries categories">
						<caption>
						<?php if ($this->config->get('access-edit-section') && $this->edit == $section->alias && $section->id) { ?>
								<!-- <a name="s<?php echo $section->id; ?>"></a> [!] This seems to cause some serious display issues -->
								<input type="text" name="fields[title]" value="<?php echo $this->escape(stripslashes($section->title)); ?>" />
								<input type="submit" value="<?php echo Lang::txt('Save'); ?>" />
								<input type="hidden" name="fields[id]" value="<?php echo $section->id; ?>" />
								<input type="hidden" name="fields[scope]" value="course" />
								<input type="hidden" name="fields[scope_id]" value="<?php echo $section->scope_id; ?>" />
								<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
								<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
								<input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
								<input type="hidden" name="action" value="savesection" />
								<input type="hidden" name="unit" value="manage" />
								<input type="hidden" name="active" value="discussions" />
						<?php } else { ?>
							<?php echo $this->escape(stripslashes($section->title)); ?>
						<?php } ?>
					<?php if (($this->config->get('access-edit-section') || $this->config->get('access-delete-section')) && $section->id) { ?>
						<?php if ($this->config->get('access-delete-section')) { ?>
							<a class="delete" href="<?php echo Route::url($base . '&b=' . $section->alias . '&c=delete'); ?>" title="<?php echo Lang::txt('Delete'); ?>">
								<span><?php echo Lang::txt('Delete'); ?></span>
							</a>
						<?php } ?>
						<?php if ($this->config->get('access-edit-section') && $this->edit != $section->alias && $section->id) { ?>
							<a class="edit" href="<?php echo Route::url($base . '&b=' . $section->alias . '&c=edit#s' . $section->id); ?>" title="<?php echo Lang::txt('Edit'); ?>">
								<span><?php echo Lang::txt('Edit'); ?></span>
							</a>
						<?php } ?>
					<?php } ?>
						</caption>
					<?php if ($this->config->get('access-create-category')) { ?>
						<tfoot>
							<tr>
								<td<?php if ($section->categories) { echo ' colspan="5"'; } ?>>
									<a class="icon-add add btn" href="<?php echo Route::url($base . '&b=' . $section->alias . '&c=new'); ?>">
										<span><?php echo Lang::txt('Add Category'); ?></span>
									</a>
								</td>
							</tr>
						</tfoot>
					<?php } ?>
						<tbody>
				<?php if ($section->categories) { ?>
					<?php foreach ($section->categories as $row) { ?>
							<tr<?php if ($row->closed) { echo ' class="closed"'; } ?>>
								<th scope="row">
									<span class="entry-id"><?php echo $this->escape($row->id); ?></span>
								</th>
								<td>
									<span class="entry-title" data-href="<?php echo Route::url($base . '&b=' . $row->alias); ?>">
										<span><?php echo $this->escape(stripslashes($row->title)); ?></span>
									</span>
									<span class="entry-details">
										<span class="entry-description">
											<?php echo str_replace(array('<p>', '</p>'), '', stripslashes($row->description)); ?>
										</span>
									</span>
								</td>
								<td>
									<span><?php echo $row->threads; ?></span>
									<span class="entry-details">
										<?php echo Lang::txt('Discussions'); ?>
									</span>
								</td>
								<td>
									<span><?php echo $row->posts; ?></span>
									<span class="entry-details">
										<?php echo Lang::txt('Posts'); ?>
									</span>
								</td>
							<?php if ($this->config->get('access-edit-category') || $this->config->get('access-delete-category')) { ?>
								<td class="entry-options">
									<?php if (($row->created_by == User::get('id') || $this->config->get('access-edit-category')) && $section->id) { ?>
										<a class="edit" href="<?php echo Route::url($base . '&b=' . $section->alias . '&c=' . $row->alias . '/edit'); ?>" title="<?php echo Lang::txt('Edit'); ?>">
											<span><?php echo Lang::txt('Edit'); ?></span>
										</a>
									<?php } ?>
									<?php if ($this->config->get('access-delete-category') && $section->id) { ?>
										<a class="delete tooltips" title="<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_DELETE_CATEGORY'); ?>" href="<?php echo Route::url($base . '&b=' . $section->alias . '&c=' . $row->alias . '/delete'); ?>" title="<?php echo Lang::txt('Delete'); ?>">
											<span><?php echo Lang::txt('Delete'); ?></span>
										</a>
									<?php } ?>
								</td>
						<?php } ?>
							</tr>
					<?php } ?>
				<?php } else { ?>
							<tr>
								<td><?php echo Lang::txt('There are no categories.'); ?></td>
							</tr>
				<?php } ?>
						</tbody>
					</table>
					<?php if ($this->config->get('access-edit-section') && $this->edit == $section->alias && $section->id) { ?>
					</form>
					<?php } ?>
				</div><!-- /.container -->
			<?php } // foreach ?>

		<?php } else { ?>

			<p><?php echo Lang::txt('This forum is currently empty.'); ?></p>

		<?php } ?>

		<?php if ($this->config->get('access-create-section')) { ?>
			<div class="container">
				<form method="post" action="<?php echo Route::url($base); ?>">
					<fieldset>
						<table class="entries categories">
							<caption>
								<label for="field-title">
									<?php echo Lang::txt('New Section'); ?>
									<input type="text" name="fields[title]" id="field-title" value="" />
								</label>
								<input type="submit" value="<?php echo Lang::txt('Create'); ?>" />
							</caption>
							<tbody>
								<tr>
									<td><?php echo Lang::txt('Use sections to group related categories.'); ?></td>
								</tr>
							</tbody>
						</table>

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
						<input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
						<input type="hidden" name="fields[scope]" value="course" />
						<input type="hidden" name="fields[scope_id]" value="<?php echo $this->course->offering()->get('id'); ?>" />
						<input type="hidden" name="active" value="discussions" />
						<input type="hidden" name="unit" value="manage" />
						<input type="hidden" name="action" value="savesection" />
					</fieldset>
				</form>
			</div><!-- /.container -->
		<?php } ?>
	</div><!-- /.subject -->
	<aside class="aside">
		<div class="container">
			<h3><?php echo Lang::txt('Statistics'); ?></h3>
			<table>
				<tbody>
					<tr>
						<th><?php echo Lang::txt('Categories'); ?></th>
						<td><span class="item-count"><?php echo $this->stats->categories; ?></span></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('Discussions'); ?></th>
						<td><span class="item-count"><?php echo $this->stats->threads; ?></span></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('Posts'); ?></th>
						<td><span class="item-count"><?php echo $this->stats->posts; ?></span></td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="container">
			<h3><?php echo Lang::txt('Last Post'); ?></h3>
			<p>
			<?php
			if (is_object($this->lastpost))
			{
				$lname = Lang::txt('Anonymous');
				$lastposter = User::getInstance($this->lastpost->created_by);
				if (is_object($lastposter))
				{
					$lname = '<a href="' . Route::url('index.php?option=com_members&id=' . $lastposter->get('id')) . '">' . $this->escape(stripslashes($lastposter->get('name'))) . '</a>';
				}
				foreach ($this->sections as $section)
				{
					if ($section->categories)
					{
						foreach ($section->categories as $row)
						{
							if ($row->id == $this->lastpost->category_id)
							{
								$cat = $row->alias;
								$sec = $section->alias;
								break;
							}
						}
					}
				}
				?>
				<span class="entry-date" data-href="<?php echo Route::url($base . '&b=' . $sec . '&c=' . $cat . '/' . ($this->lastpost->parent ? $this->lastpost->parent : $this->lastpost->id)); ?>">
					<span class="entry-date-at">@</span>
					<span class="time"><time datetime="<?php echo $this->lastpost->created; ?>"><?php echo Date::of($this->lastpost->created)->toLocal(Lang::txt('TIME_FORMAt_HZ1')); ?></time></span>
					<span class="entry-date-on"><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_ON'); ?></span>
					<span class="date"><time datetime="<?php echo $this->lastpost->created; ?>"><?php echo Date::of($this->lastpost->created)->toLocal(Lang::txt('DATE_FORMAt_HZ1')); ?></time></span>
				</span>
				<span class="entry-author">
					<?php echo Lang::txt('by'); ?>
					<?php echo $lname; ?>
				</span>
			<?php } else { ?>
				<?php echo Lang::txt('none'); ?>
			<?php } ?>
			</p>
		</div>
	</aside><!-- / .aside -->
</section><!-- /.main -->