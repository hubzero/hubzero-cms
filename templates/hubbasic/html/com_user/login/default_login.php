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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// @FIXME: most of this shouldn't go here, but this is where Nick wants it for now...

// Get and add the js and extra css to the page
$document    = &JFactory::getDocument();
$app         = JFactory::getApplication();
$template    = DS."templates".DS.$app->getTemplate().DS."html".DS."com_user";
$media       = DS."media".DS."system";
$js          = $template.DS."login.jquery.js";
$css         = $template.DS."login.css";
$uniform_js  = $media.DS."js".DS."jquery.uniform.js";
$uniform_css = $media.DS."css".DS."uniform.css";
if(file_exists(JPATH_BASE . $js))
{
	$document->addScript($js);
}
if(file_exists(JPATH_BASE . $css))
{
	$document->addStyleSheet($css);
}
if(file_exists(JPATH_BASE . $uniform_js))
{
	$document->addScript($uniform_js);
}
if(file_exists(JPATH_BASE . $uniform_css))
{
	$document->addStyleSheet($uniform_css);
}

// If we have a return set with an authenticator in it, we're linking an existing account
// Parse the return to retrive the authenticator, and remove it from the list below
if($return = JRequest::getVar('return', null))
{
	$return = base64_decode($return);
	$query  = parse_url($return);
	$query  = $query['query'];
	$query  = explode('&', $query);
	$auth   = '';
	foreach($query as $q)
	{
		$n = explode('=', $q);
		if($n[0] == 'authenticator')
		{
			$auth = $n[1];
		}
	}
}

// Figure out whether or not any of our third party auth plugins are turned on 
// Don't include the 'hubzero' plugin, or the $auth plugin as described above
$multiAuth      = false;
$plugins        = JPluginHelper::getPlugin('authentication');
$authenticators = array();

foreach($plugins as $p)
{
	if($p->name != 'hubzero' && $p->name != $auth)
	{
		$authenticators[] = $p->name;
		$multiAuth = true;
	}
}

// Override $multiAuth if authenticator is set to hubzero
if(JRequest::getWord('authenticator') == 'hubzero')
{
	$multiAuth = false;
}

// Set the return if we have it...
$return = (base64_decode($this->return) != '/members/myaccount') ? "&return={$this->return}" : '';

// Check for error messages (regular message queue)
if (!empty($error_message))
{
	echo '<p class="error">'. $error_message . '</p>';
}

?>

<div id="authentication" class="<?php echo ($multiAuth) ? 'multiAuth' : 'singleAuth'; ?>">
	<div id="error-response"></div>
	<div id="inner" class="<?php echo ($multiAuth) ? 'multiAuth' : 'singleAuth'; ?>">
		<?php if($multiAuth) { // only display if we have third part auth plugins enabled ?>
			<div id="providers" class="two columns first">
				<h3>Sign in with your:</h2>
				<ul>
					<?php 
						foreach($authenticators as $a)
						{
					?>
							<li id="<?php echo $a; ?>" class="entry">
								<a id="<?php echo $a; ?>-button" class="" href="<?php echo JRoute::_('index.php?option=com_user&view=login&authenticator=' . $a . $return); ?>"></a>
							</li>
					<?php
						}
					?>
				</ul>
			</div>
		<?php } // close if - check if any authentication plugins are enabled ?>
		<div id="credentials-hub" class="<?php echo ($multiAuth) ? 'two columns second' : 'singleAuth'; ?>">
			<div id="credentials-hub-inner">
				<h3><?php echo ($multiAuth) ? 'Your local hub account:' : 'Sign In:'; ?></h2>
				<form action="<?php echo JRoute::_('index.php', true, $this->params->get('usesecure')); ?>" method="post" id="login_form">
					<div class="labelInputPair">
						<label for="username"><?php echo JText::_('Username'); ?>:</label>
						<a class="forgots" href="<?php echo JRoute::_('index.php?option=com_user&view=remind'); ?>"><?php echo JText::_('Lost username?');?></a>
						<input tabindex="1" type="text" name="username" id="username" placeholder="username<?php //echo JText::_('username'); ?>" />
					</div>
					<div class="labelInputPair">
						<label for="password"><?php echo JText::_('Password'); ?>:</label>
						<a class="forgots" href="<?php echo JRoute::_('index.php?option=com_user&view=reset'); ?>"><?php echo JText::_('Forgot password?'); ?></a>
						<input tabindex="2" type="password" name="passwd" id="password" placeholder="password<?php //echo JText::_('password'); ?>" />
					</div>
					<div class="submission">
					<?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
						<input type="checkbox" class="option" name="remember" id="remember" value="yes" alt="Remember Me" checked="checked" />
						<label for="remember" id="remember-me-label"><?php echo JText::_('Keep me logged in?'); ?></label>
					<?php endif; ?>
					<input type="submit" value="Login" id="login-submit"/>
					</div>
					<div class="clear"></div>
					<input type="hidden" name="option" value="com_user" />
					<input type="hidden" name="task" value="login" />
					<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
					<input type="hidden" name="freturn" value="<?php echo $this->freturn; ?>" />
					<?php echo JHTML::_('form.token'); ?>
				</form>
			</div>
		</div>
		<?php if(!$multiAuth) { ?>
			<p class="callToAction">Don't have an account? <a href="/register">Create one.</a></p>
		<?php } ?>
	</div>
	<div class="clear"></div>
	<?php if($multiAuth) { ?>
		<p class="callToAction">Or, you can <a href="/register">create a local account.</a></p>
	<?php } ?>
</div>