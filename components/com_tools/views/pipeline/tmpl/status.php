<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// get configurations/ defaults
$developer_site = $this->config->get('developer_site', 'hubFORGE');
$live_site = rtrim(JURI::base(),'/');
$developer_url = $live_site = "https://" . preg_replace('#^(https://|http://)#','',$live_site);
$project_path 	= $this->config->get('project_path', '/tools/');
$dev_suffix 	= $this->config->get('dev_suffix', '_dev');

ximport('Hubzero_View_Helper_Html');

// get status name
ToolsHelperHtml::getStatusName($this->status['state'], $state);
ToolsHelperHtml::getStatusClass($this->status['state'], $this->statusClass);
?>
<div id="content-header">
	<h2><?php echo $this->title; ?> - <span class="state_hed"><?php echo $state; ?></span></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<ul id="useroptions">
    	<li><a class="main-page btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=pipeline'); ?>"><?php echo JText::_('CONTRIBTOOL_ALL_TOOLS'); ?></a></li>
		<li class="last"><a class="add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=create'); ?>"><?php echo JText::_('CONTRIBTOOL_NEW_TOOL'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">  
<?php
	if (ToolsHelperHtml::toolActive($this->status['state'])) {
		$states = array(
			JText::_('REGISTERED'),
			JText::_('CREATED'),
			JText::_('UPLOADED'),
			JText::_('INSTALLED'),
			JText::_('APPROVED'),
			JText::_('PUBLISHED')
		); // regular state list

		if ($state == JText::_('RETIRED')) 
		{
			$states[] = JText::_('RETIRED');
		}

		if ($state == JText::_('UPDATED')) 
		{
			$states[2] = JText::_('UPDATED');
		}

		$key = array_keys($states, $state);
?>
	<div class="clear"></div>
	<ol id="steps">
		<li class="steps_hed"><?php echo JText::_('STATUS'); ?>:</li>
<?php 
		for ($i=0, $n=count($states); $i < $n; $i++) 
		{ 
			$cls = 'done';
			if (strtolower($state) == strtolower($states[$i]))
			{
				$cls = 'active';
			}
			else if (count($key) == 0 or $i > $key[0]) 
			{
				$cls = 'future';
			}
?>
		<li class="<?php echo $cls; ?>">
			<?php echo $states[$i]; ?>
		</li>
<?php
		}
?>
	</ol>
	<div class="clear"></div>
<?php
	}
?>
	<div class="toolinfo_note"> 
	<?php if ($this->msg) { echo '<p class="passed">'.$this->msg.'</p>'; } ?>
	<?php if (ToolsHelperHtml::getNumofTools($this->status)) { echo '<p>'.ToolsHelperHtml::getNumofTools($this->status).'.</p>'; }?>
	</div><!-- / .toolinfo_note -->

	<div class="two columns first">
		<div class="toolinfo<?php echo $this->statusClass; ?>"> 
			<table id="toolstatus">
				<tbody>
					<tr>
						<th colspan="2" class="toolinfo_hed">
							<?php echo JText::_('TOOL_INFO'); ?> 
						<?php if (ToolsHelperHtml::toolActive($this->status['state'])) { ?>
							<a class="edit button" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&app=' . $this->status['toolname']); ?>" title="<?php echo JText::_('EDIT_TIPS'); ?>"><?php echo JText::_('EDIT'); ?></a>
						<?php } ?>
						</th>
					</tr>
					<tr>
						<th><?php echo JText::_('TITLE'); ?></th>
						<td><?php echo $this->escape(stripslashes($this->status['title'])) . ' ('.$this->status['toolname'].' - '.strtolower(JText::_('ID')).' #'.$this->status['toolid'].')'; ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('VERSION'); ?></th>
						<td><?php echo ($this->status['version']) ? JText::_('THIS_VERSION').' '.$this->status['version']: JText::_('THIS_VERSION').': '.JText::_('NO_LABEL');
							if (!$this->status['published'] or ($this->status['version']!=$this->status['currentversion'] && ToolsHelperHtml::toolActive($this->status['state']))) { echo ' ('.JText::_('UNDER_DEVELOPMENT').')';  }
							if ($this->status['published']) { echo ' [<a href="'.JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=versions&app='.$this->status['toolname']).'">'.strtolower(JText::_('ALL_VERSIONS')).'</a>]'; }  ?>
						</td>
					</tr>
					<tr>
						<th><?php echo JText::_('AT_A_GLANCE'); ?></th>
						<td><?php echo htmlspecialchars(stripslashes($this->status['description'])); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('DESCRIPTION'); ?></th>
						<td>
							<a href="<?php echo JRoute::_('index.php?option=com_resources&id=' . $this->status['resourceid'] . '&rev=dev'); ?>"><?php echo JText::_('PREVIEW'); ?></a> | 
							<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=resource&step=1&app=' . $this->status['toolname']); ?>"><?php echo JText::_('EDIT_THIS_PAGE'); ?></a>
						</td>
					</tr>
					<tr>
						<th><?php echo JText::_('VNC_GEOMETRY'); ?></th>
						<td><?php echo $this->status['vncGeometryX'].'x'.$this->status['vncGeometryY'];?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('TOOL_EXEC'); ?></th>
						<td><?php echo ToolsHelperHtml::getToolAccess($this->status['exec'], $this->status['membergroups']); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('SOURCE_CODE'); ?></th>
						<td><?php echo ToolsHelperHtml::getCodeAccess($this->status['code']); ?>
						<?php if ( ToolsHelperHtml::toolActive($this->status['state']) && ToolsHelperHtml::toolWIP($this->status['state'])) { ?>
							[<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=license&app=' . $this->status['toolname']); ?>"><?php echo JText::_('CHANGE_LICENSE'); ?></a>]
						<?php } ?>
						</td>
					</tr>
					<tr>
						<th><?php echo JText::_('PROJECT_AREA'); ?></th>
						<td><?php echo ToolsHelperHtml::getWikiAccess($this->status['wiki']); ?></td>
					</tr>
					<tr>
						<th><?php echo JText::_('DEVELOPMENT_TEAM'); ?></th>
						<td><?php echo ToolsHelperHtml::getDevTeam($this->status['developers']); ?></td>
					</tr>
					<tr>
						<th colspan="2" class="toolinfo_hed"><?php echo JText::_('DEVELOPER_TOOLS');?></th>
					</tr>
					<tr>
						<th colspan="2">
						<!-- / tool admin icons -->
							<ul class="adminactions">
								<li class="history"><a href="<?php echo JRoute::_('index.php?option=com_support&task=ticket&id=' . $this->status['ticketid']); ?>" title="<?php echo JText::_('HISTORY_TIPS');?>">History</a></li>
							<?php if ($this->status['state'] != 'Registered') { // hide for tools in registered status ?>
								<li class="wiki"><a href="<?php echo $developer_url . $project_path . $this->status['toolname']; ?>/wiki" title="<?php echo JText::_('WIKI_TIPS');?>">Wiki</a></li>
								<li class="sourcecode"><a href="<?php echo $developer_url . $project_path . $this->status['toolname']; ?>/browser" title="<?php echo JText::_('SOURCE_TIPS');?>"><?php echo JText::_('SOURCE');?></a></li>
								<li class="timeline"><a href="<?php echo $developer_url . $project_path . $this->status['toolname']; ?>/timeline" title="<?php echo JText::_('TIMELINE_TIPS');?>"><?php echo JText::_('TIMELINE');?></a></li>
							<?php }  else { ?>
								<li class="wiki"><span class="disabled"><?php echo JText::_('WIKI');?></span></li>
								<li class="sourcecode"><span class="disabled"><?php echo JText::_('SOURCE_CODE');?></span></li>
								<li class="timeline"><span class="disabled"><?php echo JText::_('TIMELINE');?></span></li>
							<?php } ?>
								<li class="message"><a href="javascript:void(0);" title="<?php echo JText::_('SEND_MESSAGE').' '.JText::_('TO');?> <?php echo ($this->config->get('access-admin-component')) ? strtolower(JText::_('DEVELOPMENT_TEAM')) : JText::_('SITE_ADMIN'); ?>" class="showmsg"><?php echo JText::_('MESSAGE');?></a></li>
							<?php if ($this->status['published']!=1 && ToolsHelperHtml::toolActive($this->status['state'])) {  // show cancel option only for tools under development ?>
								<li class="canceltool"><a href="javascript:void(0);" title="<?php echo JText::_('CANCEL_TIPS');?>" class="showcancel"><?php echo JText::_('CANCEL');?></a></li>
 							<?php } ?>                   
							</ul>
							<div id="ctCancel">
								<p class="error">
									<span class="cancel_warning"><?php echo JText::_('CANCEL_WARNING');?> </span>
									<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=cancel&app=' . $this->status['toolname']); ?>"><?php echo JText::_('CANCEL_YES');?></a>
									<span class="boundary">|</span> <a href="javascript:void(0);" class="hidecancel"><?php echo JText::_('CANCEL_NO');?></a>
								</p>
							</div>
							<div id="ctComment">
								<span class="closebox"><a href="javascript:void(0);" class="hidemsg">x</a></span>
								<h4><?php echo JText::_('SEND_MESSAGE').' '.JText::_('TO');?> <?php echo ($this->config->get('access-admin-component')) ? strtolower(JText::_('DEVELOPMENT_TEAM')) : strtolower(JText::_('SITE_ADMIN')); ?>:</h4>
								<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app=' . $this->status['toolname']); ?>" method="post" id="commentForm">
								<?php if ($this->config->get('access-admin-component')) { ?>
									<fieldset>
										<label><input type="checkbox" name="access" value="1" /> <?php echo JText::_('COMMENT_PRIVACY_TIPS'); ?></label>
									</fieldset>
								<?php } ?>
									<fieldset>
										<textarea name="comment" style="width:300px;height:100px;" cols="50" rows="5"></textarea>
									</fieldset>
									<fieldset>
										<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
										<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
										<input type="hidden" name="task" value="message" />
										<input type="hidden" name="id" value="<?php echo $this->status['toolid']; ?>" />
										<input type="hidden" name="app" value="<?php echo $this->status['toolname']; ?>" />
										<input type="submit" value="<?php echo JText::_('SEND_MESSAGE'); ?>" />
									</fieldset>
								</form>
							</div>
						</th>
					</tr>
				<?php if ($this->config->get('access-admin-component')) { ?>
					<tr>
						<th colspan="2" class="toolinfo_hed"><?php echo JText::_('ADMIN_CONTROLS');?></th>
					</tr>
					<tr>
						<th colspan="2">
							<!-- / admin controls -->
							<form action="index.php" method="post" id="adminCalls">
								<ul class="adminactions">
									<li id="createtool"><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=admin&task=addrepo&app=' . $this->status['toolname']); ?>" class="admincall" title="<?php echo JText::_('COMMAND_ADD_REPO_TIPS');?>"><?php echo JText::_('COMMAND_ADD_REPO');?></a></li>
									<li id="installtool"><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=admin&task=install&app=' . $this->status['toolname']); ?>" class="admincall" title="<?php echo JText::_('COMMAND_INSTALL_TIPS');?>"><?php echo JText::_('COMMAND_INSTALL');?></a></li>
									<li id="publishtool"><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=admin&task=publish&app=' . $this->status['toolname']); ?>" class="admincall" title="<?php echo JText::_('COMMAND_PUBLISH_TIPS');?>"><?php echo JText::_('COMMAND_PUBLISH');?></a></li>
									<li id="retiretool"><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=admin&task=retire&app=' . $this->status['toolname']); ?>" class="admincall" title="<?php echo JText::_('COMMAND_RETIRE_TIPS');?>"><?php echo JText::_('COMMAND_RETIRE');?></a></li>
								</ul>
								<div id="ctSending"></div>
								<div id="ctSuccess"></div>
								<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
								<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
								<input type="hidden" name="task" value="" />
								<input type="hidden" name="id" value="<?php echo $this->status['toolid']?>" />
								<input type="hidden" name="app" value="<?php echo $this->status['toolname']?>" />
								<input type="hidden" name="no_html" value="1" />
							</form>
						</th>
					</tr>
					<tr>
						<th>
							<span class="admin_label"><?php echo JText::_('FLIP_STATUS');?>:</span>
							<span class="admin_label"><?php echo JText::_('PRIORITY');?>:</span>
							<span class="admin_label"><?php echo JText::_('MESSAGE_TO_DEV_TEAM') . ' <br />(' . JText::_('OPTIONAL') . ')';?></span>
						</th>
						<td>
							<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app=' . $this->status['toolname']); ?>" method="post" id="adminForm">
								<fieldset class="admin_label">
									<select name="newstate">
										<option value="1"<?php if ($this->status['state'] == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('REGISTERED');?></option>
										<option value="2"<?php if ($this->status['state'] == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('CREATED');?></option>
										<option value="3"<?php if ($this->status['state'] == 3) { echo ' selected="selected"'; } ?>><?php echo JText::_('UPLOADED');?></option>
										<option value="4"<?php if ($this->status['state'] == 4) { echo ' selected="selected"'; } ?>><?php echo JText::_('INSTALLED');?></option>
										<option value="5"<?php if ($this->status['state'] == 5) { echo ' selected="selected"'; } ?>><?php echo JText::_('UPDATED');?></option>
										<option value="6"<?php if ($this->status['state'] == 6) { echo ' selected="selected"'; } ?>><?php echo JText::_('APPROVED');?></option>
										<option value="7"<?php if ($this->status['state'] == 7) { echo ' selected="selected"'; } ?>><?php echo JText::_('PUBLISHED');?></option>
									<?php if ($this->status['published']==1) { // admin can retire only tools that have a published flag on ?>
										<option value="8"<?php if ($this->status['state'] == 8) { echo ' selected="selected"'; } ?>><?php echo JText::_('RETIRED');?></option>
									<?php } ?>
									</select>
								</fieldset>
								<fieldset class="admin_label">
									<select name="priority">
										<option value="3"<?php if ($this->status['priority'] == 3) { echo ' selected="selected"'; } ?>><?php echo JText::_('NORMAL');?></option>
										<option value="2"<?php if ($this->status['priority'] == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('HIGH');?></option>
										<option value="1"<?php if ($this->status['priority'] == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('CRITICAL');?></option>
										<option value="4"<?php if ($this->status['priority'] == 4) { echo ' selected="selected"'; } ?>><?php echo JText::_('LOW');?></option>
										<option value="5"<?php if ($this->status['priority'] == 5) { echo ' selected="selected"'; } ?>><?php echo JText::_('LOWEST');?></option>
									</select>
									<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
									<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
									<input type="hidden" name="task" value="update" />
									<input type="hidden" name="id" value="<?php echo $this->status['toolid']; ?>" />
									<input type="hidden" name="app" value="<?php echo $this->status['toolname']; ?>" />
								</fieldset>
								<fieldset class="admin_label">
									<textarea name="comment" id="comment" cols="40" rows="5"></textarea>
									<input type="submit" class="submitform" value="<?php echo JText::_('APPLY_CHANGE'); ?>" />
								</fieldset>
							</form>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div><!-- / .two columns first -->
	<div class="two columns second">
		<div id="whatsnext">
			<h2 class="nextaction"><?php echo JText::_('WHAT_NEXT');?></h2>
			<form action="index.php" method="post" id="statusForm">
				<fieldset>
					<input type="hidden" name="option" value="<?php echo $this->option ?>" />
					<input type="hidden" name="task" value="update" />
					<input type="hidden" name="id" value="<?php echo $this->status['toolid']?>" />
					<input type="hidden" name="toolname" value="<?php echo $this->status['toolname']?>" />	
					<input type="hidden" name="newstate" id="newstate" value="" />
				</fieldset>	
			</form>
			<?php 
			$juser = JFactory::getUser();
			$jconfig =& JFactory::getConfig();
			$juri =& JURI::getInstance();
			//$juri->base();

			$jconfig =& JFactory::getConfig();
			$hubShortName 	= $jconfig->getValue('config.sitename'); //$hubShortName;
			$hubShortURL 	= str_replace('https://', '', $juri->base()); //$hubShortURL;
			$hubLongURL 	= $juri->base(); //$hubLongURL;

			// get tool access text
			$toolaccess = ToolsHelperHtml::getToolAccess($this->status['exec'], $this->status['membergroups']);
			$live_site = rtrim(JURI::base(),'/');
			$developer_url = $live_site = "https://" . preg_replace('#^(https://|http://)#','',$live_site);
				
			// get configurations/ defaults
			$developer_site = $this->config->get('developer_site', 'hubFORGE');
			$rappture_url   = $this->config->get('rappture_url', '');
			$learn_url      = $this->config->get('learn_url', '');
			$project_path   = $this->config->get('project_path', '/tools/');
			$dev_suffix     = $this->config->get('dev_suffix', '_dev');

			// set common paths
			$this->statuspath =  JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app=' . $this->status['toolname']);
			$testpath = JRoute::_('index.php?option=' . $this->option . '&controller=sessions&task=invoke&app=' . $this->status['toolname'] . '&version=test');

			switch ($this->status['state']) 
			{
				//  registered
				case 1:
			?>
				<p>
					<?php echo JText::_('TEAM_WILL_CREATE'); ?> <a href="<?php echo $developer_url; ?>/tools"><?php echo $developer_site; ?></a>, <?php echo JText::_('WHATSNEXT_REGISTERED_INSTRUCTIONS');?>. 
					<?php echo JText::_('WHATSNEXT_IT_HAS_BEEN'); ?> <?php echo Hubzero_View_Helper_Html::timeAgo($this->status['changed']); ?> <?php echo JText::_('WHATSNEXT_SINCE_YOUR_REQUEST'); ?>. 
					<?php echo JText::_('WHATSNEXT_YOU_WILL_RECEIVE_RESPONSE'); ?> 24 <?php echo JText::_('HOURS'); ?>
				</p>
				<h4><?php echo JText::_('WHATSNEXT_REMAINING_STEPS'); ?>:</h4>
				<ul>
					<li class="complete">
						<?php echo JText::_('WHATSNEXT_REGISTER'); ?> 
						<?php echo $hubShortName; ?>
					</li>
					<li class="incomplete">
						<?php echo JText::_('WHATSNEXT_UPLOAD_CODE'); ?> 
						<?php echo $developer_site; ?>
					</li>
				<?php if ($this->status['resource_modified'] == '1') { ?>
					<li class="complete">
						<?php echo JText::_('TODO_MAKE_RES_PAGE'); ?>.
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resource&task=preview&app=' . $this->status['toolname']); ?>"><?php echo JText::_('PREVIEW'); ?></a> | 
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resource&step=1&app=' . $this->status['toolname']); ?>"><?php echo JText::_('TODO_EDIT_PAGE'); ?>...</a>
					</li>
				<?php } else { ?>
					<li class="todo">
						<?php echo JText::_('TODO_MAKE_RES_PAGE'); ?>.
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resource&step=1&app=' . $this->status['toolname']); ?>"><?php echo JText::_('TODO_CREATE_PAGE'); ?>...</a>
					</li>
				<?php } ?>
					<li class="incomplete">
						<?php echo JText::_('WHATSNEXT_TEST_AND_APPROVE'); ?>
					</li>
					<li class="incomplete">
						<?php echo JText::_('WHATSNEXT_PUBLISH'); ?> <?php echo $hubShortURL; ?>
					</li>
				</ul>
			<?php
				break;

				//  created
				case 2:
			?>
				<p>
					<?php echo ucfirst(JText::_('THE')); ?> <?php echo $hubShortName; ?>  <?php echo JText::_('WHATSNEXT_AREA_CREATED'); ?> <a href="<?php echo $developer_url; ?>"><?php echo $developer_site; ?></a>:<br />
					<a href="<?php echo $developer_url . $project_path . $this->status['toolname']; ?>/wiki"><?php echo $developer_url . $project_path . $this->status['toolname']; ?>/wiki</a>
				</p>
				<p>
					<?php echo JText::_('WHATSNEXT_FOLLOW_STEPS'); ?>:
				</p>
				<ul>
				<?php if (!empty($learn_url)) { ?>
					<li><a href="<?php echo $learn_url; ?>"><?php echo JText::_('LEARN_MORE'); ?></a> <?php echo JText::_('WHATSNEXT_ABOUT_UPLOADING'); ?></li>
				<?php } ?>
				<?php if (!empty($rappture_url)) { ?>
					<li><?php echo JText::_('LEARN_MORE'); ?> <?php echo JText::_('ABOUT'); ?> <?php echo JText::_('THE'); ?> <a href="<?php echo $rappture_url; ?>">Rappture toolkit</a>.</li>
				<?php } ?>
					<li><?php echo JText::_('WHATSNEXT_WHEN_READY'); ?>, <a href="<?php echo $developer_url . $project_path . $this->status['toolname']; ?>/wiki/GettingStarted"><?php echo JText::_('WHATSNEXT_FOLLOW_THESE_INSTRUCTIONS'); ?></a> <?php echo JText::_('WHATSNEXT_TO_ACCESS_CODE'); ?>.</li>
				</ul>
				<h2><?php echo JText::_('WHATSNEXT_WE_ARE_WAITING'); ?></h2>
				<p><?php echo JText::_('WHATSNEXT_CREATED_LET_US_KNOW'); ?>:</p>
				<ul>
					<li class="todo">
						<span id="Uploaded">
							<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=update&newstate=Uploaded&app=' . $this->status['toolname']); ?>" class="flip">
								<?php echo JText::_('WHATSNEXT_CREATED_CODE_UPLOADED'); ?>
							</a>
						</span>
					</li>
				</ul>
				<h4><?php echo JText::_('WHATSNEXT_REMAINING_STEPS'); ?>:</h4>
				<ul>
					<li class="complete"><?php echo JText::_('WHATSNEXT_REGISTER'); ?> <?php echo $hubShortName; ?></li>
					<li class="incomplete">
						<?php echo JText::_('WHATSNEXT_UPLOAD_COMMIT_FINAL_CODE'); ?> 
						<span id="Uploaded_">
							<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=update&newstate=Uploaded&app=' . $this->status['toolname']); ?>" class="flip">
								<?php echo JText::_('WHATSNEXT_DONE'); ?>
							</a>
						</span> 
						<br /><a href="<?php echo $developer_url . $project_path . $this->status['toolname']; ?>/wiki/GettingStarted"><?php echo JText::_('WHATSNEXT_UPLOAD_HOW_DO_I_DO_THIS'); ?></a>
					</li>
				<?php if ($this->status['resource_modified'] == '1') { ?>
					<li class="complete">
						<?php echo JText::_('TODO_MAKE_RES_PAGE'); ?>.
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resource&task=preview&app=' . $this->status['toolname']); ?>"><?php echo JText::_('PREVIEW'); ?></a> | 
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resource&step=1&app=' . $this->status['toolname']); ?>"><?php echo JText::_('TODO_EDIT_PAGE'); ?>...</a>
					</li>
				<?php } else { ?>
					<li class="todo">
						<?php echo JText::_('TODO_MAKE_RES_PAGE'); ?>.
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resource&step=1&app=' . $this->status['toolname']); ?>"><?php echo JText::_('TODO_CREATE_PAGE'); ?>...</a>
					</li>
				<?php } ?>
					<li class="incomplete">
						<?php echo JText::_('WHATSNEXT_TEST_AND_APPROVE'); ?>
					</li>
					<li class="incomplete">
						<?php echo JText::_('WHATSNEXT_PUBLISH'); ?> <?php echo $hubShortURL; ?>
					</li>
				</ul>
			<?php
				break;

				//  uploaded
				case 3:
			?>
				<p>
					<?php echo ucfirst(JText::_('THE')); ?> <?php echo $hubShortName; ?> <?php echo JText::_('WHATSNEXT_UPLOADED_TEAM_NEEDS'); ?> <?php echo $hubShortName; ?> <?php echo JText::_('WHATSNEXT_UPLOADED_SO_YOU_CAN_TEST'); ?>. 
					<?php echo JText::_('WHATSNEXT_IT_HAS_BEEN'); ?> <?php echo Hubzero_View_Helper_Html::timeAgo($this->status['changed']); ?> <?php echo JText::_('WHATSNEXT_SINCE_LAST_STATUS_CHANGE'); ?>. 
					<?php echo JText::_('WHATSNEXT_YOU_WILL_RECEIVE_RESPONSE'); ?> 3 <?php echo JText::_('DAYS'); ?>.
				</p>
				<h4><?php echo JText::_('WHATSNEXT_REMAINING_STEPS'); ?>:</h4>
				<ul>
					<li class="complete">
						<?php echo JText::_('WHATSNEXT_REGISTER'); ?> <?php echo $hubShortName; ?>
					</li>
					<li class="complete">
						<?php echo JText::_('WHATSNEXT_UPLOAD_CODE'); ?> <?php echo $developer_site; ?>
					</li>
				<?php if ($this->status['resource_modified'] == '1') { ?>
					<li class="complete">
						<?php echo JText::_('TODO_MAKE_RES_PAGE'); ?>.
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resource&task=preview&app=' . $this->status['toolname']); ?>"><?php echo JText::_('PREVIEW'); ?></a> | 
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resource&step=1&app=' . $this->status['toolname']); ?>"><?php echo JText::_('TODO_EDIT_PAGE'); ?>...</a>
					</li>
				<?php } else { ?>
					<li class="todo">
						<?php echo JText::_('TODO_MAKE_RES_PAGE'); ?>.
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resource&step=1&app=' . $this->status['toolname']); ?>"><?php echo JText::_('TODO_CREATE_PAGE'); ?>...</a>
					</li>
				<?php } ?>
					<li class="incomplete">
						<?php echo JText::_('WHATSNEXT_TEST_AND_APPROVE'); ?>
					</li>
					<li class="incomplete">
						<?php echo JText::_('WHATSNEXT_PUBLISH'); ?> <?php echo $hubShortURL; ?>
					</li>
			<?php
				break;

				//  installed
				case 4:
			?>
				<p>
					<?php echo JText::_('WHATSNEXT_INSTALLED_CODE_READY'); ?> <?php echo $hubShortURL; ?>. <?php echo JText::_('WHATSNEXT_INSTALLED_PLS_TEST'); ?>:
				</p>
				<ul>
					<li class="todo">
						<span id="primary-document">
							<?php echo JText::_('WHATSNEXT_INSTALLED_TEST'); ?>: <a class="launchtool" style="padding:0.4em 0.2em 0.1em 1.5em;margin-top:1em;" href="<?php echo $testpath; ?>"><?php echo JText::_('LAUNCH_TOOL'); ?></a>
						</span>
					</li>
					<li class="todo">
				<?php if ($this->status['resource_modified']) { ?>
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resource&task=preview&app=' . $this->status['toolname']); ?>"><?php echo JText::_('TODO_REVIEW_RES_PAGE'); ?></a>
				<?php } else { ?>
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resource&step=1&app=' . $this->status['toolname']); ?>"><?php echo JText::_('TODO_CREATE_PAGE'); ?></a>
				<?php } ?>
					</li>
				</ul>
				<?php if (!$this->status['resource_modified']) { ?>
				<p class="warning">
					<?php echo JText::_('PLEASE'); ?> <a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resource&step=1&app=' . $this->status['toolname']); ?>"><?php echo strtolower(JText::_('CREATE')); ?></a> <?php echo JText::_('WHATSNEXT_PAGE_DESC'); ?>.
				</p>
				<?php } ?>
				<h2><?php echo JText::_('WHATSNEXT_WE_ARE_WAITING'); ?></h2>
				<p><?php echo JText::_('WHATSNEXT_INSTALLED_CLICK_AFTER_TESTING'); ?>:</p>
				<ul>
				<?php if ($this->status['resource_modified']) { ?>
					<li class="todo">
						<span id="Approved"><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=update&newstate=Approved&app=' . $this->status['toolname']); ?>" class="flip" ><?php echo JText::_('WHATSNEXT_INSTALLED_TOOL_WORKS'); ?></a></span>
					</li>
				<?php } else { ?>
					<li class="todo_disabled">
						<?php echo JText::_('WHATSNEXT_INSTALLED_TOOL_WORKS'); ?>
					</li>
				<?php } ?>
				</ul>
				<p><?php echo JText::_('WHATSNEXT_INSTALLED_NEED_CHANGES'); ?>:</p>
				<ul>
					<li class="todo">
						<span id="Updated"><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=update&newstate=Updated&app=' . $this->status['toolname']); ?>" class="flip" ><?php echo JText::_('WHATSNEXT_CODE_FIXED_PLS_INSTALL'); ?>.</a></span>
					</li>
				</ul>
				<h4><?php echo JText::_('WHATSNEXT_REMAINING_STEPS'); ?>:</h4>
				<ul>
					<li class="complete">
						<?php echo JText::_('WHATSNEXT_REGISTER'); ?> <?php echo $hubShortName; ?>
					</li>
					<li class="complete">
						<?php echo JText::_('WHATSNEXT_UPLOAD_CODE'); ?> <?php echo $developer_site; ?>
					</li>
				<?php if ($this->status['resource_modified'] == '1') { ?>
					<li class="complete">
						<?php echo JText::_('TODO_MAKE_RES_PAGE'); ?>.
						<a href="<?php echo JRoute::_('index.php?option=com_resources&id=' . $this->status['resourceid'] . '&rev=dev'); ?>"><?php echo JText::_('PREVIEW'); ?></a> | 
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resource&step=1&app=' . $this->status['toolname']); ?>"><?php echo JText::_('TODO_EDIT_PAGE'); ?>...</a>
					</li>
				<?php } else { ?>
					<li class="todo">
						<?php echo JText::_('TODO_MAKE_RES_PAGE'); ?>.
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resource&step=1&app=' . $this->status['toolname']); ?>"><?php echo JText::_('TODO_CREATE_PAGE'); ?>...</a>
					</li>
				<?php } ?>
					<li class="todo">
						<?php echo JText::_('WHATSNEXT_TEST_AND_APPROVE'); ?>. 
				<?php if ($this->status['resource_modified'] == '1') { ?>
						<span id="Approved_"><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=update&newstate=Approved&app=' . $this->status['toolname']); ?>" class="flip"><?php echo JText::_('WHATSNEXT_I_APPROVE'); ?></a></span> 
				<?php } else { ?>
						<span class="disabled"><?php echo JText::_('WHATSNEXT_I_APPROVE'); ?></span> 
				<?php } ?>
						| <span id="Updated_"><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=update&newstate=Updated&app=' . $this->status['toolname']); ?>" class="flip"><?php echo JText::_('WHATSNEXT_CHANGES_MADE'); ?></a></span>
					</li>
					<li class="incomplete">
						<?php echo JText::_('WHATSNEXT_PUBLISH'); ?> <?php echo $hubShortURL; ?>
					</li>
				</ul>
			<?php 
				break;

				//  updated
				case 5:
			?>
				<p>
					<?php echo ucfirst(JText::_('THE')); ?> <?php echo $hubShortName; ?> <?php echo JText::_('WHATSNEXT_UPLOADED_TEAM_NEEDS'); ?> <?php echo $hubShortName; ?> <?php echo JText::_('WHATSNEXT_UPLOADED_SO_YOU_CAN_TEST'); ?>. 
					<?php echo JText::_('WHATSNEXT_IT_HAS_BEEN'); ?> <?php echo Hubzero_View_Helper_Html::timeAgo($this->status['changed']); ?> <?php echo JText::_('WHATSNEXT_SINCE_LAST_STATUS_CHANGE'); ?>. 
					<?php echo JText::_('WHATSNEXT_YOU_WILL_RECEIVE_RESPONSE'); ?> 3 <?php echo JText::_('DAYS'); ?>.
				</p>
				<h4><?php echo JText::_('WHATSNEXT_REMAINING_STEPS'); ?>:</h4>
				<ul>
					<li class="complete">
						<?php echo JText::_('WHATSNEXT_REGISTER'); ?> <?php echo $hubShortName; ?>
					</li>
					<li class="complete">
						<?php echo JText::_('WHATSNEXT_UPLOAD_CODE'); ?> <?php echo $developer_site; ?>
					</li>
				<?php if ($this->status['resource_modified'] == '1') { ?>
					<li class="complete">
						<?php echo JText::_('TODO_MAKE_RES_PAGE'); ?>.
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resource&task=preview&app=' . $this->status['toolname']); ?>"><?php echo JText::_('PREVIEW'); ?></a> | 
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resource&step=1&app=' . $this->status['toolname']); ?>"><?php echo JText::_('TODO_EDIT_PAGE'); ?>...</a>
					</li>
				<?php } else { ?>
					<li class="todo">
						<?php echo JText::_('TODO_MAKE_RES_PAGE'); ?>.
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resource&step=1&app=' . $this->status['toolname']); ?>"><?php echo JText::_('TODO_CREATE_PAGE'); ?>...</a>
					</li>
				<?php } ?>
					<li class="incomplete">
						<?php echo JText::_('WHATSNEXT_TEST_AND_APPROVE'); ?>
					</li>
					<li class="incomplete">
						<?php echo JText::_('WHATSNEXT_PUBLISH'); ?> <?php echo $hubShortURL; ?>
					</li>
				</ul>
			<?php
				break;

				//  approved
				case 6:
			?>
				<p>
					<?php echo ucfirst(JText::_('THE')).' '.$hubShortName.' '.JText::_('WHATSNEXT_APPROVED_TEAM_WILL_FINALIZE').' '.JText::_('WHATSNEXT_IT_HAS_BEEN').' '.Hubzero_View_Helper_Html::timeAgo($this->status['changed']).' '.JText::_('WHATSNEXT_APPROVED_SINCE').'  '.JText::_('WHATSNEXT_APPROVED_WHAT_WILL_HAPPEN').' '.$toolaccess; ?>.
				</p>
				<p>
					<?php echo JText::_('WHATSNEXT_APPROVED_PLS_CLICK'); ?> <?php echo $hubShortName; ?>: <br />
					<a href="<?php echo JRoute::_('index.php?option=com_resources&alias=' . $this->status['toolname']); ?>"><?php echo JRoute::_('index.php?option=' . $this->option . '&app=' . $this->status['toolname']); ?></a>
				</p>
				<h4><?php echo JText::_('WHATSNEXT_REMAINING_STEPS'); ?>:</h4>
				<ul>
					<li class="complete">
						<?php echo JText::_('WHATSNEXT_REGISTER'); ?> <?php echo $hubShortName; ?>
					</li>
					<li class="complete">
						<?php echo JText::_('WHATSNEXT_UPLOAD_CODE'); ?> <?php echo $developer_site; ?>
					</li>
				<?php if ($this->status['resource_modified'] == '1') { ?>
					<li class="complete">
						<?php echo JText::_('TODO_MAKE_RES_PAGE'); ?>.
						<a href="<?php echo JRoute::_('index.php?option=com_resources&id=' . $this->status['resourceid'] . '&rev=dev'); ?>"><?php echo JText::_('PREVIEW'); ?></a> | 
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resource&step=1&app=' . $this->status['toolname']); ?>"><?php echo JText::_('TODO_EDIT_PAGE'); ?>...</a>
					</li>
				<?php } else { ?>
					<li class="todo">
						<?php echo JText::_('TODO_MAKE_RES_PAGE'); ?>.
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=resource&step=1&app=' . $this->status['toolname']); ?>"><?php echo JText::_('TODO_CREATE_PAGE'); ?>...</a>
					</li>
				<?php } ?>
					<li class="complete">
						<?php echo JText::_('WHATSNEXT_TEST_AND_APPROVE'); ?>
					</li>
					<li class="incomplete">
						<?php echo JText::_('WHATSNEXT_PUBLISH'); ?> <?php echo $hubShortURL; ?>
						<br /><span id="Updated"><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=update&newstate=Updated&app=' . $this->status['toolname']); ?>" class="flip" ><?php echo JText::_('WHATSNEXT_WAIT'); ?></a></span>
					</li>
				</ul>
			<?php
				break;

				//  published
				case 7:
			?>
				<p>
					<?php echo JText::_('WHATSNEXT_PUBLISHED_MSG'); ?>: <br />
					<a href="<?php echo JRoute::_('index.php?option=com_resources&alias=' . $this->status['toolname']); ?>"><?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&app=' . $this->status['toolname']); ?></a>
				</p>
				<h3><?php echo JText::_('WHATSNEXT_YOUR_OPTIONS'); ?>:</h3>
				<ul class="youroptions">
					<li>
						<?php echo JText::_('WHATSNEXT_CHANGES_MADE'); ?> 
						<span id="Updated"><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=update&newstate=Updated&app=' . $this->status['toolname']); ?>" class="flip"><?php echo JText::_('WHATSNEXT_PUBLISHED_PLS_INSTALL'); ?></a></span>
					</li>
				</ul>
			<?php
				break;

				//  retired
				case 8:
			?>
				<p>
					<?php echo JText::_('WHATSNEXT_RETIRED_FROM'); ?> <?php echo $hubShortURL; ?>. 
					<?php echo JText::_('CONTACT'); ?> <?php echo $hubShortName; ?> <?php echo JText::_('CONTACT_SUPPORT_TO_REPUBLISH'); ?>.
				</p>
				<h3><?php echo JText::_('WHATSNEXT_YOUR_OPTIONS'); ?>:</h3>
				<ul class="youroptions">
					<li>
						<?php echo JText::_('WHATSNEXT_RETIRED_WANT_REPUBLISH'); ?>. 
						<span id="Updated">
							<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=update&newstate=Updated&app=' . $this->status['toolname']); ?>" class="flip"><?php echo JText::_('WHATSNEXT_RETIRED_PLS_REPUBLISH'); ?></a>
						</span>
					</li>
				</ul>
			<?php
				break;

				//  abandoned
				case 9:
			?>
				<p>
					<?php echo JText::_('WHATSNEXT_ABANDONED_MSG'); ?> <?php echo $hubShortName; ?> <?php echo JText::_('WHATSNEXT_ABANDONED_CONTACT'); ?>.
				</p>
			<?php
				break;
			}
			?>
		</div><!-- / #whatsnext -->
	</div><!-- / .two columns second -->
	<div class="clear"></div>
</div><!-- / .main section -->