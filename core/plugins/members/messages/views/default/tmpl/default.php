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

//section links
$sections = array(
	array(
		'name' => 'inbox',
		'title' => Lang::txt('PLG_MEMBERS_MESSAGES_INBOX'),
		'link' => Route::url($this->member->link() . '&active=messages&task=inbox&limit=' . $this->filters['limit'] . '&limitstart=0')
	),
	array(
		'name' => 'sent',
		'title' => Lang::txt('PLG_MEMBERS_MESSAGES_SENT'),
		'link' => Route::url($this->member->link() . '&active=messages&task=sent&limit=' . $this->filters['limit'] . '&limitstart=0')
	),
	array(
		'name' => 'archive',
		'title' => Lang::txt('PLG_MEMBERS_MESSAGES_ARCHIVE'),
		'link' => Route::url($this->member->link() . '&active=messages&task=archive&limit=' . $this->filters['limit'] . '&limitstart=0')
	),
	array(
		'name' => 'trash',
		'title' => Lang::txt('PLG_MEMBERS_MESSAGES_TRASH'),
		'link' => Route::url($this->member->link() . '&active=messages&task=trash&limit=' . $this->filters['limit'] . '&limitstart=0')
	),
	array(
		'name' => 'new',
		'title' => Lang::txt('PLG_MEMBERS_MESSAGES_COMPOSE'),
		'link' => Route::url($this->member->link() . '&active=messages&task=new&limit=' . $this->filters['limit'] . '&limitstart=0')
	)
);

//option links
$options = array(
	array(
		'name'  => 'settings',
		'title' => Lang::txt('PLG_MEMBERS_MESSAGES_SETTINGS'),
		'link'  => Route::url($this->member->link() . '&active=messages&task=settings')
	)
);

//no html?
$no_html = Request::getVar("no_html", 0);
?>
<?php if (!$no_html) : ?>
<h3 class="section-header">
	<?php echo Lang::txt('PLG_MEMBERS_MESSAGES'); ?>
</h3>

<div class="section">
	<ul id="message-toolbar">
		<?php foreach ($sections as $s) : ?>
			<?php $sel = ($this->task == $s['name']) ? 'active': ''; ?>
			<li><a class="<?php echo $s['name'] . ' ' . $sel; ?>" title="<?php echo $s['title']; ?>" href="<?php echo $s['link']; ?>"><?php echo $s['title']; ?></a></li>
		<?php endforeach; ?>
	</ul>
	<ul id="message-options">
		<?php foreach ($options as $o) : ?>
			<?php $sel = ($this->task == $o['name']) ? 'active': ''; ?>
			<li><a class="<?php echo $o['name'] . ' ' . $sel; ?>" title="<?php echo $o['title']; ?>" href="<?php echo $o['link']; ?>"><?php echo $o['title']; ?></a></li>
		<?php endforeach; ?>
	</ul>
	<br class="clear" />

	<div id="messages-container">
		<?php
			foreach ($this->notifications as $n)
			{
				echo '<p class="' . $n['type'] . '">' . $n['message'] . '</p>';
			}
		?>
<?php endif; ?>

		<?php echo $this->body; ?>

<?php if (!$no_html) : ?>
	</div>
</div>
<?php endif; ?>