<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();
if ($this->params->get('allowClose', 1))
{
	$this->js();
}

$rows = $this->offering->announcements(array(
	'limit'     => $this->params->get('display_limit', 1),
	'published' => true
));

if ($rows->total() > 0)
{
	$announcements = array();

	foreach ($rows as $row)
	{
		if ($this->params->get('allowClose', 1))
		{
			if (!($hide = Request::getWord('ancmnt' . $row->get('id'), '', 'cookie')))
			{
				$announcements[] = $row;
			}
		}
	}

	if (count($announcements))
	{
		?>
		<div class="announcements">
			<?php foreach ($announcements as $row) { ?>
				<div class="announcement<?php if ($row->get('priority')) { echo ' high'; } ?>">
					<?php echo $row->content('parsed'); ?>
					<dl class="entry-meta">
						<dt class="entry-id"><?php echo $row->get('id'); ?></dt>
						<dd class="time">
							<time datetime="<?php echo $row->published(); ?>">
								<?php echo $row->published('time'); ?>
							</time>
						</dd>
						<dd class="date">
							<time datetime="<?php echo $row->published(); ?>">
								<?php echo $row->published('date'); ?>
							</time>
						</dd>
					</dl>
					<?php
						$page = Request::getString('REQUEST_URI', '', 'server');
						if ($page && $this->params->get('allowClose', 1))
						{
							$page .= (strstr($page, '?')) ? '&' : '?';
							$page .= 'ancmnt' . $row->get('id') . '=closed';
							?>
							<a class="close" href="<?php echo $page; ?>" data-id="<?php echo $row->get('id'); ?>" data-duration="<?php echo $this->params->get('closeDuration', 30); ?>" title="<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_CLOSE_THIS'); ?>">
								<span><?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_CLOSE'); ?></span>
							</a>
							<?php
						}
					?>
				</div>
			<?php } ?>
		</div>
		<?php
	}
}
