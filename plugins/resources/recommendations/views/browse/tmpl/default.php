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

$this->js();
?>
<div id="recommendations">
	<h3><?php echo JText::_('PLG_RESOURCES_RECOMMENDATIONS_HEADER'); ?></h3>
	<div class="subject" id="recommendations-subject" data-base="<?php echo JURI::base(true); ?>">
		<?php if ($this->results) { ?>
			<ul>
			<?php foreach ($this->results as $line) { ?>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&' . ($line->alias ? 'alias=' . $line->alias : 'id=' . $line->id) . '&rec_ref=' . $this->resource->id); ?>"><?php echo $this->escape(stripslashes($line->title)); ?></a>
				</li>
			<?php } ?>
			</ul>
		<?php } else { ?>
			<p><?php echo JText::_('PLG_RESOURCES_RECOMMENDATIONS_NO_RESULTS_FOUND'); ?></p>
		<?php } ?>

		<p id="credits">
			<a href="<?php echo JURI::base(true); ?>/about/hubzero#recommendations"><?php echo JText::_('PLG_RESOURCES_RECOMMENDATIONS_POWERED_BY'); ?></a>
		</p>
	</div>
	<div class="aside">
		<p><?php echo JText::_('PLG_RESOURCES_RECOMMENDATIONS_EXPLANATION'); ?></p>
	</div>
</div>
