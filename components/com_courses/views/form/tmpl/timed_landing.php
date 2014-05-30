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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<p>
	You have <strong><?php echo FormHelper::timeDiff($realLimit*60) ?></strong> to complete this form.
	There are <strong><?php echo $this->pdf->getQuestionCount() ?></strong> questions.
	<?php if ($this->dep->getAllowedAttempts() > 1) : ?>
		You are allowed <strong><?php echo $this->dep->getAllowedAttempts() ?></strong> attempts.
		This is your <strong><?php echo FormHelper::toOrdinal((int)$this->resp->getAttemptNumber()) ?></strong> attempt.
	<?php endif; ?>
</p>
<?php if ($realLimit == $limit): ?>
	<p><em>Time will begin counting when you click 'Continue' below.</em></p>
<?php else: ?>
	<p><em>Time is already running because the form is close to expiring!</em></p>
<?php endif; ?>
<form action="<?php echo JRoute::_($this->base); ?>" method="post">
	<fieldset>
		<input type="hidden" name="task" value="startWork" />
		<input type="hidden" name="crumb" value="<?php echo $this->dep->getCrumb() ?>" />
		<input type="hidden" name="attempt" value="<?php echo (int)$this->resp->getAttemptNumber() ?>" />
		<input type="hidden" name="controller" value="form" />
		<?php echo isset($_GET['tmpl']) ? '<input type="hidden" name="tmpl" value="'.str_replace('"', '&quot;', $_GET['tmpl']).'" />' : '' ?>
		<button type="submit">Continue</button>
	</fieldset>
</form>
