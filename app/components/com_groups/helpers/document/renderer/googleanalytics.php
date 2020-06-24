<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Helpers\Document\Renderer;

use Components\Groups\Helpers\Document\Renderer;

class GoogleAnalytics extends Renderer
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
