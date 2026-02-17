<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<?php
$formUrl = Route::url(
    'index.php?option=com_support&task=new&tmpl=component&referrer='
    . $this->referrer
);
?>
<div id="help-pane" data-form="<?php echo $formUrl; ?>">
</div><!-- / #help-pane -->
