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

// No direct access.
defined('_HZEXEC_') or die();

Html::behavior('core');

//is this a share session thats read-only
$readOnly = false;
foreach ($this->shares as $share)
{
	if (User::get('username') == $share->viewuser)
	{
		if (strtolower($share->readonly) == 'yes')
		{
			$readOnly = true;
		}
	}
}

include_once(PATH_CORE . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'preferences.php');

$database = App::get('db');
$preferences = new \Components\Tools\Tables\Preferences($database);
$preferences->loadByUser(User::get('id'));

$declared = Request::getWord('viewer');
if ($declared)
{
	if (Request::getInt('preferred', 0))
	{
		$preferences->set('user_id', User::get('id'));
		$preferences->param()->set('viewer', $declared);
		$preferences->store();
	}
}
else if ($declared = $preferences->param('viewer'))
{
	Request::setVar('viewer', $declared);
}

// We actually need to do this first so we know what viewer is the active one.
$output  = Event::trigger('tools.onToolSessionView', array($this->app, $this->output, $readOnly));
$plugins = Event::trigger('tools.onToolSessionIdentify');

$this->css('tools.css')
     ->js('sessions.js');
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_SESSION_NUMBER', $this->app->sess); ?></h2>
</header><!-- / #content-header -->

<section id="session" class="main section">
<?php
if (!$this->app->sess) {
	echo '<p class="error">' . implode('<br />', $this->output) . '</p>';
} else {
?>

	<?php if ($readOnly) : ?>
		<p class="warning readonly-warning">
			<?php echo Lang::txt('COM_TOOLS_WARNING_SESSION_READ_ONLY'); ?>
		</p>
	<?php endif; ?>

	<?php echo implode("\n", Event::trigger('tools.onToolSessionViewBefore', array($this->app, $this->output, $readOnly))); ?>

	<div id="app-wrap">
		<div id="app-header">
			<h2 id="session-title" class="session-title item:name id:<?php echo $this->app->sess; ?> <?php if (is_object($this->app->owns)) : ?>editable<?php endif; ?>" rel="<?php echo $this->app->sess; ?>"><?php echo $this->app->caption; ?></h2>
			<?php if ($this->app->sess) { ?>
				<ul class="app-toolbar" id="session-options">
					<li>
						<a id="app-btn-keep" class="keep" href="<?php echo Route::url('index.php?option=com_members&task=myaccount'); ?>">
							<span><?php echo Lang::txt('COM_TOOLS_KEEP_FOR_LATER'); ?></span>
						</a>
					</li>
					<?php if ($this->app->owns) { ?>
						<li>
							<a id="app-btn-close" class="terminate sessiontips" href="<?php echo Route::url('index.php?option='.$this->option.'&app='.$this->toolname.'&task=stop&sess='.$this->app->sess.'&return='.$this->rtrn); ?>" title="<?php echo Lang::txt('COM_TOOLS_TERMINATE_WARNING'); ?>">
								<span><?php echo Lang::txt('COM_TOOLS_TERMINATE'); ?></span>
							</a>
						</li>
					<?php } else { ?>
						<li>
							<a id="app-btn-close" class="terminate sessiontips" href="<?php echo Route::url('index.php?option=' . $this->option . '&app=' . $this->toolname . '&task=unshare&sess=' . $this->app->sess.'&return='.$this->rtrn); ?>" title="<?php echo Lang::txt('COM_TOOLS_TERMINATE_WARNING'); ?>">
								<span><?php echo Lang::txt('COM_TOOLS_STOP_SHARING'); ?></span>
							</a>
						</li>
					<?php } ?>
					<?php if (count($plugins) > 1) { ?>
						<li>
							<a id="app-btn-options" class="options" href="<?php echo Route::url('index.php?option='.$this->option.'&app='.$this->toolname.'&task=session&sess='.$this->app->sess); ?>">
								<span><?php echo Lang::txt('COM_TOOLS_SESSION_OPTIONS'); ?></span>
							</a>
						</li>
					<?php } ?>
				</ul>
			<?php } ?>
		</div><!-- #app-header -->
		<?php if (count($plugins) > 1) { ?>
			<div id="app-options">
				<form method="get" action="<?php echo Route::url('index.php?option='.$this->option.'&app='.$this->toolname.'&task=session&sess='.$this->app->sess); ?>">
					<fieldset>
						<?php
						$viewer = ($declared ? $declared : $this->output->rendered); //Session::get('tool_viewer'));
						?>
						<?php echo Lang::txt('COM_TOOLS_SESSION_USING_VIEWER', Lang::txt('PLG_TOOLS_' . $viewer . '_TITLE')); ?>

						<span class="input-wrap">
							<label for="app-viewer">
								<?php echo Lang::txt('COM_TOOLS_SESSION_VIEWER_CHANGE'); ?>
							</label>
							<select name="viewer" id="app-viewer">
								<?php foreach ($plugins as $plugin) {
									if ($viewer == $plugin->name) continue;
								?>
									<option value="<?php echo $plugin->name; ?>"<?php if ($viewer == $plugin->name) { echo ' selected="selected"'; } ?>><?php echo $plugin->title; ?></option>
								<?php } ?>
							</select>
						</span>

						<span class="input-wrap">
							<input type="checkbox" name="preferred" id="app-viewer-preferred" value="1" />
							<label for="app-viewer-preferred">
								<?php echo Lang::txt('Use for future sessions.'); ?>
							</label>
						</span>

						<span class="input-wrap">
							<input type="submit" value="<?php echo Lang::txt('COM_TOOLS_APPLY'); ?>" />
						</span>
						<input type="hidden" name="sess" value="<?php echo $this->app->sess; ?>" />
					</fieldset>
				</form>
			</div>
		<?php } ?>
		<div id="app-content" tabindex="1" class="<?php if ($readOnly) { echo 'view-only'; } ?>" style="width: <?php echo $this->output->width; ?>px; height: <?php echo $this->output->height; ?>px">
			<noscript>
				<p class="warning">
					<?php echo Lang::txt('COM_TOOLS_ERROR_NOSCRIPT'); ?>
				</p>
			</noscript>
			<input type="hidden" id="app-orig-width" name="apporigwidth" value="<?php echo $this->escape($this->output->width); ?>" />
			<input type="hidden" id="app-orig-height" name="apporigheight" value="<?php echo $this->escape($this->output->height); ?>" />
			<?php
			$output = implode("\n", $output);
			if (!trim($output))
			{
				$output = '<p class="error">' . Lang::txt('COM_TOOLS_ERROR_NOVIEWER') . '</p>';
			}
			echo $output;
			?>
		</div><!-- / #app-content -->
		<div id="app-footer">
			<?php
			if ($this->config->get('show_storage'))
			{
				$this->view('diskusage', 'storage')
				     ->set('option', $this->option)
				     ->set('amt', $this->app->percent)
				     ->set('du', NULL)
				     ->set('percent', 0)
				     ->set('msgs', 0)
				     ->set('ajax', 0)
				     ->set('writelink', 1)
				     ->set('total', $this->total)
				     ->display();

				if ($this->app->percent >= 100 && isset($this->app->remaining))
				{
					$this->view('warning', 'storage')
					     ->set('sec', $this->app->remaining)
					     ->set('padHours', false)
					     ->set('option', $this->option)
					     ->display();
				}
			}
			?>
		</div><!-- #app-footer -->
		<?php if ($this->zone->config('zones') && $this->zone->exists()) { ?>
			<div id="app-zone">
				<div class="grid">
					<div class="col span6">
						<p class="zone-identity">
							<?php if ($logo = $this->zone->logo()) { ?>
								<img src="<?php echo $logo; ?>" alt="" />
							<?php } ?>
						</p>
						<p>
							<?php echo $this->zone->get('description', Lang::txt('COM_TOOLS_POWERED_BY_MIRROR', $this->zone->get('title', $this->zone->get('zone')))); ?>
						</p>
					</div><!-- / .col span6 -->
					<div class="col span6 omega">
						<form name="share" id="app-zone" method="post" action="<?php echo Route::url('index.php?option='.$this->option.'&app='.$this->toolname.'&task=reinvoke&sess='.$this->app->sess); ?>">
							<p><?php echo Lang::txt('COM_TOOLS_ZONE_WARNING_CHANGE'); ?></p>
							<p><label for="field-zone">
								<?php echo Lang::txt('COM_TOOLS_ZONE_RELAUNCH'); ?>
								<select name="zone" id="field-zone">
									<option value=""><?php echo Lang::txt('COM_TOOLS_SELECT'); ?></option>
									<?php
									foreach ($this->middleware->zones('list', array('state' => 'up', 'id' => $this->middleware->get('allowed'))) as $zone)
									{
										if ($zone->get('id') == $this->zone->get('id'))
										{
											continue;
										}
									?>
									<option value="<?php echo $zone->get('id'); ?>"><?php echo $this->escape($zone->get('title', $zone->get('zone'))); ?></option>
								<?php } ?>
								</select>
							</label>
							<input type="submit" value="Go" /></p>
						</form>
					</div><!-- / .col span6 omega -->
				</div><!-- .grid -->
			</div><!-- #app-zone -->
		<?php } ?>
	</div><!-- #app-wrap -->

	<?php echo implode("\n", Event::trigger('tools.onToolSessionViewAfter', array($this->app, $this->output, $readOnly))); ?>

	<?php
	// Are we on an iPad?
	$isiPad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

	if ($isiPad && $this->config->get('launch_ipad', 0) && $this->config->get('launch_ipad_app'))
	{
		?>
		<p class="tablet-app"><a class="btn icon-tablet" href="<?php echo $this->config->get('launch_ipad_app'); ?>://tools/session/<?php echo $this->app->sess; ?>"><?php echo Lang::txt('Launch in iPad app'); ?></a></p>
		<?php
	}
	?>

	<div class="clear share-divider"></div>
<?php if ($this->config->get('shareable', 0)) { ?>
	<form name="share" id="app-share" method="post" action="<?php echo Route::url('index.php?option='.$this->option.'&app='.$this->toolname.'&task=session&sess='.$this->app->sess); ?>">
		<div class="grid">
		<?php if (is_object($this->app->owns)) : ?>
			<div class="col span8">
				<p class="share-member-photo" id="shareform">
					<img src="<?php echo User::picture(); ?>" alt="" />
				</p>
				<fieldset>
					<legend><?php echo Lang::txt('COM_TOOLS_SHARE_SESSION'); ?></legend>

					<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
					<input type="hidden" name="controller" value="<?php echo $this->escape($this->controller); ?>" />
					<input type="hidden" name="task" value="share" />
					<input type="hidden" name="sess" value="<?php echo $this->escape($this->app->sess); ?>" />
					<input type="hidden" name="app" value="<?php echo $this->escape($this->toolname); ?>" />
					<input type="hidden" name="return" value="<?php echo base64_encode(Route::url('index.php?option='.$this->option.'&app='.$this->toolname.'&task=session&sess='.$this->app->sess)); ?>" />

					<label for="field-username">
						<?php echo Lang::txt('COM_TOOLS_SHARE_SESSION_WITH'); ?>
						<?php
						$mc = Event::trigger('hubzero.onGetMultiEntry', array(array('members', 'username', 'acmembers')));
						if (count($mc) > 0) {
							echo '<span class="hint">'.Lang::txt('COM_TOOLS_SHARE_SESSION_HINT_AUTOCOMPLETE').'</span>'.$mc[0];
						} else { ?>
							<span class="hint"><?php echo Lang::txt('COM_TOOLS_SHARE_SESSION_HINT'); ?></span>
							<input type="text" name="username" id="field-username" value="" />
						<?php } ?>
					</label>
					<label for="group">
						<?php echo Lang::txt('COM_TOOLS_SHARE_SESSION_WITH_GROUP'); ?>
						<select name="group" id="group">
							<option value=""><?php echo Lang::txt('- Select Group &mdash;'); ?></option>
							<?php if (!empty($this->mygroups)) { foreach ($this->mygroups as $group) : ?>
								<option value="<?php echo $group->gidNumber; ?>"><?php echo $group->description; ?></option>
							<?php endforeach; } ?>
						</select>
					</label>
					<label for="field-readonly" id="readonly-label">
						<input class="option" type="checkbox" name="readonly" id="readonly" value="Yes" />
						<?php echo Lang::txt('COM_TOOLS_SHARE_SESSION_READ_ONLY'); ?>
					</label>

					<p class="submit">
						<input type="submit" value="<?php echo Lang::txt('COM_TOOLS_SHARE'); ?>" id="share-btn" />
					</p>

					<div class="sidenote">
						<p>
							<?php echo Lang::txt('COM_TOOLS_SHARE_SESSION_NOTES'); ?>
						</p>
					</div>
				</fieldset>
			</div><!-- / .col span8 -->
		<?php endif; ?>
			<div class="<?php if (is_object($this->app->owns)) : ?>col span4 omega<?php endif; ?>">
				<table class="entries">
					<thead>
						<tr>
							<th<?php if (count($this->shares) > 1) { ?> colspan="3"<?php } ?>>
								<?php echo Lang::txt('COM_TOOLS_SESSION_SHARED_WITH'); ?>
							</th>
						</tr>
					</thead>
					<tbody>
				<?php if (count($this->shares) <= 1) { ?>
						<tr>
							<td>
								<?php echo Lang::txt('COM_TOOLS_SHARE_SESSION_NONE'); ?>
							</td>
						</tr>
				<?php } else {
					foreach ($this->shares as $row)
					{
						if ($row->viewuser != User::get('username'))
						{
							$user = User::getInstance($row->viewuser);

							$id = ($user->get('id') < 0) ? 'n' . -$user->get('id') : $user->get('id');
						?>
						<tr>
							<th class="entry-img">
								<img width="40" height="40" src="<?php echo $user->picture(); ?>" alt="<?php echo $this->escape(stripslashes($user->get('name'))); ?>" />
							</th>
							<td>
								<a class="entry-title" href="<?php echo Route::url('index.php?option=com_members&id='.$id); ?>">
									<?php echo $this->escape(stripslashes($user->get('name'))); ?>
								</a><br />
								<span class="entry-details">
									<span class="organization"><?php echo $this->escape(stripslashes($user->get('organization'))); ?></span>
								</span>
							</td>
							<td class="entry-actions">
								<?php if (is_object($this->app->owns)) : ?>
									<?php if (strtolower($row->readonly) == 'yes') : ?>
										<span class="readonly"><?php echo Lang::txt('COM_TOOLS_SESSION_READ_ONLY'); ?></span>
									<?php endif; ?>
									<a class="entry-remove" href="<?php echo Route::url('index.php?option=' . $this->option . '&app=' . $this->toolname . '&task=unshare&sess=' . $this->app->sess.'&username='.$row->viewuser.'&return='.$this->rtrn); ?>" title="<?php echo Lang::txt('COM_TOOLS_SESSION_SHARED_REMOVE_USER'); ?>">
										<span><?php echo Lang::txt('COM_TOOLS_SESSION_SHARED_REMOVE_USER'); ?></span>
									</a>
								<?php endif; ?>
							</td>
						</tr>
						<?php
						}
					}
					?>
				<?php } ?>
					</tbody>
				</table>
			</div><!-- / .col span4 -->
		</div><!-- / .grid -->
	</form>
<?php } // shareable ?>
<?php } ?>

<?php if ($this->config->get('access-manage-session')) { ?>
	<p id="app-manager"><?php echo Lang::txt('COM_TOOLS_SESSION_ADMIN_INFO', $this->app->username, $this->app->ip, $this->app->sess); ?></p>
<?php } ?>

	<?php
	$output = Event::trigger(
		'mw.onSessionView',
		array(
			$this->option,
			$this->toolname,
			$this->app->sess
		)
	);

	if (count($output) > 0)
	{
		?>
		<div id="app-info">
			<h2><?php echo Lang::txt('COM_TOOLS_SESSION_APP_INFO'); ?></h2>
			<div id="app-info-content">
				<?php
				foreach ($output as $out)
				{
					echo $out;
				}
				?>
			</div>
		</div><!-- #app-info -->
		<?php
	}
	?>
</section><!-- / .main section #session-section -->
