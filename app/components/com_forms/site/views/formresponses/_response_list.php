<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$responses = $this->responses;

?>

<ul class="responses-list">
	<?php
		foreach ($responses as $response):
			$this->view('_response_item')
				->set('response', $response)
				->display();
		endforeach;
	?>
</ul>
