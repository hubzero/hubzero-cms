<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('tools.css');
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_TOOLS_QUOTAEXCEEDED'); ?></h2>
</header><!-- / #content-header -->

<section class="main section<?php if (!$this->config->get('access-manage-session') && $this->active == 'all') { echo ' hide';
} else { echo ''; }?>" id="mysessions-section">
	<p class="warning"><?php echo Lang::txt('COM_TOOLS_ERROR_QUOTAEXCEEDED'); ?></p>
	<table class="sessions">
		<thead>
			<tr>
				<th><?php echo Lang::txt('COM_TOOLS_MYSESSIONS_COL_SESSION'); ?></th>
				<th><?php echo Lang::txt('COM_TOOLS_MYSESSIONS_COL_STARTED'); ?></th>
				<th><?php echo Lang::txt('COM_TOOLS_MYSESSIONS_COL_LAST_ACCESSED'); ?></th>
				<th><?php echo Lang::txt('COM_TOOLS_MYSESSIONS_COL_OPTION'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		if ($this->sessions) {
			$cls = 'even';
			foreach ($this->sessions as $session)
			{
				$cls = ($cls == 'odd') ? 'even' : 'odd';
		?>
			<tr class="<?php echo $cls; ?>">
				<td><a href="<?php echo Route::url('index.php?option='.$this->option.'&controller='.$this->controller.'&task=session&app='.$session->appname.'&sess='.$session->sessnum); ?>" title="<?php echo Lang::txt('COM_TOOLS_RESUME_TITLE'); ?>"><?php echo $session->sessname; ?></a></td>
				<td><?php echo $session->start; ?></td>
				<td><?php echo $session->accesstime; ?></td>
			<?php if (User::get('username') == $session->username) { ?>
				<td><a class="closetool" href="<?php echo Route::url('index.php?option='.$this->option.'&controller='.$this->controller.'&task=stop&app='.$session->appname.'&sess='.$session->sessnum); ?>" title="<?php echo Lang::txt('COM_TOOLS_TERMINATE_TITLE'); ?>"><?php echo Lang::txt('COM_TOOLS_TERMINATE'); ?></a></td>
			<?php } else { ?>
				<td><a class="disconnect" href="<?php echo Route::url('index.php?option='.$this->option.'&controller='.$this->controller.'&task=unshare&app='.$session->appname.'&sess='.$session->sessnum); ?>" title="<?php echo Lang::txt('COM_TOOLS_DISCONNECT_TITLE'); ?>"><?php echo Lang::txt('COM_TOOLS_DISCONNECT'); ?></a> <span class="owner"><?php echo Lang::txt('COM_TOOLS_MY_SESSIONS_OWNER').': '.$session->username; ?></span></td>
			<?php } ?>
			</tr>
		<?php
			}
		}
		?>
		</tbody>
	</table>
</section><!-- / .section -->
