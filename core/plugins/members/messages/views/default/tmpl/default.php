<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
$no_html = Request::getInt("no_html", 0);
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
<?php endif;
