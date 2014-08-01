<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$pdf    = $this->pdf;
$resp   = $this->resp;
$dep    = $this->dep;
$record = $resp->getAnswers();
?>

<header id="content-header">
	<h2>Results: <?php echo $this->title ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-prev back btn" href="<?php echo JRoute::_($this->base); ?>">
				<?php echo JText::_('Back to course'); ?>
			</a>
		</p>
	</div>
</header>

<section class="main section">
	<p>Completed <?php echo JHTML::_('date', $resp->getEndTime(), 'r'); ?></p>
	<p>Score <strong><?php echo $record['summary']['score'] ?>%</strong></p>
	<?php if ($this->dep->getResultsClosed() == 'details'): ?>
		<p>More detailed results will be available <?php echo ($dep->getEndTime()) ? JHTML::_('date', $dep->getEndTime(), 'r') . " (about " . FormHelper::timeDiff(strtotime($this->dep->getEndTime()) - strtotime(JFactory::getDate())) . " from now)" : 'soon'; ?>. Check the course progress page for more details.</p>
	<?php endif; ?>
	<?php if ($this->dep->getAllowedAttempts() > 1) : ?>
		<?php $attempt = $resp->getAttemptNumber(); ?>
		You are allowed <strong><?php echo $this->dep->getAllowedAttempts() ?></strong> attempts.
		This was your <strong><?php echo FormHelper::toOrdinal((int)$attempt) ?></strong> attempt.
		<form action="<?php echo JRoute::_($this->base . '&task=form.complete') ?>">
			<input type="hidden" name="crumb" value="<?php echo $this->dep->getCrumb() ?>" />
			View another attempt:
			<select name="attempt">
				<?php for ($i = 1; $i <= $this->dep->getAllowedAttempts(); $i++) { ?>
					<?php
						if ($i == $attempt) :
							continue;
						endif;
					?>
					<option value="<?php echo $i ?>"><?php echo FormHelper::toOrdinal($i) ?> attempt</option>
				<?php } ?>
				?>
			</select>
			<input class="btn btn-secondary" type="submit" value="GO" />
		</form>
	<?php endif; ?>
</section>