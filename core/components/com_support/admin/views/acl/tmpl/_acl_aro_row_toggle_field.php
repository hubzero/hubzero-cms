<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$id = $this->id;
$action = $this->action;
$isEnabled = $this->isEnabled;
$updateValue = $isEnabled ? '0' : '1';
$token = Session::getFormToken();
$action = Route::url(
	"index.php?option=$this->option&controller=$this->controller&task=update&id=$id&action=$action&value=$updateValue&$token=1"
);

if ($isEnabled)
{
	$alt = Lang::txt('JYES');
	$class = 'publish';
}
else
{
	$alt = Lang::txt('JNO');
	$class = 'unpublish';
}
?>

<td class="align-center">
	<a class="state <?php echo $class; ?>" href="<?php echo $action; ?>">
		<span>
			<?php echo $alt; ?>
		</span>
	</a>
</td>
