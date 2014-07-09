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

if (!$this->no_html)
{
	$this->css();
}
?>
<?php if ($this->no_html) { ?>
	<div id="report-response">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
		<?php } ?>
		<div>
			<p><?php echo JText::_('COM_SUPPORT_YOUR_TICKET'); ?> # <span><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=ticket&id=' . $this->ticket); ?>"><?php echo $this->ticket; ?></a></span></p>
			<p><button onclick="javascript:HUB.Modules.ReportProblems.resetForm();" title="<?php echo JText::_('COM_SUPPORT_NEW_REPORT'); ?>"><?php echo JText::_('COM_SUPPORT_NEW_REPORT'); ?></button></p>
		</div>
		<p>
			<?php echo JText::_('COM_SUPPORT_TROUBLE_THANKS'); ?><br /><br />
			<?php echo JText::_('COM_SUPPORT_TROUBLE_TICKET_TIMES'); ?>
		</p>
	</div>
	<script type="text/javascript">window.top.window.HUB.Modules.ReportProblems.hideTimer();</script>
<?php } else { ?>
	<section class="main section">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
		<?php } ?>
		<div class="grid">
			<div class="col span-half">
				<div id="ticket-number">
					<h2>
						<span><?php echo JText::sprintf('COM_SUPPORT_TICKET_NUMBER', ' '); ?></span><strong><?php echo $this->ticket; ?></strong>
					</h2>
				</div>
			</div>
			<div class="col span-half omega">
				<div id="messagebox">
					<div class="wrap">
						<h3><?php echo JText::_('COM_SUPPORT_TROUBLE_THANKS'); ?></h3>
						<p><?php echo JText::_('COM_SUPPORT_TROUBLE_TICKET_TIMES'); ?></p>
					<?php if ($this->ticket) { ?>
						<p><?php echo JText::sprintf('COM_SUPPORT_TROUBLE_TICKET_REFERENCE', $this->ticket); ?></p>
					<?php } ?>
					</div>
				</div>
			</div><!-- / .col span-half omega -->
		</div><!-- / .grid -->
	</section><!-- / .main section -->
<?php } ?>