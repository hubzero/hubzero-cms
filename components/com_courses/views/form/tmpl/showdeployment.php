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

$this->css('form.css')
     ->css('tablesorter.themes.blue.css', 'system')
     ->js('showdeployment.js')
     ->js('timepicker.js')
     ->js('deploy.js')
     ->js('jquery.tablesorter.min', 'system');
?>
<header id="content-header">
	<h2>Deployment: <?php echo $this->escape($this->title) ?></h2>
</header>

<?php $link = JRoute::_($this->base . '&task=form.complete&crumb=' . $this->dep->getCrumb()); ?>

<section class="main section courses-form">
	<p class="distribution-link">Link to distribute: <a href="<?php echo $link ?>"><?php echo $link ?></a><span class="state <?php echo $this->dep->getState() ?>"><?php echo $this->dep->getState() ?></span></p>
	<form action="<?php echo JRoute::_($this->base); ?>" method="post" id="deployment">
		<?php require 'deployment_form.php'; ?>
		<fieldset>
			<input type="hidden" name="controller" value="form" />
			<input type="hidden" name="task" value="updateDeployment" />
			<input type="hidden" name="formId" value="<?php echo $this->pdf->getId() ?>" />
			<input type="hidden" name="deploymentId" value="<?php echo $this->dep->getId() ?>" />
			<input type="hidden" name="id" value="<?php echo $this->dep->getId() ?>" />
			<?php if ($tmpl = JRequest::getWord('tmpl', false)): ?>
				<input type="hidden" name="tmpl" value="<?php echo $tmpl ?>" />
			<?php endif; ?>
			<div class="navbar">
				<div><a href="<?php echo JURI::base(true); ?>/courses/form" id="done">Done</a></div>
				<button type="submit">Update deployment</button>
			</div>
		</fieldset>
	</form>
</section>