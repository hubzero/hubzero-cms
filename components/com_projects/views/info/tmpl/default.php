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

$dateFormat = '%b %d, %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'M d, Y';
	$tz = false;
}

$view = $this->info;
$goto = $this->goto;

$view->project->about = rtrim(stripslashes(ProjectsHtml::cleanText($view->project->about)));

// Transform the wikitext to HTML
ximport('Hubzero_Wiki_Parser');
$p =& Hubzero_Wiki_Parser::getInstance();

//import the wiki parser
$wikiconfig = array(
	'option'   => $view->option,
	'scope'    => $view->project->alias.DS.'notes',
	'pagename' => 'projects',
	'pageid'   => $view->project->id,
	'filepath' => $view->config->get('webpath'),
	'domain'   => $view->project->alias
);

$view->project->about = $p->parse( $view->project->about, $wikiconfig );
$privacy = $view->project->private ? JText::_('COM_PROJECTS_PRIVATE') : JText::_('COM_PROJECTS_PUBLIC');

?>
<div id="plg-header">
	<h3 class="inform"><?php echo JText::_('COM_PROJECTS_PROJECT_INFO'); ?></h3>
</div>
<?php if ($view->project->role == 1 ) { ?> 	
<p class="editing"><a href="<?php echo JRoute::_('index.php?option=' . $view->option . a . 'task=edit' . a . $goto).'/?edit=info'; ?>"><?php echo JText::_('COM_PROJECTS_EDIT_PROJECT'); ?></a></p>
<?php } ?>

<div id="basic_info">
	<table id="infotbl">
		<tbody>
			<tr>
				<td class="htd"><?php echo JText::_('COM_PROJECTS_TITLE'); ?></td>
				<td><?php echo $view->project->title; ?></td>
				<?php if($view->config->get('grantinfo', 0) && $view->params->get( 'grant_title')) { ?>				
					<td rowspan="5" class="grantinfo">
						<h4><?php echo JText::_('COM_PROJECTS_INFO_GRANTINFO'); ?></h4>
						<p>
							<span class="block"><span class="faded"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_TITLE'); ?>:</span> <?php echo $view->params->get( 'grant_title'); ?></span>
							<span class="block"><span class="faded"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_PI'); ?>:</span> <?php echo $view->params->get( 'grant_PI', 'N/A'); ?></span>
							<span class="block"><span class="faded"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_AGENCY'); ?>:</span> <?php echo $view->params->get( 'grant_agency', 'N/A'); ?></span>
							<span class="block"><span class="faded"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_GRANT_BUDGET'); ?>:</span> <?php echo $view->params->get( 'grant_budget', 'N/A'); ?></span>
							<?php if($view->project->role == 1) { ?>
								<a href="<?php echo JRoute::_('index.php?option=' . $view->option . a . 'task=edit' . a . $goto).'/?edit=settings'; ?>"><?php echo JText::_('COM_PROJECTS_EDIT_THIS'); ?></a>
							<?php } ?>
						</p>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<td class="htd"><?php echo JText::_('COM_PROJECTS_ALIAS'); ?></td>
				<td><?php echo $view->project->alias; ?></td>
			</tr>
			<tr>
				<td class="htd"><?php echo JText::_('COM_PROJECTS_TYPE'); ?></td>
				<td><?php echo $view->project->projecttype; ?></td>
			</tr>
			<?php if($view->project->about) { ?>
			<tr>
				<td class="htd"><?php echo JText::_('COM_PROJECTS_ABOUT'); ?></td>
				<td><?php echo $view->project->about; ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td class="htd"><?php echo JText::_('COM_PROJECTS_ACCESS'); ?></td>
				<td><p><?php echo $privacy; ?> <?php if(!$view->project->private) { ?><span class="mini faded">[<a href="<?php echo JRoute::_('index.php?option=' . $view->option . a . $goto . a . 'preview=1'); ?>"><?php echo JText::_('COM_PROJECTS_PREVIEW_PUBLIC_PROFILE'); ?></a>]</span><?php } ?></p></td>
			</tr>
			<tr>
				<td class="htd"><?php echo JText::_('COM_PROJECTS_CREATED'); ?></td>
				<td><?php echo JHTML::_('date', $view->project->created, $dateFormat, $tz); ?></td>
			</tr>
		</tbody>
	</table>
</div><!-- / .basic info -->
