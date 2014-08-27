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

$yearFormat = '%Y';
$dateFormat = '%m/%d/%Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$yearFormat = 'Y';
	$dateFormat = 'm/d/Y';
	$tz = false;
}

$pubHelper 		= $this->pub->_helpers->pubHelper;
$htmlHelper 	= $this->pub->_helpers->htmlHelper;
$projectsHelper = $this->pub->_helpers->projectsHelper;

// Get hub config
$juri 	 = JURI::getInstance();
$jconfig = JFactory::getConfig();
$site 	 = $jconfig->getValue('config.live_site')
	? $jconfig->getValue('config.live_site')
	: trim(preg_replace('/\/administrator/', '', $juri->base()), DS);

$now = JFactory::getDate()->toSql();

// Get creator name
$profile = \Hubzero\User\Profile::getInstance($this->pub->created_by);
$creator = $profile->get('name') . ' (' . $profile->get('username') . ')';

// Version status
$status = $pubHelper->getPubStateProperty($this->pub, 'status');
$class 	= $pubHelper->getPubStateProperty($this->pub, 'class');

// Get block content
$blockcontent = $this->pub->_curationModel->parseBlock( 'edit' );
?>
<?php echo $this->project->provisioned == 1
			? $pubHelper->showPubTitleProvisioned( $this->pub, $this->route)
			: $pubHelper->showPubTitle( $this->pub, $this->route, $this->title); ?>

<?php
	// Draw status bar
	echo $this->pub->_curationModel->drawStatusBar();
?>
<div id="pub-body">
	<?php echo $blockcontent; ?>
 </div>
<p class="rightfloat">
	<a href="<?php echo JRoute::_('index.php?option=com_publications&id=' . $this->pub->id . '&v=' . $this->pub->version_number); ?>" class="public-page" rel="external" title="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VIEW_PUB_PAGE'); ?>"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VIEW_PUB_PAGE'); ?></a>
</p>
<script>
jQuery(document).ready(function($){
	HUB.ProjectPublicationsDraft.initialize();
});
</script>
