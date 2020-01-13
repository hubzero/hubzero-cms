<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/formsRouter.php";

use Components\Forms\Helpers\FormsRouter;

$formId = $this->formId;
$router = new FormsRouter();
$startUrl = $router->formResponseStartUrl($formId)
?>

<a class="start" href="<?php echo $startUrl; ?>">
	Start &#x2192
</a>
