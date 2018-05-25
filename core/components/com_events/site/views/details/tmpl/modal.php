<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<div id="event" class="modal">
<?php if ($this->row) { ?>
	<h2 class="entry-title">
		<?php echo $this->escape(stripslashes($this->row->title)); ?>
		<?php if ($this->authorized || $this->row->created_by == User::get('id')) { ?>
			<a class="edit" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=edit&id=' . $this->row->id); ?>" title="<?php echo Lang::txt('EVENTS_EDIT'); ?>">
				<?php echo strtolower(Lang::txt('EVENTS_EDIT')); ?>
			</a>
			<a class="delete" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=delete&id=' . $this->row->id); ?>" title="<?php echo Lang::txt('EVENTS_DELETE'); ?>">
				<?php echo strtolower(Lang::txt('EVENTS_DELETE')); ?>
			</a>
		<?php } ?>
	</h2>

	<?php if ($this->pages || ($this->row->registerby && $this->row->registerby != '0000-00-00 00:00:00')) { ?>
		<div id="sub-sub-menu">
			<ul>
				<li<?php if ($this->page->alias == '') { echo ' class="active"'; } ?>>
					<a class="tab" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=details&id=' . $this->row->id . '&no_html=1'); ?>">
						<span><?php echo Lang::txt('EVENTS_OVERVIEW'); ?></span>
					</a>
				</li>
			<?php
			if ($this->pages) {
				foreach ($this->pages as $p)
				{
			?>
				<li<?php if ($this->page->alias == $p->alias) { echo ' class="active"'; } ?>>
					<a class="tab" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=details&id=' . $this->row->id . '&no_html=1&page=' . $p->alias); ?>">
						<span><?php echo trim(stripslashes($p->title)); ?></span>
					</a>
				</li>
			<?php
				}
			}
			?>
			<?php if ($this->row->registerby && $this->row->registerby != '0000-00-00 00:00:00') { ?>
				<li<?php if ($this->page->alias == 'register') { echo ' class="active"'; } ?>>
					<a class="tab" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=details&id=' . $this->row->id . '&no_html=1&page=register'); ?>">
						<span><?php echo Lang::txt('EVENTS_REGISTER'); ?></span>
					</a>
				</li>
			<?php } ?>
			</ul>
			<div class="clear"></div>
		</div>
	<?php } ?>

	<div class="entry-details">
	<?php if ($this->page->alias != '') { ?>
		<?php echo (trim($this->page->pagetext)) ? stripslashes($this->page->pagetext) : '<p class="warning">' . Lang::txt('EVENTS_NO_INFO_AVAILABLE') . '</p>'; ?>
	<?php } else { ?>

		<div class="col span6">
			<div class="container">
				<h3><?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_DESCRIPTION'); ?></h3>
				<p class="entry-description">
					<?php echo stripslashes($this->row->content); ?>
				</p>
				<?php
				if ($this->fields) {
					foreach ($this->fields as $field)
					{
						if (end($field) != NULL) {
							if (end($field) == '1') {
				?>
					<h3><?php echo $this->escape(stripslashes($field[1])); ?></h3>
					p><?php echo Lang::txt('YES'); ?></p>
				<?php } else { ?>
					<h3><?php echo $this->escape(stripslashes($field[1])); ?></h3>
					<p><?php echo end($field); ?></p>
				<?php
							}
						}
					}
				}
				?>
			</div>
			<?php if ($this->tags) { ?>
				<div class="container">
					<h3><?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_TAGS'); ?></h3>
					<?php echo $this->tags; ?>
				</div>
			<?php } ?>
		</div>

		<div class="col span6 omega">
			<div class="container">
				<h3><?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_CATEGORY'); ?></h3>
				<p class="entry-category">
					<?php echo $this->escape(stripslashes($this->categories[$this->row->catid])); ?>
				</p>
			</div>

			<div class="container">
				<h3><?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_WHEN'); ?></h3>
				<p class="entry-datetime">
				<?php
				$ts = explode(':', $this->row->start_time);
				//$ts[0] = intval($ts[0]);
				if (intval($ts[0]) > 12) {
					$ts[0] = ($ts[0] - 12);
					$ts[0] = (substr($ts[0], 0, 1) == '0') ? substr($ts[0], 1) : $ts[0];
					$this->row->start_time = implode(':',$ts);
					$this->row->start_time .= ' <abbr title="Post Meridian">am</abbr>';
				} else {
					$this->row->start_time = (substr($this->row->start_time, 0, 1) == '0') ? substr($this->row->start_time, 1) : $this->row->start_time;
					$this->row->start_time .= (intval($ts[0]) == 12) ? ' <small>'.Lang::txt('EVENTS_NOON').'</small>' : ' <abbr title="Ante Meridian">am</abbr>';
				}
				$te = explode(':', $this->row->stop_time);
				//$te[0] = intval($te[0]);
				if (intval($te[0]) > 12) {
					$te[0] = ($te[0] - 12);
					$te[0] = (substr($te[0], 0, 1) == '0') ? substr($te[0], 1) : $te[0];
					$this->row->stop_time = implode(':', $te);
					$this->row->stop_time .= ' <abbr title="Post Meridian">pm</abbr>';
				} else {
					$this->row->stop_time = (substr($this->row->stop_time, 0, 1) == '0') ? substr($this->row->stop_time, 1) : $this->row->stop_time;
					$this->row->stop_time .= (intval($te[0]) == 12) ? ' <small>'.Lang::txt('EVENTS_NOON').'</small>' : ' <abbr title="Ante Meridian">pm</abbr>';
				}
				if ($this->row->start_date == $this->row->stop_date) {
					echo $this->row->start_date .',<br />'.$this->row->start_time.'&nbsp;-&nbsp;'.$this->row->stop_time.'<br />';
				} else {
					echo Lang::txt('EVENTS_CAL_LANG_FROM').' '.$this->row->start_date.'&nbsp;-&nbsp;'.$this->row->start_time.'<br />'.
						Lang::txt('EVENTS_CAL_LANG_TO').' '.$this->row->stop_date.'&nbsp;-&nbsp;'.$this->row->stop_time.'<br />';
				}
				?>
				</p>
			</div>

			<?php if (trim($this->row->adresse_info)) { ?>
				<div class="container">
					<h3><?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_ADRESSE'); ?></h3>
					<p class="entry-location">
						<?php echo $this->escape(stripslashes($this->row->adresse_info)); ?>
					</p>
				</div>
			<?php } ?>

			<?php if (trim($this->row->extra_info)) { ?>
				<div class="container">
					<h3><?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_EXTRA'); ?></h3>
					<p class="entry-link">
						<a href="<?php echo stripslashes($this->row->extra_info); ?>"><?php echo $this->escape(stripslashes($this->row->extra_info)); ?></a>
					</p>
				</div>
			<?php } ?>

			<?php if (trim($this->row->contact_info)) { ?>
				<div class="container">
					<h3><?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_CONTACT'); ?></h3>
					<p class="entry-contact">
						<?php echo $this->escape(stripslashes($this->row->contact_info)); ?>
					</p>
				</div>
			<?php } ?>

			<?php if ($this->config->getCfg('byview') == 'YES') {
				$user = User::getInstance($this->row->created_by);

				if (is_object($user)) {
					$name = $user->get('name');
				} else {
					$name = Lang::txt('EVENTS_CAL_LANG_UNKNOWN');
				}
				?>
				<div class="container">
					<h3><?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_AUTHOR_ALIAS'); ?></h3>
					<p class="entry-author">
						<?php echo $this->escape(stripslashes($name)); ?>
					</p>
				</div>
			<?php } ?>
		</div>
	<?php } ?>
	</div><!-- / .entry-details -->
<?php } else { ?>
	<p class="warning"><?php echo Lang::txt('EVENTS_CAL_LANG_REP_NOEVENTSELECTED'); ?></p>
<?php } ?>
</div><!-- / .modal -->
