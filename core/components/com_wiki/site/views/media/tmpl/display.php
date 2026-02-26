<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;

// No direct access.
defined('_HZEXEC_') or die();

?>
    <div id="attachments">
        <?php
        $baseUrl = Request::base(true);
        $iframeSrc = $baseUrl . '/index.php?option=' . $this->option
            . '&amp;tmpl=component&amp;controller=media&amp;task=list'
            . '&amp;listdir=' . $this->listdir;
        ?>
        <form action="<?php echo $baseUrl; ?>/index.php" id="adminForm"
            method="post" enctype="multipart/form-data">
            <fieldset>
                <div id="themanager" class="manager">
                    <iframe src="<?php echo $iframeSrc; ?>"
                        name="imgManager" id="imgManager" width="98%" height="180"></iframe>
                </div>
            </fieldset>

            <fieldset>
                <table>
                    <tbody>
                        <tr>
                            <td><input type="file" name="upload" id="upload" /></td>
                        </tr>
                        <tr>
                            <td><input type="submit" value="<?php echo Lang::txt('COM_WIKI_UPLOAD'); ?>" /></td>
                        </tr>
                    </tbody>
                </table>

                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="listdir" id="listdir" value="<?php echo $this->listdir; ?>" />
                <input type="hidden" name="task" value="upload" />
                <input type="hidden" name="tmpl" value="component" />
                <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
            </fieldset>
        </form>
<?php if ($this->getError()) { ?>
        <p class="error"><?php echo $this->getError(); ?></p>
<?php }
