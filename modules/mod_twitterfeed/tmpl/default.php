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

// No direct access
defined('_JEXEC') or die('Restricted access');

//get tweet count
$count = $this->params->get('tweetcount', 5);
if (!is_numeric($count) || $count > 20 || $count < 1)
{
	$count = 5;
}

//get screen name
$screenName = ltrim($this->params->get('twitterID'), '@');

//get settings
$widgetSettings  = '';
$widgetSettings .= ($this->params->get('displayHeader') == 'no') ? ' noheader' : '';
$widgetSettings .= ($this->params->get('displayFooter') == 'no') ? ' nofooter' : '';
$widgetSettings .= ($this->params->get('displayBorders') == 'no') ? ' noborders' : '';
?>

<?php if ($this->params->get('moduleTitle', '') != '') : ?>
	<h3>
		<?php echo $this->params->get('moduleTitle'); ?>
	</h3>
<?php endif; ?>

<a class="twitter-timeline" 
	href="https://twitter.com/"
	data-widget-id="346714310770302976"
	data-screen-name="<?php echo $screenName; ?>"
	data-tweet-limit="<?php echo $count; ?>"
	data-chrome="<?php echo trim($widgetSettings); ?>"
	>Loading Tweets...</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>