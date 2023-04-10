<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('introduction.css', 'system')
     ->css();
?>

<header id="content-header">
	<h2><?php echo $this->forgeName; ?></h2>
</header>

<section id="introduction" class="section">
	<div class="grid">
		<div class="col span9">
			<h3><?php echo Lang::txt('COM_TOOLS_TOOL_DEVELOPMENT'); ?></h3>
			<p>
				<?php echo Lang::txt('COM_TOOLS_TOOL_DEVELOPMENT_INTRO', $this->escape($this->forgeName), $this->escape(Config::get('sitename'))); ?>
			</p>
		</div>
		<div class="col span3 omega">
			<?php if (User::isGuest()) { ?>
			<h3><?php echo Lang::txt('COM_TOOLS_HELP'); ?></h3>
			<ul>
				<li><a href="<?php echo Route::url('index.php?option=com_members&controller=register'); ?>"><?php echo Lang::txt('COM_TOOLS_SIGN_UP_FREE'); ?></a></li>
			</ul>
			<?php } ?>
		</div>
	</div>
</section><!-- / #introduction.section -->

<section class="section">
	<div class="grid">
		<div class="col span3">
			<h2><?php echo Lang::txt('COM_TOOLS_AVAILABLE'); ?></h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<table>
				<thead>
					<tr>
						<th scope="col"><?php echo Lang::txt('COM_TOOLS_TITLE'); ?></th>
						<th scope="col"><?php echo Lang::txt('COM_TOOLS_ALIAS'); ?></th>
						<th scope="col"><?php echo Lang::txt('COM_TOOLS_STATUS'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				$cls = 'even';
				if (count($this->apps) > 0)
				{
					foreach ($this->apps as $project)
					{
						//if ($project->state == 1 || $project->state == 3) {
						if ($project->tool_state != 8)
						{
							$status = ($project->codeaccess == '@OPEN' ? Lang::txt('COM_TOOLS_OPEN_SOURCE') : Lang::txt('COM_TOOLS_CLOSED_SOURCE'));
							?>
					<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even';
echo $cls; ?>">
						<td><a href="<?php echo Route::url('index.php?option=' . $this->option . '&app=' . $project->toolname . '&task=wiki'); ?>"><?php echo \Hubzero\Utility\Str::truncate(stripslashes($project->title), 50); ?></a></td>
						<td><a href="<?php echo Route::url('index.php?option=' . $this->option . '&app=' . $project->toolname . '&task=wiki'); ?>"><?php echo $this->escape($project->toolname); ?></a></td>
						<td><span class="<?php echo $status; ?>-code"><?php echo $status; ?></span></td>
					</tr>
							<?php
						}
					}
				} else {
				?>
					<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even';
echo $cls; ?>">
						<td colspan="3"><?php echo Lang::txt('COM_TOOLS_NONE'); ?></td>
					</tr>
				<?php
				}
				?>
				</tbody>
			</table>
		</div><!-- / .col span9 omega -->
	</div>
</section><!-- / .section -->
