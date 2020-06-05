<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml\Macros;

use Plugins\Content\Formathtml\Macro;

/**
 * macro class for dipslaying a twitter feed
 */
class Twitter extends Macro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return  array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = "Embeds a Twitter Feed into the page";
		$txt['html'] = '<p>Embeds a Twitter Feed into the page. Can be a user feed(@hubzero), or a Twitter widget ID(see below), followed by a comma(,) and then the number of tweets to display.</p>
						<p>A Twitter widget may be set up by going to <a href="https://www.twitter.com/settings/widgets">Twitter\'s widget configuration page</a>.  After setting a widget up, the widget ID can be found as an 18 digit number in the URL of the settings, or within the HTML in the text box labeled "Copy and paste the code into the HTML of your site" as the item data-widget-id="####".</p>
						<p>Examples:</p>
						<ul>
							<li><code>[[Twitter(@hubzeroplatform,2)]]</code></li>
						</ul>
						<p>Displays:</p>
						<a class="twitter-timeline"
							href="https://twitter.com/"
							data-widget-id="346714310770302976"
							data-screen-name="hubzeroplatform"
							data-tweet-limit="2"
							data-chrome=""
							>Loading Tweets...</a>';

		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return  string
	 */
	public function render()
	{
		// get the args passed in
		$args = array_map('trim', explode(',', $this->args));

		// get screen name & num tweets
		$screenName = (isset($args[0])) ? ltrim($args[0], '@') : '';
		$widgetId   = (preg_match('/widgetid="([^"]*)"/', $this->args, $matches)) ? $matches[1] : '';
		$chrome     = (preg_match('/chrome="([^"]*)"/', $this->args, $matches)) ? $matches[1] : '';
		$width      = (preg_match('/width="([^"]*)"/', $this->args, $matches)) ? $matches[1] : '100%';
		$height     = (preg_match('/height="([^"]*)"/', $this->args, $matches)) ? $matches[1] : 500;

		// make sure we have a user name
		if ($screenName == '' && $widgetId == '' || strpos($screenName, '#') !== false)
		{
			return '(Please enter a valid Twitter Username/ID or Widget ID)';
		}

		// code for screename
		$atts   = array();
		$atts[] = 'data-widget-id="346714310770302976"';

		// twitter does not allow numeric usernames, this must be a widget id
		if (is_numeric($screenName))
		{
			$atts = array('data-widget-id="' . $screenName . '"');
			$screenName = '';
		}

		// pass already configured widget
		if ($widgetId)
		{
			$atts = array('data-widget-id="' . $widgetId . '"');
		}
		// no widget id, set up the screen name to show tweets from
		else
		{
			$atts[] = 'href="https://twitter.com/'. $screenName . '"';
			$atts[] = 'data-screen-name="' . $screenName . '"';
		}


		$atts[] = 'width="' . $width . '"';
		$atts[] = 'height="' . $height . '"';
		$atts[] = 'data-chrome="' . $chrome . '"';

		if (isset($args[1]) && is_numeric($args[1]))
		{
			$atts[] = 'data-tweet-limit="' . $args[1] . '"';
		}

		\Document::addScript(\Request::root() . 'core/plugins/content/formathtml/macros/macro-assets/twitter/twitter.js?t=' . filemtime(__DIR__ . '/macro-assets/twitter/twitter.js'));

		// output embeded timeline
		return '<a class="twitter-timeline" ' . implode(' ', $atts) . '>Loading Tweets...</a>';
	}
}
