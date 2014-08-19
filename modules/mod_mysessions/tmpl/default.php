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
defined('_JEXEC') or die('Restricted access');
?>

<div class="<?php echo $this->params->get('moduleclass_sfx',''); ?> session-list <?php if (!$this->params->get('show_storage', 1)) { echo 'without-storage'; } ?>">
	<ul>
		<?php if (count($this->sessions) > 0) : ?>
			<?php foreach ($this->sessions as $k => $session) : ?>
				<?php
					$cls = ($k == 0) ? 'active' : 'not-active';

					//get the appname
					$bits = explode('_',$session->appname);
					$bit = (count($bits) > 1) ? array_pop($bits) : '';
					$appname = implode('_',$bits);

					//are we on the iPad
					$isiPad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

					//get tool params
					$launchOnIpad = $this->toolsConfig->get('launch_ipad', 0);

					//are we launching on iPad?
					if ($isiPad && $launchOnIpad)
					{
						$resumeLink = 'nanohub://tools/session/' . $session->sessnum;
					}
					else
					{
						$resumeLink = JRoute::_('index.php?option=com_tools&task=session&sess='.$session->sessnum.'&app='.$appname);
					}

					//terminate & disconnect links
					$terminateLink = JRoute::_('index.php?option=com_tools&task=stop&sess='.$session->sessnum.'&app='.$appname);
					$disconnectLink = JRoute::_('index.php?option=com_tools&task=unshare&sess='.$session->sessnum.'&app='.$appname);

					//get snapshot
					$snapshot = DS . 'api' . DS . 'tools' . DS . 'screenshot?sessionid=' . $session->sessnum . '&notfound=1';
				?>
				<li class="session <?php echo $cls; ?>">
					<div class="session-title-bar">
						<?php if ($this->params->get('show_screenshots', 1)) : ?>
							<?php if ($this->params->get('quick_launch', 1)) : ?>
								<a class="session-title-quicklaunch tooltips" title="Quick Launch :: <?php echo JText::_('MOD_MYSESSIONS_RESUME_TITLE'); ?>" href="<?php echo $resumeLink; ?>">
									<img class="snapshot" data-src="<?php echo $snapshot; ?>" />
								</a>
							<?php else : ?>
								<div class="session-title-icon">
									<img class="snapshot" data-src="<?php echo $snapshot; ?>" />
								</div>
							<?php endif; ?>
						<?php else : ?>
							<div class="session-title-noicon">
							</div>
						<?php endif; ?>
						<div class="session-title">
							<?php echo $session->sessname; ?>
							<span class="status"></span>
						</div>
					</div>

					<div class="session-details">
						<?php if ($this->params->get('show_screenshots', 1)) : ?>
							<div class="session-details-left">
								<div class="session-snapshot">
									<a class="session-snapshot-link" href="<?php echo $snapshot; ?>" title="<?php echo $session->sessname; ?>">
										<img class="snapshot snapshot-main" data-src="<?php echo $snapshot; ?>" />
									</a>
								</div>
							</div>
						<?php endif; ?>
						<div class="session-details-right">
							<div class="session-accesstime">
								<span>Last Accessed:</span>
								<?php echo date("F d, Y @ g:ia", strtotime($session->accesstime)); ?>
							</div>

							<?php if ($this->juser->get('username') != $session->username) : ?>
								<div class="session-sharing">
									<span>Session Owner:</span>
									<?php
										$user = JUser::getInstance($session->username);
										echo '<a href="/members/' . $user->get('id') . '" title="Go to ' . $user->get('name') . '\'s Profile">' . $user->get('name') . '</a>';
									?>
								</div>
							<?php endif; ?>

							<div class="session-buttons">
								<a class="btn icon-resume resume" href="<?php echo $resumeLink; ?>" title="<?php echo JText::_('MOD_MYSESSIONS_RESUME_TITLE'); ?>">
									<?php echo ucfirst( JText::_('MOD_MYSESSIONS_RESUME') ); ?>
								</a>
								<?php $tcls = ($this->params->get('terminate_double_check', 1)) ? 'terminate-confirm' : 'terminate'; ?>
								<?php if ($this->juser->get('username') == $session->username) : ?>
									<a class="btn icon-terminate <?php echo $tcls; ?>" href="<?php echo $terminateLink; ?>" title="<?php echo JText::_('MOD_MYSESSIONS_TERMINATE_TITLE'); ?>">
										<?php echo ucfirst( JText::_('MOD_MYSESSIONS_TERMINATE') ); ?>
									</a>
								<?php else : ?>
									<a class="btn icon-disconnect disconnect" href="<?php echo $disconnectLink; ?>" title="<?php echo JText::_('MOD_MYSESSIONS_DISCONNECT_TITLE'); ?>">
										<?php echo ucfirst( JText::_('MOD_MYSESSIONS_DISCONNECT') ); ?>
									</a>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		<?php else : ?>
			<li class="no-sessions">
				<?php echo JText::_('MOD_MYSESSIONS_NONE'); ?>
			</li>
		<?php endif; ?>
	</ul>
</div>

<?php if ($this->params->get('show_storage', 1)) : ?>
	<div class="session-storage">
		<span><?php echo JText::_('MOD_MYSESSIONS_STORAGE'); ?> (<a href="<?php echo JRoute::_('index.php?option=com_tools&task=storage'); ?>"><?php echo JText::_('MOD_MYSESSIONS_MANAGE'); ?></a>)</span>
		<?php
			$diskUsage = MwUtils::getDiskUsage($this->juser->get('username'));
			if (!is_array($diskUsage) || !isset($diskUsage['space']))
			{
				echo "<p class=\"error\">" . JText::_('MOD_MYSESSIONS_ERROR_RETRIEVING_STORAGE') . "</p></div>";
				return;
			}
			else if (isset($diskUsage['softspace']) && $diskUsage['softspace'] == 0)
			{
				echo "<p class=\"info\">" . JText::_('MOD_MYSESSIONS_NO_QUOTA') . "</p></div>";
				return;
			}
			else
			{
				// Calculate the percentage of spaced used
				bcscale(6);
				$total 		= $diskUsage['softspace'] / 1024000000;
				$val 		= ($diskUsage['softspace'] > 0) ? bcdiv($diskUsage['space'], $diskUsage['softspace']) : 0;
				$percent 	= round( $val * 100 );
				$percent 	= ($percent > 100) ? 100: $percent;

				// Amount can only have a max of 100 due to some display restrictions
				$amount  	= ($percent > 100) ? 100 : $percent;

				//show different colored bar
				$cls 		= ($percent < 50) ? 'storage-low' : 'storage-high';
			}
		?>

		<div class="storage-meter <?php echo $cls; ?>">
			<?php if ($amount > 0) : ?>
				<span class="storage-meter-percent" style="width:<?php echo $percent; ?>%"></span>
			<?php endif; ?>
			<span class="storage-meter-amount"><?php echo $amount . '% of ' . $total . 'GB'; ?></span>
		</div>

		<?php if ($percent == 100) : ?>
			<p class="warning">
				<?php echo JText::_('MOD_MYSESSIONS_MAXIMUM_STORAGE'); ?>
			</p>
		<?php endif; ?>

		<?php if ($percent > 100) : ?>
			<p class="warning">
				<?php echo JText::_('MOD_MYSESSIONS_EXCEEDING_STORAGE'); ?>
			</p>
		<?php endif; ?>
	</div>
<?php endif; ?>