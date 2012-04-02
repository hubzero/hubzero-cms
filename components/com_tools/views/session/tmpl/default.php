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

$juser =& JFactory::getUser();
?>
<div id="session">
<?php
if (!$this->app['sess']) {
	echo '<p class="error"><strong>'.JText::_('ERROR').'</strong><br /> '.implode('<br />', $this->output).'</p>';
} else {
?>
	<div id="app-wrap">
		<div id="app-header">
			<h2 class="session-title item:name id:<?php echo $this->app['sess']; ?>">
				<?php echo $this->app['caption']; ?>
			</h2>
			<!-- <ul class="app-toolbar" id="app-options">
				<li>
					<a id="app-btn-about" class="about" href="<?php echo JRoute::_('index.php?option=com_resources&alias=' . $this->toolname); ?>">
						<span><?php echo JText::_('About'); ?></span>
					</a>
				</li>
			</ul> -->
<?php if ($this->app['sess']) { ?>
			<ul class="app-toolbar" id="session-options">
				<li>
					<a id="app-btn-keep" class="keep" href="<?php echo JRoute::_('index.php?option=com_members&task=myaccount'); ?>">
						<span><?php echo JText::_('Keep for later'); ?></span>
					</a>
				</li>
				<li>
					<a id="app-btn-close" class="terminate sessiontips" href="<?php echo JRoute::_('index.php?option='.$this->option.'&app='.$this->toolname.'&task=stop&sess='.$this->app['sess'].'&return='.$this->rtrn); ?>" title="Warning! :: This will end your session.">
						<span><?php echo JText::_('Terminate'); ?></span>
					</a>
				</li>
			</ul>
<?php } ?>
		</div><!-- #app-header -->
<?php
	JPluginHelper::importPlugin('mw');
	$dispatcher =& JDispatcher::getInstance();
	$output = $dispatcher->trigger(
		'onSessionView', 
		array(
			$this->option, 
			$this->toolname,
			$this->app['sess']
		)
	);
	
	if (count($output) > 0) 
	{
?>
		<div id="app-info">
<?php 
		foreach ($output as $out) 
		{
			echo $out;
		} 
?>
		</div><!-- #app-info -->
<?php
	}
?>
		<noscript>
			<p class="warning">
				This site works best when Javascript is enabled in your browser (<a href="/kb/misc/javascript/">How do I do this?</a>).
				Without Javascript support some operations will not work.
			</p>
		</noscript>
		<p id="troubleshoot" class="help">If your application fails to appear within a minute, <a href="/kb/tools/troubleshoot/">troubleshoot this problem.</a></p>

<?php
	$k = 0;
	$html = '<div id="app-content">'."\n";
	foreach ($this->output as $line)
	{
		if (strpos($line,'<div id="app-wrap">')) {
			continue;
		}
		if (strpos($line,"</div>") && $k==0) {
			$k++;
			continue;
		} else {
			$html .= $line."\n";
		}
	}
	$html .= '</div><!-- / #app-content -->'."\n";
	echo $html;
?>
		<div id="app-footer">
<?php 
			if ($this->config->get('show_storage')) {
				$view = new JView(array('name'=>'monitor'));
				$view->option = $this->option;
				$view->amt = $this->app['percent'];
				$view->du = NULL;
				$view->percent = 0;
				$view->msgs = 0;
				$view->ajax = 0;
				$view->writelink = 1;
				$view->total = $this->total;
				$view->display();

				if ($this->app['percent'] >= 100 && isset($this->app['remaining'])) 
				{
					$view = new JView(array('name'=>'monitor','layout'=>'warning'));
					$view->sec = $this->app['remaining'];
					$view->padHours = false;
					$view->option = $this->option;
					$view->display();
				}
			} 
?>
		</div><!-- #app-footer -->
	</div><!-- #app-wrap -->
	
	<div class="clear"></div>
	<?php if ($this->shareable): ?>

	<form name="share" id="app-share" method="get" action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>">
		<div class="three columns first second">
			<p class="share-member-photo">
				<a class="share-anchor" name="shareform"></a>
<?php
				ximport('Hubzero_User_Profile');
				ximport('Hubzero_User_Profile_Helper');
				
				$jxuser = new Hubzero_User_Profile();
				$jxuser->load($juser->get('id'));
				$thumb = Hubzero_User_Profile_Helper::getMemberPhoto($jxuser, 0);
?>
				<img src="<?php echo $thumb; ?>" alt="" />
			</p>
			<fieldset>
				<legend><?php echo JText::_('Share session'); ?></legend>
				
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="task" value="share" />
				<input type="hidden" name="sess" value="<?php echo $this->app['sess']; ?>" />
				<input type="hidden" name="app" value="<?php echo $this->toolname; ?>" />
				<input type="hidden" name="return" value="<?php echo base64_encode(JRoute::_('index.php?option='.$this->option.'&app='.$this->toolname.'&task=session&sess='.$this->app['sess'])); ?>" />
				
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
				
				<label for="field-readonly" id="readonly-label">
					<input class="option" type="checkbox" name="readonly" id="readonly" value="Yes" /> 
					<?php echo JText::_('Read-Only? (only you control the session)'); ?>
				</label>
				
				<p class="submit">
					<input type="submit" value="<?php echo JText::_('Share'); ?>" />
				</p>
				
				<div class="sidenote">
					<p>
						Anyone added for sharing will see your tool session in the <em>My Sessions</em> area of their dashboard. They will be able to manipulate the session unless you check "read-only".
					</p>
				</div>
			</fieldset>
			
			<!-- <p>What does it mean to <a href="/kb/tips/share_a_simulation_session">share a session</a>?</p> -->
			
		</div>
		<div class="three columns third">
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
				ximport('Hubzero_View_Helper_Html');
				
				$config =& JComponentHelper::getParams( 'com_members' );
				$thumb = $config->get('webpath');
				$thumb = DS . trim($thumb, DS);

				$dfthumb = $config->get('defaultpic');
				$dfthumb = DS . ltrim($dfthumb, DS);
				$dfthumb = Hubzero_View_Helper_Html::thumbit($dfthumb);
				
				foreach ($this->shares as $row)
				{
					if ($row->viewuser != $juser->get('username')) { 
						$user = Hubzero_User_Profile::getInstance($row->viewuser);
						
						if ($user->get('uidNumber') < 0) {
							$id = 'n' . -$user->get('uidNumber');
						} else {
							$id = $user->get('uidNumber');
						}

						// User picture
						$uthumb = '';
						if ($user->get('picture')) 
						{
							$uthumb = $thumb . DS . Hubzero_View_Helper_Html::niceidformat($user->get('uidNumber')) . DS . $user->get('picture');
							$uthumb = Hubzero_View_Helper_Html::thumbit($uthumb);
						}

						if ($uthumb && is_file(JPATH_ROOT.$uthumb)) {
							$p = $uthumb;
						} else {
							$p = $dfthumb;
						}
?>
					<tr>
						<th class="entry-img">
							<img width="40" height="40" src="<?php echo $p; ?>" alt="Avatar for <?php echo $this->escape(stripslashes($user->get('name'))); ?>" />
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
							<a class="entry-remove" href="<?php echo JRoute::_('index.php?option='.$this->option.'&app='.$this->toolname.'&task=unshare&sess='.$this->app['sess'].'&username='.$row->viewuser.'&return='.$this->rtrn); ?>" title="Remove this user from sharing">
								<span><?php echo JText::_('Remove this user from sharing'); ?></span>
							</a>
						</td>
					</tr>
<?php
					}
				}
?>
<?php } ?>
				</tbody>
			</table>
		</div>
		<div class="clear"></div>
	</form>
        <?php endif;?>
<?php } ?>

<?php /*if ($this->authorized === 'admin') {
	echo '<p>Administrator viewing '.$this->app['username'].' '.$this->app['ip'].' '.$this->app['sess'].'</p>';


	<p id="powered-by">Powered by <a href="https://nanohub.org/about/middleware/#Maxwell" rel="external">Maxwell&#146;s D&#xE6;mon</a>.</p>
}*/ ?>
</div><!-- / .main section #session-section -->