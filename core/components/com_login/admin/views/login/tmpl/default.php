<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Module;

// No direct access.
defined('_HZEXEC_') or die();
?>
<style>
#content.com_users .auth:before,
#content.com_users .auth:after {
        -webkit-box-sizing: content-box;
        -moz-box-sizing: content-box;
        box-sizing: content-box;
}

.auth .bootstrap-select {
        white-space: normal;
        position: static;
}
.auth .bootstrap-select .btn-default {
        position: absolute;
        top: 9px;
        left: 45px;
}

.hz_user>p {
        margin: 1em;
}

@media only screen  and (min-width : 1024px) {
        .hz_user>p {
                max-width: 700px;
                margin: 2em auto;
        }
}
</style>
<?php

// Get the login modules
// If you want to use a completely different login module change the value of name
// in your layout override.

$loginmodule = \Components\Login\Models\Login::getLoginModule('mod_adminlogin');

echo Module::render($loginmodule, array('style' => 'rounded', 'id' => 'section-box'));


// Get any other modules in the login position.
// If you want to use a different position for the modules, change the name here in your override.
$modules = Module::byPosition('login');

foreach ($modules as $module) {
    // Render the login modules
    if ($module->module != 'mod_adminlogin') {
        echo Module::render($module, array('style' => 'rounded', 'id' => 'section-box'));
    }
}
