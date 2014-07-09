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

$this->css()
     ->js();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<section id="import" class="section">
	<?php foreach ($this->messages as $message) { ?>
		<p class="<?php echo $message['type']; ?>"><?php echo $message['message']; ?></p>
	<?php } ?>

	<ul id="steps">
		<li><a href="<?php echo JURI::base(true); ?>/citations/import" class="active"><?php echo JText::_('COM_CITATIONS_IMPORT_STEP1'); ?><span><?php echo JText::_('COM_CITATIONS_IMPORT_STEP1_NAME'); ?></span></a></li>
		<li><a><?php echo JText::_('COM_CITATIONS_IMPORT_STEP2'); ?><span><?php echo JText::_('COM_CITATIONS_IMPORT_STEP2_NAME'); ?></span></a></li>
		<li><a><?php echo JText::_('COM_CITATIONS_IMPORT_STEP3'); ?><span><?php echo JText::_('COM_CITATIONS_IMPORT_STEP3_NAME'); ?></span></a></li>
	</ul><!-- / #steps -->

	<form id="hubForm" enctype="multipart/form-data" method="post" action="<?php echo JRoute::_('index.php?option='. $this->option . '&task=import_upload'); ?>">
		<p class="explaination">
			<strong><u><?php echo JText::_('COM_CITATIONS_IMPORT_ACCEPTABLE'); ?></u></strong><br />
			<?php echo implode($this->accepted_files, "<br />"); ?>
		</p>
		<fieldset>
			<legend><?php echo JText::_('COM_CITATIONS_IMPORT_UPLOAD'); ?>:</legend>
			<label><?php echo JText::_('COM_CITATIONS_IMPORT_UPLOAD_FILE'); ?>: <span class="required">Required</span>
				<input type="file" name="citations_file" />
				<span class="hint"><?php echo JText::_('COM_CITATIONS_IMPORT_UPLOAD_MAX'); ?></span>
			</label>
		</fieldset>

		<p class="submit">
			<input type="submit" name="submit" value="<?php echo JText::_('COM_CITATIONS_IMPORT_UPLOAD'); ?>" />
		</p>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="import_upload" />
	</form>
</section><!-- / .section -->
