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

$base = rtrim(JURI::base(true), '/');

$cls = array();
if ($this->app->params->get('noResize', 0))
{
	$cls[] = 'no-resize';
}
if ($this->app->params->get('noPopout', 0))
{
	$cls[] = 'no-popout';
}
if ($this->app->params->get('noPopoutClose', 0))
{
	$cls[] = 'no-popout-close';
}
if ($this->app->params->get('noPopoutMaximize', 0))
{
	$cls[] = 'no-popout-maximize';
}
if ($this->app->params->get('noRefresh', 0))
{
	$cls[] = 'no-refresh';
}
?>
<div id="theapp" class="thisapp<?php if (!empty($cls)) { echo ' ' . implode(' ', $cls); } ?>" data-width="<?php echo $this->output->width; ?>" data-height="<?php echo $this->output->height; ?>">
	<p class="error">
		<?php echo JText::_('COM_TOOLS_ERROR_JAVASCRIPT_REQUIRED'); ?>
	</p>
</div>
<script type="text/javascript">
	HUB.Mw.startAppletTimeout();
	HUB.Mw.connectingTool();
</script>
