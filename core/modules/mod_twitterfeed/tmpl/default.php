<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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

$this->js();
?>
<div class="<?php echo $this->module->module; ?> <?php echo $this->params->get('moduleclass_sfx', ''); ?>">
	<?php if ($this->params->get('moduleTitle', '') != '') : ?>
		<h3>
			<?php echo $this->params->get('moduleTitle'); ?>
		</h3>
	<?php endif; ?>

	<a class="twitter-timeline" href="https://twitter.com/"
		data-widget-id="346714310770302976"
		data-screen-name="<?php echo $screenName; ?>"
		data-tweet-limit="<?php echo $count; ?>"
		data-chrome="<?php echo trim($widgetSettings); ?>"><?php echo Lang::txt('MOD_TWITTERFEED_LOADING'); ?></a>
</div>