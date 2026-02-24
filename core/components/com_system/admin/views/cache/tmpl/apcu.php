<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('cache');

Toolbar::title(Lang::txt('COM_SYSTEM_CACHE_APCU'), 'config');

$apcu = $this->apcu;
$apcuMem = $this->apcuMem;

if ($apcu) {
    Toolbar::custom('resetapcu', 'refresh', '', Lang::txt('COM_SYSTEM_CACHE_RESET_APCU'), false);
}
?>

<?php echo $this->loadTemplate('_submenu'); ?>

<?php if (!$apcu) : ?>
    <p class="warning">
        <?php echo Lang::txt('COM_SYSTEM_CACHE_APCU_NOT_AVAILABLE'); ?>
    </p>
<?php else : ?>
    <?php
        $entries = $apcu['cache_list'] ?? [];
        // Sort by hits descending
        usort($entries, function ($a, $b) {
            return ($b['num_hits'] ?? 0) - ($a['num_hits'] ?? 0);
        });
    ?>

    <form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>"
          method="post"
          name="adminForm"
          id="adminForm">

        <fieldset class="adminform">
            <legend>
                <?php echo Lang::txt('COM_SYSTEM_CACHE_APCU'); ?>
                (<?php echo number_format(count($entries)); ?> entries)
            </legend>

            <table class="adminlist">
                <thead>
                    <tr>
                        <th scope="col">Key</th>
                        <th scope="col" class="priority-3">
                            <?php echo Lang::txt('COM_SYSTEM_CACHE_HITS'); ?>
                        </th>
                        <th scope="col" class="priority-3">Size</th>
                        <th scope="col" class="priority-4">Created</th>
                        <th scope="col" class="priority-4">TTL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($entries)) : ?>
                        <tr>
                            <td colspan="5">No cached entries.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($entries as $entry) : ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($entry['info'] ?? $entry['key'] ?? ''); ?>
                            </td>
                            <td class="priority-3">
                                <?php echo number_format($entry['num_hits'] ?? 0); ?>
                            </td>
                            <td class="priority-3">
                                <?php echo \Hubzero\Utility\Number::formatBytes($entry['mem_size'] ?? 0); ?>
                            </td>
                            <td class="priority-4">
                                <?php
                                    $created = $entry['creation_time'] ?? $entry['mtime'] ?? 0;
                                    echo $created ? date('Y-m-d H:i:s', $created) : 'n/a';
                                ?>
                            </td>
                            <td class="priority-4">
                                <?php
                                    $ttl = $entry['ttl'] ?? 0;
                                    echo $ttl > 0 ? number_format($ttl) . 's' : 'none';
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </fieldset>

        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
        <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
        <input type="hidden" name="task" value="apcu" />
    </form>
<?php endif; ?>
