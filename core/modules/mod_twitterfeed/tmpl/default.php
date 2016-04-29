<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Get tweet count
$count = $this->params->get('tweetcount', 5);
if (!is_numeric($count) || $count > 20 || $count < 1)
{
	$count = 5;
}

// Get screen name
$screenName = ltrim($this->params->get('twitterID'), '@');

// Get settings
$widgetSettings  = '';
$widgetSettings .= ($this->params->get('displayHeader') == 'no') ? ' noheader' : '';
$widgetSettings .= ($this->params->get('displayFooter') == 'no') ? ' nofooter' : '';
$widgetSettings .= ($this->params->get('displayBorders') == 'no') ? ' noborders' : '';
?>
<div class="<?php echo $this->module->module; ?> <?php echo $this->params->get('moduleclass_sfx', ''); ?>">
	<?php if ($this->params->get('moduleTitle', '') != '') : ?>
		<h3>
			<?php echo $this->params->get('moduleTitle'); ?>
		</h3>
	<?php endif; ?>

	<a class="twitter-timeline" href="https://twitter.com/" data-widget-id="346714310770302976" data-screen-name="<?php echo $screenName; ?>" data-tweet-limit="<?php echo $count; ?>" data-chrome="<?php echo trim($widgetSettings); ?>"><?php echo Lang::txt('MOD_TWITTERFEED_LOADING'); ?></a>
	<script type="text/javascript">!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
</div>