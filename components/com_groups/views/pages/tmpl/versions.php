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

$editPageUrl = 'index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=edit&pageid='.$this->page->get('id');

// add page stylesheets
$stylesheets = GroupsHelperView::getPageCss($this->group);
$doc = JFactory::getDocument();
foreach ($stylesheets as $stylesheet)
{
	$doc->addStylesheet($stylesheet);
}

// add styles & scripts
$this->css()
	 ->js()
	 ->js('jquery.cycle2', 'system');
?>
<header id="content-header">
	<h2><?php echo JText::sprintf('COM_GROUPS_PAGES_VERSIONS_FOR_PAGE', $this->page->get('title')); ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li><a class="icon-edit edit btn" href="<?php echo JRoute::_($editPageUrl); ?>">
				<?php echo JText::_('COM_GROUPS_PAGES_EDIT_PAGE_BACK'); ?>
			</a></li>
		</ul>
	</div>
</header>

<section class="main section">
	<div class="version-manager">
		<div class="toolbar grid">
			<div class="col span6 title">
				<div class="btn-group">
					<h3 class="btn version-title"></h3>
					<a class="btn version-source" href="javascript:void(0);"><?php echo JText::_('COM_GROUPS_PAGES_VERSIONS_VIEW_SOURCE'); ?></a>
				</div>
				<a class="btn version-meta" title="<?php echo JText::_('COM_GROUPS_PAGES_VERSIONS_TOGGLE_METADATA'); ?>" href="javascript:void(0);">&hellip;</a>
			</div>
			<div class="col span6 omega controls">
				<div class="btn-group">
					<a href="javascript:void(0);" class="btn icon-prev version-prev">
						<?php echo JText::_('COM_GROUPS_PAGES_VERSIONS_PREVIOUS'); ?>
					</a>
					<span class="version-jumpto-container">
						<select class="btn version-jumpto icon-prev">
							<?php foreach ($this->page->versions() as $version) :?>
								<option value="<?php echo $version->get('version'); ?>"><?php echo $version->get('version'); ?></option>
							<?php endforeach; ?>
						</select>
					</span>
					<a href="javascript:void(0);" class="btn icon-next opposite version-next">
						<?php echo JText::_('COM_GROUPS_PAGES_VERSIONS_NEXT'); ?>
					</a>
				</div>
				<a href="javascript:void(0);" class="btn btn-info version-restore">
					<?php echo JText::_('COM_GROUPS_PAGES_VERSIONS_RESTORE'); ?>
				</a>
			</div>
		</div>
		
		<div class="content">
			<div class="versions">
				<?php foreach ($this->page->versions()->reverse() as $k => $pageVersion) : ?>
					<?php $cls = ($k+1 == $this->page->versions()->count()) ? ' current' : ''; ?>
					<div class="version <?php echo $cls; ?>" 
						data-cycle-hash="v<?php echo $pageVersion->get('version'); ?>" 
						data-cycle-title="Version # <?php echo $pageVersion->get('version'); ?>"
						data-raw-url="<?php echo $pageVersion->url('raw'); ?>"
						data-restore-url="<?php echo ($k+1 != $this->page->versions()->count()) ? $pageVersion->url('restore') : null; ?>">

						<?php
							$created = JText::_('COM_GROUPS_PAGES_PAGE_NA');
							if ($pageVersion->get('created') != null)
							{
								$created = JHTML::_('date', $pageVersion->get('created'), 'F d, Y @ g:ia');
							}

							$created_by = JText::_('COM_GROUPS_PAGES_PAGE_NA');
							if ($pageVersion->get('created_by') == 1000)
							{
								$created_by = JText::_('COM_GROUPS_PAGES_PAGE_SYSTEM');
							}
							else if ($pageVersion->get('created_by') != null && is_numeric($pageVersion->get('created_by')))
							{
								$profile = \Hubzero\User\Profile::getInstance( $pageVersion->get('created_by') );
								$created_by = '<a href="'.JRoute::_('index.php?option=com_members&id=' . $profile->get('uidNumber')).'">'.$profile->get('name').'</a>';
							}

							$approved_on = JText::_('COM_GROUPS_PAGES_PAGE_NA');
							if ($pageVersion->get('approved_on') != null)
							{
								$approved_on = JHTML::_('date', $pageVersion->get('approved_on'), 'F d, Y @ g:ia');
							}

							$approved_by = JText::_('COM_GROUPS_PAGES_PAGE_NA');
							if ($pageVersion->get('approved_by') == 1000)
							{
								$approved_by = JText::_('COM_GROUPS_PAGES_PAGE_SYSTEM');
							}
							else if ($pageVersion->get('approved_by') != null && is_numeric($pageVersion->get('approved_by')))
							{
								$profile = \Hubzero\User\Profile::getInstance( $pageVersion->get('approved_by') );
								$approved_by = '<a href="'.JRoute::_('index.php?option=com_members&id=' . $profile->get('uidNumber')).'">'.$profile->get('name').'</a>';
							}
						?>
						<div class="grid version-metadata">
							<div class="col span3">
								<span><?php echo JText::_('COM_GROUPS_PAGES_VERSIONS_CREATED'); ?></span>
								<?php echo $created; ?></span>
							</div>
							<div class="col span3">
								<span><?php echo JText::_('COM_GROUPS_PAGES_VERSIONS_CREATED_BY'); ?></span>
								<?php if ($created_by != 'n/a' && $created_by != 'System') : ?>
									<img align="left" width="20" src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($profile->get('uidNumber')); ?>" />
								<?php endif; ?>
								<?php echo $created_by; ?>
							</div>
							<div class="col span3">
								<span><?php echo JText::_('COM_GROUPS_PAGES_VERSIONS_APPROVED'); ?></span>
								<?php echo $approved_on; ?>
							</div>
							<div class="col span3 omega">
								<span><?php echo JText::_('COM_GROUPS_PAGES_VERSIONS_APPROVED_BY'); ?></span>
								<?php if ($approved_by != 'n/a' && $approved_by != 'System') : ?>
									<img align="left" width="20" src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($profile->get('uidNumber')); ?>" />
								<?php endif; ?>
								<?php echo $approved_by; ?>
							</div>
						</div>
						<div class="version-content">
							<?php echo GroupsHelperPages::generatePreview($this->page, $pageVersion->get('version'), true); ?>
						</div>
						<div class="version-code">
							<?php
								$current = explode("\n", $pageVersion->content('raw'));
								$previousVersion = $pageVersion->get('version') - 1;
								if ($previousVersion == 0)
								{
									$previous = array();
								}
								else
								{
									$previous = $this->page->version($previousVersion);
									$previous = explode("\n", $previous->content('raw'));
								}

								// define function to format context's
								// basically make sure lines that are not different 
								// are outputted as code not rendered html
								$contextFormatter = function($context)
								{
									return htmlentities($context);
								};

								// out formatted diff table
								$formatter = new TableDiffFormatter();
								$diff = $formatter->format(new Diff($previous, $current), $contextFormatter);
								echo $diff;
							?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>