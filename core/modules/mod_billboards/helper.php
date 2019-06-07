<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Billboards;

use Hubzero\Module\Module;
use Component;
use Components\Billboards\Models\Billboard;

require_once Component::path('com_billboards') . DS . 'models' . DS . 'billboard.php';

/**
 * Module helper class, used to query for billboards and contains the display method
 */
class Helper extends Module
{
	/**
	 * Tracker for number of instances
	 *
	 * @var integer
	 */
	public static $multiple_instances = 0;

	/**
	 * Get the list of billboads in the selected collection
	 *
	 * @return array
	 */
	private function getList()
	{
		// Get the correct billboards collection to display from the parameters
		$collection = (int) $this->params->get('collection', 1);

		// Grab all the buildboards associated with the selected collection
		// Make sure we only grab published billboards
		$rows = Billboard::whereEquals('published', 1)
		                 ->whereEquals('collection_id', $collection)
		                 ->order('ordering', 'asc')
		                 ->rows();

		return $rows;
	}

	/**
	 * Display method
	 *
	 * Used to add CSS for each slide as well as the javascript file(s) and the parameterized function
	 *
	 * @return void
	 */
	public function display()
	{
		// Check if we have multiple instances of the module running
		// If so, we only want to push the CSS and JS to the template once
		if (!self::$multiple_instances)
		{
			// Push some CSS to the template
			$this->css();
			$this->js();
		}
		self::$multiple_instances++;

		// Get the billboard slides
		$this->slides = $this->getList();

		// Get some parameters
		$transition       = $this->params->get('transition', 'scrollHorz');
		$random           = $this->params->get('random', 0);
		$timeout          = $this->params->get('timeout', 5) * 1000;
		$speed            = $this->params->get('speed', 1) * 1000;
		$this->collection = $this->params->get('collection', 1);
		$this->pager      = $this->params->get('pager', 'pager');

		// Add the CSS to the template for each billboard
		foreach ($this->slides as $slide)
		{
			$background = $slide->background_img ? "background-image: url('{$slide->background_img}');" : '';
			$padding    = $slide->padding        ? "padding: {$slide->padding};"                        : '';

			$css =
				"#{$slide->alias} {
					$background
					}
				#{$slide->alias} p {
					$padding
					}";
			$this->css($css);
			$this->css($slide->css);
		}

		// Add the CSS to give the pager a unique ID per billboard collection
		// We need this to manage multiple buildboard pagers potentially moving at different speeds
		// @TODO: there should be a better way of doing this
		if ($this->pager != 'null')
		{
			$js_pager    = "'#{$this->pager}{$this->collection}'";
			$this->pager = $this->pager . $this->collection;
			$pager =
				".slider #{$this->pager} a.activeSlide {
					opacity:1.0;
					}";
			$this->css($pager);
		}
		else
		{
			$js_pager = $this->pager;
		}

		// Add the javascript ready function with variables based on this specific billboard
		// Pause: true - means the billbaord stops scrolling on hover
		$js = '
			jQuery(document).ready(function() {
				$(\'#' . $this->collection . '\').cycle({
					fx: "' . $transition . '",
					timeout: ' . $timeout .',
					pager: ' . $js_pager . ',
					speed: ' . $speed . ',
					random: ' . $random . ',
					cleartypeNoBg: true,
					slideResize: 0,
					pause: true
				});
			});';

		$this->js($js);

		require $this->getLayoutPath();
	}
}
