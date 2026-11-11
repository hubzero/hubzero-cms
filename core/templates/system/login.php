<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

// No direct access.
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Document\Type\Html $this
 */
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>"
    class="<?php echo $this->direction; ?>">
    <head>
        <jdoc:include type="head" />
        <link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/system.css" type="text/css" />
    </head>
    <body id="login-body">
        <div class="container">
            <jdoc:include type="message" />
            <jdoc:include type="component" />
            <noscript>
                <?php echo Lang::txt('JGLOBAL_WARNJAVASCRIPT'); ?>
            </noscript>
        </div>
    </body>
</html>