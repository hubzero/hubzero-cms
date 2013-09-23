<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

$outside = isset($this->outside) && $this->outside == 1 ? 1 : 0;
?>

<?php if($outside) 
{
	?>
	<div class="mysubmissions">
<?php	if($this->juser->get('guest'))
	{
		// Have user log in
		echo '<p class="noresults">' . JText::_('PLG_PROJECTS_PUBLICATIONS_PLEASE') . ' <a href="' . 
		JRoute::_('index.php?option=com_publications' . a . 'task=submit' . a . 'action=login') . '">'
		. JText::_('PLG_PROJECTS_PUBLICATIONS_LOGIN') . '</a> ' . JText::_('PLG_PROJECTS_PUBLICATIONS_TO_VIEW_SUBMISSIONS') 
		. '</p>';	
	}
	else {
		// Display submissions if any ?>
		<div id="mypub">
			<div class="columns three first second">
			<h3><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_STARTED_BY_ME'); ?>	
				<?php if($this->mypubs_count > count($this->mypubs)) { ?>
					<span class="rightfloat mini"><a href="<?php echo JRoute::_('index.php?option=com_publications'.a.'task=submit'.a.'limit=0'); ?>">&raquo; <?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VIEW_ALL') . ' ' 
					. $this->mypubs_count . ' ' . strtolower(JText::_('PUBLICATIONS')) ; ?></a></span>
				<?php } ?></h3>
			<?php if(count($this->mypubs) > 0 ) { ?>
				<ul class="mypubs">
					<?php foreach($this->mypubs as $row) { 
						// Normalize type title
						$cls = str_replace(' ', '', $row->cat_alias);
						
						$route = $row->project_provisioned 
									? 'index.php?option=com_publications' . a . 'task=submit'
									: 'index.php?option=com_projects' . a . 'alias=' . $row->project_alias . a . 'active=publications';
						$url = JRoute::_($route . a . 'pid=' . $row->id);
						$preview = 	JRoute::_('index.php?option=com_publications'.a.'id='.$row->id);
						
						$status = PublicationHelper::getPubStateProperty($row, 'status', 0);
						$class = PublicationHelper::getPubStateProperty($row, 'class');						
					?>
					<li>
						<span class="mypub-options">
							<a href="<?php echo $preview; ?>" title="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VIEW_TITLE'); ?>"><?php echo strtolower(JText::_('PLG_PROJECTS_PUBLICATIONS_VIEW')); ?></a> |
							<a href="<?php echo $url; ?>" title="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_MANAGE_TITLE'); ?>"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_MANAGE'); ?></a>
						</span>
						<span class="mypub-status"><span class="<?php echo $class; ?> major_status"><?php echo $status; ?></span></span>
						<span class="mypub-version"><?php if($row->dev_version_label && $row->dev_version_label != $row->version_label) 
						{ echo '<span class="mypub-newversion"><a href="' . $url . '/?version=dev'
						. '">v.' . $row->dev_version_label . '</a> '
						. JText::_('PLG_PROJECTS_PUBLICATIONS_IN_PROGRESS') . '</span> '; } ?> v.<?php echo $row->version_label; ?></span>
						<span class="restype"><span class="<?php echo $cls; ?>"></span></span>
						<?php echo Hubzero_View_Helper_Html::shortenText(stripslashes($row->title), 80, 0); ?>
						<?php if($row->project_provisioned == 0) { echo '<span class="mypub-project">' 
						. JText::_('PLG_PROJECTS_PUBLICATIONS_IN_PROJECT') . ' <a href="' 
						. JRoute::_('index.php?option=com_projects' . a . 'alias=' . $row->project_alias) . '">'
						. Hubzero_View_Helper_Html::shortenText(stripslashes($row->project_title), 80, 0) . '</a>' . '</span>'; } ?>
					</li>	
					<?php }?>
				</ul>
			<?php } else { 
				echo ('<p class="noresults">'.JText::_('PLG_PROJECTS_PUBLICATIONS_NO_RELEVANT_PUBS_FOUND').'</a></p>'); } ?>
			<h3><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_COAUTHORED'); ?>	
					<?php if($this->coauthored_count > count($this->coauthored)) { ?>
						<span class="rightfloat mini"><a href="<?php echo JRoute::_('index.php?option=com_publications'.a.'task=submit'.a.'limit=0'); ?>">&raquo; <?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VIEW_ALL') . ' ' 
						. $this->coauthored_count . ' ' . strtolower(JText::_('PUBLICATIONS')) ; ?></a></span>
					<?php } ?></h3>
			<?php if(count($this->coauthored) > 0 ) { ?>
					<ul class="mypubs">
						<?php foreach($this->coauthored as $row) { 
							// Normalize type title
							$cls = str_replace(' ', '', $row->cat_alias);

							$route = $row->project_provisioned 
										? 'index.php?option=com_publications' . a . 'task=submit'
										: 'index.php?option=com_projects' . a . 'alias=' . $row->project_alias . a . 'active=publications';
							$url = JRoute::_($route . a . 'pid=' . $row->id);
							$preview = 	JRoute::_('index.php?option=com_publications'.a.'id='.$row->id);

							$status = PublicationHelper::getPubStateProperty($row, 'status', 0);
							$class = PublicationHelper::getPubStateProperty($row, 'class');	
							
							// Check team role	
							$pOwner = new ProjectOwner( $this->database );
							$owner = $pOwner->isOwner($this->uid, $row->project_id); 								
						?>
						<li>
							<span class="mypub-options">
								<a href="<?php echo $preview; ?>" title="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VIEW_TITLE'); ?>"><?php echo strtolower(JText::_('PLG_PROJECTS_PUBLICATIONS_VIEW')); ?></a> <?php if($owner != 3) { ?> |
								<a href="<?php echo $url; ?>" title="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_MANAGE_TITLE'); ?>"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_MANAGE'); ?></a><?php } ?>
							</span>
							<span class="mypub-status"><span class="<?php echo $class; ?> major_status"><?php echo $status; ?></span></span>
							<span class="mypub-version"><?php if($row->dev_version_label && $row->dev_version_label != $row->version_label) 
							{ echo '<span class="mypub-newversion"><a href="' . $url . '/?version=dev'
							. '">v.' . $row->dev_version_label . '</a> '
							. JText::_('PLG_PROJECTS_PUBLICATIONS_IN_PROGRESS') . '</span> '; } ?> v.<?php echo $row->version_label; ?></span>
							<span class="restype"><span class="<?php echo $cls; ?>"></span></span>
							<?php echo Hubzero_View_Helper_Html::shortenText(stripslashes($row->title), 80, 0); ?>
							<?php if($row->project_provisioned == 0) { echo '<span class="mypub-project">' 
							. JText::_('PLG_PROJECTS_PUBLICATIONS_IN_PROJECT') . ' <a href="' 
							. JRoute::_('index.php?option=com_projects' . a . 'alias=' . $row->project_alias) . '">'
							. Hubzero_View_Helper_Html::shortenText(stripslashes($row->project_title), 80, 0) . '</a>' . '</span>'; } ?>
						</li>	
						<?php }?>
					</ul>
			<?php } else { 
				echo ('<p class="noresults">'.JText::_('PLG_PROJECTS_PUBLICATIONS_NO_RELEVANT_PUBS_FOUND').'</a></span></p>'); } ?>	
			</div>
			<div class="columns three third">
				<div id="contrib-start">
					<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CONTRIB_START'); ?></p>
					<p class="getstarted-links"><a href="/members/myaccount/projects"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VIEW_YOUR_PROJECTS'); ?></a> | <span class="addnew"><a href="/projects/start"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_START_PROJECT'); ?></a></span></p>
					<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CONTRIB_START_INDEPENDENT'); ?></p>
					<p id="getstarted"><a href="<?php echo JRoute::_('index.php?option=com_publications'.a.'task=start'); ?>"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_GET_STARTED'); ?> &raquo;</a></p>
				</div>
			</div>
			<div class="clear"></div>
		</div>
<?php } ?>
	</div>
	<?php if($this->pubconfig->get('documentation')) { ?>
	<p class="rightfloat mini"><a href="<?php echo $this->pubconfig->get('documentation'); ?>"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_LEARN_MORE'); ?> &raquo;</a></p>
	<?php } ?>
<?php } ?>
<div id="pubintro">
	<h3><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_HOW_IT_WORKS'); ?></h3>
	<div class="columns three first">
		<h4><span class="num">1</span> <?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_INTRO_STEP_ONE'); ?></h4>
		<p><?php echo $outside 
						? JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_INTRO_STEP_ONE_ABOUT_OUTSIDE')
						: JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_INTRO_STEP_ONE_ABOUT'); ?></p>
	</div>
	<div class="columns three second">
		<h4><span class="num">2</span> <?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_INTRO_STEP_TWO'); ?></h4>
		<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_INTRO_STEP_TWO_ABOUT'); ?></p>
	</div>
	<div class="columns three third">
		<h4><span class="num">3</span> <?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_INTRO_STEP_THREE'); ?></h4>
		<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_PUB_INTRO_STEP_THREE_ABOUT'); ?></p>
	</div>
	<div class="clear"></div>
</div>