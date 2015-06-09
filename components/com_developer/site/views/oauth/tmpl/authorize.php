<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_JEXEC') or die('Restricted access');

?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_DEVELOPER_API_OAUTH_AUTHORIZATION_NEEDED'); ?></h2>
</header>

<section class="main section">
	<div class="section-inner">
		<p><?php echo Lang::txt('COM_DEVELOPER_API_OAUTH_AUTHORIZATION_NEEDED_DESC', $this->application->get('name')); ?></p>
		<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" id="oauth_form" method="post">
			<fieldset class="buttons">
				<button type="submit" name="authorize" value="1" class="btn btn-success"><?php echo Lang::txt('Authorize'); ?></button>
				<button type="submit" name="authorize" value="0" class="btn btn-danger btn-secondary"><?php echo Lang::txt('No Thanks'); ?></button>
			</fieldset>
			<input type="hidden" name="option" value="com_developer" />
			<input type="hidden" name="controller" value="oauth" />
			<input type="hidden" name="task" value="doauthorize" />
			<input type="hidden" name="client_id" value="<?php echo $this->application->get('client_id'); ?>" />
			<input type="hidden" name="response_type" value="<?php echo $this->escape(Request::getWord('response_type', '')); ?>" />
			<input type="hidden" name="redirect_uri" value="<?php echo $this->escape(Request::getVar('redirect_uri', '')); ?>" />
			<input type="hidden" name="state" value="<?php echo $this->escape(Request::getCmd('state', '')); ?>" />
		</form>
	</div>
</section>