<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;

// No direct access
defined('_HZEXEC_') or die();

$this->js('media.js');
?>
<div id="attachments">
    <?php $routeUrl = Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>
    <form action="<?php echo $routeUrl; ?>" method="post" id="filelist" name="filelist">
        <table>
            <tbody>
                <?php if (count($this->docs) == 0) { ?>
                    <tr>
                        <td>
                            <?php echo Lang::txt('No files found.'); ?>
                        </td>
                    </tr>
                    <?php
                } else {
                    $docs = $this->docs;
                    for ($i = 0; $i < count($docs); $i++) {
                        $docName = key($docs);

                        $subdird = ($this->subdir && $this->subdir != DS) ? $this->subdir . DS : DS;
                        ?>
                        <tr>
                            <td>
                                <?php echo $docs[$docName]; ?>
                            </td>
                            <td>
                                <?php
                                    $routeUrl = Route::url(
                                        'index.php?option=' . $this->option  . '&controller=' . $this->controller
                                        . '&task=deletefile&delFile=' . $docs[$docName] . '&listdir='
                                        . $this->listdir . '&tmpl=component&subdir=' . $this->subdir . '&course='
                                        . $this->course_id . '&' . Session::getFormToken() . '=1'
                                    );
                                ?>
                                <a
                                    class="delete-file"
                                    href="<?php echo $routeUrl; ?>"
                                    <?php
                                        $confirmMsg = Lang::txt(
                                            'Are you sure you want to delete the file "%s"?',
                                            $docs[$docName]
                                        );
                                    ?>
                                    data-confirm="<?php echo $confirmMsg; ?>"
                                    title="<?php echo Lang::txt('DELETE'); ?>">
                                    <img
                                        <?php
                                            $trashImg = Request::base(true)
                                                . '/core/components/' . $this->option
                                                . '/admin/assets/img/trash.png';
                                        ?>
                                        src="<?php echo $trashImg; ?>"
                                        width="15"
                                        height="15"
                                        alt="<?php echo Lang::txt('DELETE'); ?>" />
                                </a>
                            </td>
                        </tr>
                        <?php
                        next($docs);
                    }
                }
                ?>
            </tbody>
        </table>
    </form>
    <?php if ($this->getError()) { ?>
        <p class="error"><?php echo $this->getError(); ?></p>
    <?php } ?>
</div>