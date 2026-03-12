<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

defined('_HZEXEC_') or die();
?>
<form action="index.php?option=com_search" method="post">
    <input type="hidden" name="search-task" value="<?php echo $this->edit ? 'SiteMapSaveEdit' : 'SiteMapEdit'; ?>" />
    <table class="adminlist">
        <thead>
            <tr>
                <th><?php echo Lang::txt('COM_SEARCH_COL_TITLE'); ?></th>
                <th><?php echo Lang::txt('COM_SEARCH_COL_LINK'); ?></th>
                <th><?php echo Lang::txt('COM_SEARCH_COL_DESCRIPTION'); ?></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->map as $item) : ?>
                <tr>
                    <?php if ($this->edit == $item['id']) : ?>
                        <?php
                        $titleVal = array_key_exists('sm-title', $_POST) ? $_POST['sm-title'] : $item['title'];
                        $linkVal  = array_key_exists('sm-link', $_POST) ? $_POST['sm-link'] : $item['link'];
                        $descVal  = array_key_exists('sm-description', $_POST) ? $_POST['sm-description'] : $item['description'];
                        ?>
                        <td>
                            <input type="text" name="sm-title" value="<?php echo htmlentities($titleVal); ?>" />
                        </td>
                        <td>
                            <input type="text" name="sm-link" value="<?php echo htmlentities($linkVal); ?>" />
                        </td>
                        <td>
                            <textarea cols="60" rows="3" name="sm-description"><?php echo htmlentities($descVal); ?></textarea>
                        </td>
                        <td>
                            <input type="hidden" name="sm-id" value="<?php echo $item['id']; ?>" />
                            <input type="submit" name="save" value="<?php echo Lang::txt('COM_SEARCH_SAVE'); ?>" />
                            <input type="submit" name="cancel" value="<?php echo Lang::txt('COM_SEARCH_CANCEL'); ?>" />
                        </td>
                    <?php else : ?>
                        <td><?php echo htmlentities($item['title']); ?></td>
                        <td><?php echo htmlentities($item['link']); ?></td>
                        <td><?php echo htmlentities($item['description']); ?></td>
                        <td>
                            <?php if (!$this->edit) : ?>
                                <input type="submit" name="edit-<?php echo $item['id']; ?>" value="<?php echo Lang::txt('COM_SEARCH_EDIT'); ?>" />
                                <input type="submit" name="delete-<?php echo $item['id']; ?>" value="<?php echo Lang::txt('COM_SEARCH_DELETE'); ?>" />
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            <?php if (!$this->edit) : ?>
                <tr>
                    <?php
                    $newTitleVal = array_key_exists('new-sm-title', $_POST) ? $_POST['new-sm-title'] : '';
                    $newLinkVal  = array_key_exists('new-sm-link', $_POST) ? $_POST['new-sm-link'] : '';
                    $newDescVal  = array_key_exists('new-sm-description', $_POST) ? $_POST['new-sm-description'] : '';
                    ?>
                    <td>
                        <input type="text" name="new-sm-title" value="<?php echo htmlentities($newTitleVal); ?>" />
                    </td>
                    <td>
                        <input type="text" name="new-sm-link" value="<?php echo htmlentities($newLinkVal); ?>" />
                    </td>
                    <td>
                        <textarea cols="60" rows="3" name="new-sm-description"><?php echo htmlentities($newDescVal); ?></textarea>
                    </td>
                    <td>
                        <input type="submit" name="add" value="<?php echo Lang::txt('COM_SEARCH_ADD'); ?>" />
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</form>
