<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;
use Hubzero\Facades\User;

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('Solr Search: Index Blacklist'));
Toolbar::preferences($this->option, '550');
$this->css('solr');
$option = $this->option;
$header = clone $this->blacklist;
$header = $header->toArray();

$this->view('_submenu', 'shared')
    ->display();
?>

<?php if (($this->blacklist->count() > 0)) : ?>
    <table class="adminlist">
        <thead>
            <tr>
                <?php
                // The columns come back named as the table names them. Say
                // them the way the rest of the screen does, and fall back to
                // the column for anything this list does not know about.
                $labels = array(
                    'doc_id'     => 'COM_SEARCH_LABEL_DOC_ID',
                    'created_by' => 'COM_SEARCH_LABEL_CREATED_BY',
                    'created'    => 'COM_SEARCH_LABEL_CREATED',
                );
                ?>
                <?php foreach (array_keys($header[0]) as $key) : ?>
                    <th><?php echo isset($labels[$key]) ? Lang::txt($labels[$key]) : $key; ?></th>
                <?php endforeach; ?>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->blacklist as $entry) : ?>
                <tr>
                    <?php foreach (array_keys($header[0]) as $key) : ?>
                        <?php if ($key == 'created_by') : ?>
                            <td>
                                <?php
                                    $memberUrl = Route::url(
                                        'index.php?option=com_members'
                                        . '&controller=members'
                                        . '&search_field=uidNumber'
                                        . '&search=' . $entry->$key
                                    );
                                    $memberName = User::getInstance($entry->$key)->name;
                                ?>
                                <a href="<?php echo $memberUrl;?>">
                                    <?php echo $memberName; ?>
                                </a>
                            </td>
                        <?php else : ?>
                            <td><?php echo $entry->$key; ?></td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <td>
                        <?php
                            $removeUrl = Route::url(
                                'index.php?option=' . $this->option
                                . '&controller=' . $this->controller
                                . '&task=removeBlacklistEntry'
                                . '&entryID=' . $entry->get('id')
                            );
                        ?>
                        <a href="<?php echo $removeUrl; ?>" class="button">
                            Remove entry
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else : ?>
    <div class="warning message"><?php echo Lang::txt('COM_SEARCH_NO_BLACKLIST_ENTRIES'); ?></div>
<?php endif;
