<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$componentPath = \Component::path('com_forms');

require_once "$componentPath/helpers/formsRouter.php";

use Components\Forms\Helpers\FormsRouter;

$routes = new FormsRouter();

$classes = isset($this->classes) ? $this->classes : '';
$content = $this->content;
$urlFunction = $this->urlFunction;
$urlFunctionArgs = $this->urlFunctionArgs;
$url = $routes->$urlFunction(...$urlFunctionArgs);
?>

<a href="<?php echo $url; ?>" class="protected-link <?php echo $classes; ?>">
	<?php echo $content; ?>
</a>
