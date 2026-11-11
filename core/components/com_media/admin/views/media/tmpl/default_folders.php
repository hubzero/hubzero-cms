<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;

// No direct access.
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{folder: mixed, folderDepth: mixed, folderTree: mixed, folders_id: mixed} $this
 */

if (!isset($this->folderDepth)) :
    $this->set('folderDepth', 1);
endif;
?>
<ul <?php echo $this->folders_id; ?> class="<?php echo 'depth' . $this->folderDepth; ?>">
    <?php foreach ($this->folderTree as $folder) : ?>
        <?php
        $cls = '';

        $icon = Html::asset('image', 'assets/filetypes/folder.svg', '', null, true, true);

        $open = 0;
        $p = [];
        if ($this->folderDepth == 1) :
            $cls = ' class="open"';
            $icon = Html::asset('image', 'assets/filetypes/folder-open.svg', '', null, true, true);
        else :
                $fld = trim($this->folder, '/');
                $trail = explode('/', $fld);

                $p = explode('/', trim($folder['path'], '/'));

            foreach ($p as $i => $f) :
                if (!isset($trail[$i])) :
                    break;
                endif;

                if ($p[$i] == $trail[$i]) :
                    $open++;
                endif;
            endforeach;

            if ($open && $open == count($p)) :
                $cls = ' class="open"';
            endif;
        endif;
        ?>
        <li id="<?php echo $this->escape($folder['name']); ?>"<?php echo $cls; ?>>
            <?php
            $folderHref = Route::url(
                'index.php?option=com_media&controller=medialist'
                . '&tmpl=component&tmpl=' . Request::getCmd('tmpl')
                . '&' . Session::getFormToken() . '=1'
                . '&folder=/' . urlencode($folder['path']),
            );
            $dataFolder = $this->escape('/' . $folder['path']);
            ?>
            <a class="folder"
                data-folder="<?php echo $dataFolder; ?>"
                href="<?php echo $folderHref; ?>">
                <span class="folder-icon">
                    <img src="<?php echo $icon; ?>" alt="<?php echo $this->escape($folder['name']); ?>" />
                </span>
                <?php echo $this->escape($folder['name']); ?>
            </a>
            <?php
            if (isset($folder['children']) && count($folder['children'])) :
                $temp = $this->folderTree;

                $this->set('folderTree', $folder['children']);
                $this->set('folders_id', 'id="folder-' . $folder['name'] . '"');
                $this->set('folderDepth', $this->folderDepth + 1);

                echo $this->loadTemplate('folders');

                $this->set('folderTree', $temp);
                $this->set('folderDepth', $this->folderDepth - 1);
            endif;
            ?>
        </li>
    <?php endforeach; ?>
</ul>
