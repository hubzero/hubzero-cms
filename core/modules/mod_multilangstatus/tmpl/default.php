<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

Html::behavior('modal');
?>
<span class="multilanguage"><a class="modal" href="<?php echo Route::url('index.php?option=com_languages&view=multilangstatus&tmpl=component');?>" rel="{handler:'iframe', size:{x:700,y:300}}"><?php echo Lang::txt('MOD_MULTILANGSTATUS');?></a></span>
