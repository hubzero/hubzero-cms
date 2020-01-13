<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$formName = $this->formName;
$responseId = $this->responseId;
?>

<span>
	<?php
		$this->view('_link', 'shared')
			->set('content', $formName)
			->set('urlFunction', 'responseFeedUrl')
			->set('urlFunctionArgs', [$responseId])
			->display();
	?>
</span>
