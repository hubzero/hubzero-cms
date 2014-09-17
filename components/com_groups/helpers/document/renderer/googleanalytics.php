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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class GroupsHelperDocumentRendererGoogleAnalytics extends GroupsHelperDocumentRenderer
{
	/**
	 * Render content to group template
	 *
	 * @param    string
	 */
	public function render()
	{
		$js = '';

		// get the account
		$account = (isset($this->params->account) && $this->params->account != '') ? $this->params->account : null;

		// define tracker property name
		$name    = ($this->group) ? $this->group->get('cn') : 'newTracker';
		$name    = str_replace('-', '', $name);

		// if we have an account lets output
		if ($account !== null)
		{
			$js = "
				<script>
					setTimeout(function(){
						if (typeof ga == 'undefined')
						{
							console.log('manually adding ga');
							(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
								(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
								m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
							})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
						}
						
						ga('create', '" . $account . "', 'auto', {'name': '" . $name . "'});
						ga('" . $name . ".send', 'pageview');
					}, 200);
				</script>";
		}

		return $js;
	}
}