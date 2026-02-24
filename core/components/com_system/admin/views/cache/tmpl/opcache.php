<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('cache');

Toolbar::title(Lang::txt('COM_SYSTEM_CACHE_OPCACHE'), 'config');

$opcache = $this->opcache;

if ($opcache) {
    Toolbar::custom('resetopcache', 'refresh', '', Lang::txt('COM_SYSTEM_CACHE_RESET_OPCACHE'), false);
}
?>

<?php echo $this->loadTemplate('_submenu'); ?>

<?php if (!$opcache) : ?>
    <p class="warning">
        <?php echo Lang::txt('COM_SYSTEM_CACHE_OPCACHE_NOT_AVAILABLE'); ?>
    </p>
<?php else : ?>
    <?php
        $scripts = $opcache['scripts'] ?? [];
        // Sort by hits descending
        usort($scripts, function ($a, $b) {
            return $b['hits'] - $a['hits'];
        });
    ?>

    <form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>"
          method="post"
          name="adminForm"
          id="adminForm">

        <fieldset class="adminform">
            <legend>
                <?php echo Lang::txt('COM_SYSTEM_CACHE_OPCACHE'); ?>
                (<?php echo number_format(count($scripts)); ?> scripts)
            </legend>

            <table class="adminlist">
                <thead>
                    <tr>
                        <th scope="col">Script</th>
                        <th scope="col" class="priority-3">
                            <?php echo Lang::txt('COM_SYSTEM_CACHE_HITS'); ?>
                        </th>
                        <th scope="col" class="priority-3">
                            <?php echo Lang::txt('COM_SYSTEM_CACHE_MEMORY'); ?>
                        </th>
                        <th scope="col" class="priority-4">Last Used</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($scripts)) : ?>
                        <tr>
                            <td colspan="4">No cached scripts.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($scripts as $script) : ?>
                        <tr>
                            <td>
                                <span class="smallsub">
                                    <?php echo htmlspecialchars($script['full_path']); ?>
                                </span>
                            </td>
                            <td class="priority-3">
                                <?php echo number_format($script['hits']); ?>
                            </td>
                            <td class="priority-3">
                                <?php echo \Hubzero\Utility\Number::formatBytes($script['memory_consumption']); ?>
                            </td>
                            <td class="priority-4">
                                <?php echo date('Y-m-d H:i:s', $script['last_used_timestamp']); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </fieldset>

        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
        <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
        <input type="hidden" name="task" value="opcache" />
    </form>
<?php endif; ?>
