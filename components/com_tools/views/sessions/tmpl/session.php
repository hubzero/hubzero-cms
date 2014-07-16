<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$juser = JFactory::getUser();

//is this a share session thats read-only
$readOnly = false;
foreach($this->shares as $share)
{
	if ($juser->get('username') == $share->viewuser)
	{
		if (strtolower($share->readonly) == 'yes')
		{
			$readOnly = true;
		}
	}
}

JPluginHelper::importPlugin('mw');
$dispatcher = JDispatcher::getInstance();
?>
<div id="session">
<?php
if (!$this->app->sess) {
	echo '<p class="error"><strong>'.JText::_('ERROR').'</strong><br /> '.implode('<br />', $this->output).'</p>';
} else {
?>

	<?php if ($readOnly) : ?>
		<p class="warning readonly-warning">
			This tool session has been shared with you in 'Read-Only' mode, meaning you don't have control over the session.
		</p>
	<?php endif; ?>

	<div id="app-wrap">
		<div id="app-header">
			<h2 id="session-title" class="session-title item:name id:<?php echo $this->app->sess; ?> <?php if (is_object($this->app->owns)) : ?>editable<?php endif; ?>" rel="<?php echo $this->app->sess; ?>"><?php echo $this->app->caption; ?></h2>
		<?php if ($this->app->sess) { ?>
			<ul class="app-toolbar" id="session-options">
				<li>
					<a id="app-btn-keep" class="keep" href="<?php echo JRoute::_('index.php?option=com_members&task=myaccount'); ?>">
						<span><?php echo JText::_('Keep for later'); ?></span>
					</a>
				</li>
			<?php if ($this->app->owns) { ?>
				<li>
					<a id="app-btn-close" class="terminate sessiontips" href="<?php echo JRoute::_('index.php?option='.$this->option.'&app='.$this->toolname.'&task=stop&sess='.$this->app->sess.'&return='.$this->rtrn); ?>" title="Warning! :: This will end your session.">
						<span><?php echo JText::_('Terminate'); ?></span>
					</a>
				</li>
			<?php } else { ?>
				<li>
					<a id="app-btn-close" class="terminate sessiontips" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&app=' . $this->toolname . '&task=unshare&sess=' . $this->app->sess.'&return='.$this->rtrn); ?>" title="Warning! :: This will end your session.">
						<span><?php echo JText::_('Stop sharing'); ?></span>
					</a>
				</li>
			<?php } ?>
			</ul>
		<?php } ?>
		</div><!-- #app-header -->
		<noscript>
			<p class="warning">
				This site works best when Javascript is enabled in your browser (<a href="/kb/misc/javascript/">How do I do this?</a>).
				Without Javascript support some operations will not work.
			</p>
		</noscript>

		<div id="app-content" class="<?php if ($readOnly) { echo 'view-only'; } ?>" style="width: <?php echo $this->output->width; ?>px; height: <?php echo $this->output->height; ?>px">
			<input type="hidden" id="app-orig-width" name="apporigwidth" value="<?php echo $this->escape($this->output->width); ?>" />
			<input type="hidden" id="app-orig-height" name="apporigheight" value="<?php echo $this->escape($this->output->height); ?>" />
			<?php
			$view = new JView(array(
				'name'   => 'sessions',
				'layout' => 'session_' . (JRequest::getInt('novnc', 0) ? 'novnc' : 'java')
			));
			$view->option   = $this->option;
			$view->output   = $this->output;
			$view->app      = $this->app;
			$view->readOnly = $readOnly;
			$view->display();
			?>
		</div><!-- / #app-content -->
		<div id="app-footer">
			<?php 
			if ($this->config->get('show_storage')) 
			{
				$view = new JView(array(
					'name'   => 'storage', 
					'layout' => 'diskusage'
				));
				$view->option    = $this->option;
				$view->amt       = $this->app->percent;
				$view->du        = NULL;
				$view->percent   = 0;
				$view->msgs      = 0;
				$view->ajax      = 0;
				$view->writelink = 1;
				$view->total     = $this->total;
				$view->display();

				if ($this->app->percent >= 100 && isset($this->app->remaining)) 
				{
					$view = new JView(array(
						'name'   => 'storage',
						'layout' => 'warning'
					));
					$view->sec      = $this->app->remaining;
					$view->padHours = false;
					$view->option   = $this->option;
					$view->display();
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
						<?php echo JText::sprintf('This tool session is powered by the mirror site at %s', $this->zone->get('title', $this->zone->get('zone'))); ?>
					</p>
				</div><!-- / .col span6 -->
				<div class="col span6 omega">
					<form name="share" id="app-zone" method="post" action="<?php echo JRoute::_('index.php?option='.$this->option.'&app='.$this->toolname.'&task=reinvoke&sess='.$this->app->sess); ?>">
						<p><?php echo JText::_('<strong>Warning:</strong> Changing zones will terminate this session.'); ?></p>
						<p><label for="field-zone">
							<?php echo JText::_('Run elsewhere:'); ?>
							<select name="zone" id="field-zone">
								<option value=""><?php echo JText::_('Select ...'); ?></option>
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

	<div class="clear share-divider"></div>
<?php if ($this->config->get('shareable', 0)) { ?>
	<form name="share" id="app-share" method="post" action="<?php echo JRoute::_('index.php?option='.$this->option.'&app='.$this->toolname.'&task=session&sess='.$this->app->sess); ?>">
		<div class="grid">
		<?php if (is_object($this->app->owns)) : ?>
			<div class="col span8">
				<p class="share-member-photo" id="shareform">
					<?php
					$jxuser = new \Hubzero\User\Profile();
					$jxuser->load($juser->get('id'));
					?>
					<img src="<?php echo $jxuser->getPicture(); ?>" alt="" />
				</p>
				<fieldset>
					<legend><?php echo JText::_('Share session'); ?></legend>

					<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
					<input type="hidden" name="controller" value="<?php echo $this->escape($this->controller); ?>" />
					<input type="hidden" name="task" value="share" />
					<input type="hidden" name="sess" value="<?php echo $this->escape($this->app->sess); ?>" />
					<input type="hidden" name="app" value="<?php echo $this->escape($this->toolname); ?>" />
					<input type="hidden" name="return" value="<?php echo base64_encode(JRoute::_('index.php?option='.$this->option.'&app='.$this->toolname.'&task=session&sess='.$this->app->sess)); ?>" />

					<label for="field-username">
						<?php echo JText::_('Share session with:'); ?>
						<?php 
						JPluginHelper::importPlugin('hubzero');
						$mc = $dispatcher->trigger('onGetMultiEntry', array(array('members', 'username', 'acmembers')));
						if (count($mc) > 0) {
							echo '<span class="hint">'.JText::_('(supports usernames, user IDs, and e-mails)').'</span>'.$mc[0];
						} else { ?> 
						<span class="hint"><?php echo JText::_('(enter usernames or user IDs separated by spaces or commas)'); ?></span>
						<input type="text" name="username" id="field-username" value="" />
						<?php } ?>
					</label>
					<label for="group">
						<?php echo JText::_('Share with one of your Groups:'); ?>
						<select name="group" id="group">
							<option value=""><?php echo JText::_('- Select Group &mdash;'); ?></option>
							<?php if (!empty($this->mygroups)) { foreach ($this->mygroups as $group) : ?>
								<option value="<?php echo $group->gidNumber; ?>"><?php echo $group->description; ?></option>
							<?php endforeach; } ?>
						</select>
					</label>
					<label for="field-readonly" id="readonly-label">
						<input class="option" type="checkbox" name="readonly" id="readonly" value="Yes" /> 
						<?php echo JText::_('Read-Only? (only you control the session)'); ?>
					</label>

					<p class="submit">
						<input type="submit" value="<?php echo JText::_('Share'); ?>" id="share-btn" />
					</p>

					<div class="sidenote">
						<p>
							Anyone added for sharing will see your tool session in the <em>My Sessions</em> area of their dashboard. They will be able to manipulate the session unless you check "read-only".
						</p>
					</div>
				</fieldset>
			</div><!-- / .col span8 -->
		<?php endif; ?>
			<div class="<?php if (is_object($this->app->owns)) : ?>col span4 omega<?php endif; ?>">
				<table class="entries" summary="<?php echo Jtext::_('A list of users this session is shared with'); ?>">
					<thead>
						<tr>
							<th<?php if (count($this->shares) > 1) { ?> colspan="3"<?php } ?>>
								This session is shared with:
							</th>
						</tr>
					</thead>
					<tbody>
				<?php if (count($this->shares) <= 1) { ?>
						<tr>
							<td>
								(none)
							</td>
						</tr>
				<?php } else {
					foreach ($this->shares as $row)
					{
						if ($row->viewuser != $juser->get('username')) 
						{ 
							$user = \Hubzero\User\Profile::getInstance($row->viewuser);

							$id = ($user->get('uidNumber') < 0) ? 'n' . -$user->get('uidNumber') : $user->get('uidNumber');

							// User picture
							$p = $user->getPicture();
						?>
						<tr>
							<th class="entry-img">
								<img width="40" height="40" src="<?php echo $p; ?>" alt="<?php echo JText::sprintf('Avatar for %s', $this->escape(stripslashes($user->get('name')))); ?>" />
							</th>
							<td>
								<a class="entry-title" href="<?php echo JRoute::_('index.php?option=com_members&id='.$id); ?>">
									<?php echo $this->escape(stripslashes($user->get('name'))); ?>
								</a><br />
								<span class="entry-details">
									<span class="organization"><?php echo $this->escape(stripslashes($user->get('organization'))); ?></span>
								</span>
							</td>
							<td class="entry-actions">
								<?php if (is_object($this->app->owns)) : ?>
									<?php if (strtolower($row->readonly) == 'yes') : ?>
										<span class="readonly">Readonly</span>
									<?php endif; ?>
									<a class="entry-remove" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&app=' . $this->toolname . '&task=unshare&sess=' . $this->app->sess.'&username='.$row->viewuser.'&return='.$this->rtrn); ?>" title="Remove this user from sharing">
										<span><?php echo JText::_('Remove this user from sharing'); ?></span>
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
	<p id="app-manager"><?php echo JText::sprintf('Administrator viewing <span class="username"><strong>username:</strong> %s</span>, <span class="ip"><strong>IP:</strong> %s</span>, <span class="sess"><strong>session:</strong> %s</span>', $this->app->username, $this->app->ip, $this->app->sess); ?></p>
<?php } ?>

	<?php
	$output = $dispatcher->trigger(
		'onSessionView', 
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
			<h2>App info</h2>
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
</div><!-- / .main section #session-section -->
