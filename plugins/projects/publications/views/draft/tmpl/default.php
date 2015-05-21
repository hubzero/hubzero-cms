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

// Get creator name
$creator = $this->pub->creator('name') . ' (' . $this->pub->creator('username') . ')';

// Version status
$status = $this->pub->getStatusName();
$class  = $this->pub->getStatusCss();

// Get block content
$blockcontent = $this->pub->_curationModel->parseBlock('edit');
?>
<?php 
// Write title
echo \Components\Publications\Helpers\Html::showPubTitle( $this->pub, $this->title);

// Draw status bar
echo $this->pub->_curationModel->drawStatusBar();
?>
<div id="pub-body">
	<?php echo $blockcontent; ?>
 </div>
<p class="rightfloat">
	<a href="<?php echo Route::url($this->pub->link('version')); ?>" class="public-page" rel="external" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_PUB_PAGE'); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_PUB_PAGE'); ?></a>
</p>
<script>
jQuery(document).ready(function($){
	HUB.ProjectPublicationsDraft.initialize();
});
</script>
