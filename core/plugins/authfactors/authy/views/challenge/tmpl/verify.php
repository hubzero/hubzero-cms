<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

$this->css('enroll');
?>

<form class="authy" action="<?php echo Request::current(); ?>" method="POST">
	<div class="title">Authy</div>
	<div class="img-wrap">
		<img class="logo" src="/core/plugins/authfactors/authy/assets/img/authy_logo.svg" alt="authy logo" />
	</div>

	<div class="grouping">
		<label for="token">Token:</label>
		<input type="text" name="token" value="<?php echo User::get('token'); ?>" placeholder="1234567" />
	</div>

	<div class="grouping">
		<input type="hidden" name="action" value="verify" />
		<input type="submit" value="Submit" class="btn btn-success" />
	</div>
</form>