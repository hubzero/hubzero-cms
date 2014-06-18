<?php
/**
 * HUBzero CMS
 *
 * Copyright 2012 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.environment.request');
?>

<form action="<?php echo $this->form_action;?>" id="oauth_form" method="post">

 	<input id="oauth_token" name="oauth_token" type="hidden" value="<?php echo $this->oauth_token;?>" />

	<fieldset class="sign-in">
	  	<legend>Sign in to HUBzero</legend>
	  	<div class="row user">
	    	<label for="username" tabindex="-1">Username</label>
	    	<input aria-required="true" autocapitalize="off" autocorrect="off" autofocus="autofocus" class="text" id="username" name="username" required="required" type="text" />
	  	</div>
	  	<div class="row password">
	  		<label for="password" tabindex="-1">Password</label>
	    	<input aria-required="true" class="password text" id="password" name="password" required="required" type="password" value="" />
	  	</div>
	</fieldset>

	<fieldset class="buttons">
    	<legend>Authorize access to use your account?</legend>
      	<input type="submit" value="Authorize app" class="submit button selected" id="allow">
      	<input type="submit" value="No, thanks" class="submit button" name="deny" id="deny">
    </fieldset>
</form>