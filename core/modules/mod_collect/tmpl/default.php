<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();

$foo = App::get('editor')->display('description', '', '', '', 35, 5, false, 'field_description', null, null, array('class' => 'minimal no-footer'));

$url  = urldecode(Request::path());
$url  = implode('/', array_map('rawurlencode', explode('/', $url)));
$url .= (strstr($url, '?') ? '&' : '?') . 'tryto=collect';
?>

<p class="collector"<?php if ($this->params->get('id')) { echo ' id="' . $this->params->get('id') . '"'; } ?>>
	<a class="icon-collect btn collect-this" href="<?php echo htmlspecialchars($url); ?>">
		<?php echo Lang::txt('MOD_COLLECT_ACTION'); ?>
	</a>
</p>