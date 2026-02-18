<?php

// @phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->css('uploader')
     ->js();

$metadata = Plugin::byType('metadata');

$subdirlink = $this->subdir ? '&subdir=' . urlencode($this->subdir) : '';
$sortbyDir  = $this->sortdir == 'ASC' ? 'DESC' : 'ASC';

$filesLink = $this->model->link('files');
$connParam = '&connection=' . $this->connection->id;
$browseBase = $filesLink . '&action=browse' . $connParam;
?>

<div id="preview-window"></div>

<form action="<?php echo Route::url($filesLink); ?>"
    method="post"
    enctype="multipart/form-data"
    id="plg-form"
    class="file-browser submit-ajax">
    <div id="plg-header">
        <h3 class="files">
            <a href="<?php echo Route::url($filesLink); ?>">
                <?php echo Lang::txt('Connections'); ?>
            </a>
            &nbsp;&raquo;
            <?php
            $imgRel = '/plugins/filesystem/'
                . $this->connection->provider->alias
                . '/assets/img/icon.png';
            $img = (is_file(PATH_APP . DS . $imgRel))
                ? '/app' . $imgRel
                : '/core' . $imgRel;
            ?>
            <img src="<?php echo $img; ?>"
                alt=""
                height="20"
                width="20" />
            <?php $browseUrl = Route::url($browseBase); ?>
            <a href="<?php echo $browseUrl; ?>">
                <?php echo $this->connection->name; ?>
            </a>
            &nbsp;
            <?php
            $crumbLink = $filesLink
                . '&action=browse' . $connParam;
            echo \Components\Projects\Helpers\Html::buildFileBrowserCrumbs(
                $this->subdir,
                $crumbLink,
                $parent,
                true,
                $this->connection->adapter()
            );
            ?>
        </h3>
    </div>
    <fieldset>
        <input type="hidden"
            name="subdir"
            id="subdir"
            value="<?php echo urlencode($this->subdir); ?>" />
        <input type="hidden"
            name="sortby"
            id="sortby"
            value="<?php echo $this->sortby; ?>" />
        <input type="hidden"
            name="sortdir"
            id="sortdir"
            value="<?php echo $this->sortdir; ?>" />
        <input type="hidden"
            name="id"
            id="projectid"
            value="<?php echo $this->model->get('id'); ?>" />
        <input type="hidden"
            name="uid"
            id="uid"
            value="<?php echo User::get('id'); ?>" />
    </fieldset>
    <div class="list-editing">
        <p>
            <?php if ($this->model->access('content')) : ?>
                <span id="manage_assets">
                    <?php if ($this->connection->provider->alias != 'github') {?>
                        <?php
                        $uploadUrl = Route::url(
                            $filesLink . $connParam
                            . '&action=upload' . $subdirlink
                        );
                        $uploadTitle = Lang::txt(
                            'PLG_PROJECTS_FILES_UPLOAD_TOOLTIP'
                        );
                        ?>
                    <a href="<?php echo $uploadUrl; ?>"
                        class="fmanage"
                        id="a-upload"
                        title="<?php echo $uploadTitle; ?>">
                        <span>
                            <?php echo Lang::txt('PLG_PROJECTS_FILES_UPLOAD'); ?>
                        </span>
                    </a>
                        <?php
                        $newdirUrl = Route::url(
                            $filesLink . $connParam
                            . '&action=newdir' . $subdirlink
                        );
                        $folderTitle = Lang::txt(
                            'PLG_PROJECTS_FILES_FOLDER_TOOLTIP'
                        );
                        ?>
                    <a href="<?php echo $newdirUrl; ?>"
                        id="a-folder"
                        title="<?php echo $folderTitle; ?>"
                        class="fmanage">
                        <span>
                            <?php echo Lang::txt('PLG_PROJECTS_FILES_NEW_FOLDER'); ?>
                        </span>
                    </a>
                    <?php }?>
                    <?php
                    $downloadUrl = Route::url(
                        $filesLink . $connParam
                        . '&action=download' . $subdirlink
                        . '&a=1'
                    );
                    $downloadTitle = Lang::txt(
                        'PLG_PROJECTS_FILES_DOWNLOAD_TOOLTIP'
                    );
                    ?>
                    <a href="<?php echo $downloadUrl; ?>"
                        class="fmanage js"
                        id="a-download"
                        title="<?php echo $downloadTitle; ?>">
                        <span>
                            <?php echo Lang::txt('PLG_PROJECTS_FILES_DOWNLOAD'); ?>
                        </span>
                    </a>
                    <?php if ($this->connection->provider->alias != 'github') {?>
                        <?php
                        $moveUrl = Route::url(
                            $filesLink . $connParam
                            . '&action=move' . $subdirlink
                        );
                        $moveTitle = Lang::txt(
                            'PLG_PROJECTS_FILES_MOVE_TOOLTIP'
                        );
                        ?>
                    <a href="<?php echo $moveUrl; ?>"
                        class="fmanage js"
                        id="a-move"
                        title="<?php echo $moveTitle; ?>">
                        <span>
                            <?php echo Lang::txt('PLG_PROJECTS_FILES_MOVE'); ?>
                        </span>
                    </a>
                        <?php
                        $deleteUrl = Route::url(
                            $filesLink . $connParam
                            . '&action=delete' . $subdirlink
                        );
                        $deleteTitle = Lang::txt(
                            'PLG_PROJECTS_FILES_DELETE_TOOLTIP'
                        );
                        ?>
                    <a href="<?php echo $deleteUrl; ?>"
                        class="fmanage js"
                        id="a-delete"
                        title="<?php echo $deleteTitle; ?>">
                        <span>
                            <?php echo Lang::txt('PLG_PROJECTS_FILES_DELETE'); ?>
                        </span>
                    </a>
                        <?php
                        $renameUrl = Route::url(
                            $filesLink . $connParam
                            . '&action=rename' . $subdirlink
                        );
                        $renameTitle = Lang::txt(
                            'PLG_PROJECTS_FILES_RENAME_TOOLTIP'
                        );
                        ?>
                    <a href="<?php echo $renameUrl; ?>"
                        class="fmanage js"
                        id="a-rename"
                        title="<?php echo $renameTitle; ?>">
                        <span>
                            <?php echo Lang::txt('PLG_PROJECTS_FILES_RENAME'); ?>
                        </span>
                    </a>
                    <?php }?>
                    <?php if (count($metadata)) : ?>
                        <?php
                        $annotateUrl = Route::url(
                            $filesLink . $connParam
                            . '&action=annotate' . $subdirlink
                        );
                        $annotateTitle = Lang::txt(
                            'PLG_PROJECTS_FILES_ANNOTATE_TOOLTIP'
                        );
                        ?>
                        <a href="<?php echo $annotateUrl; ?>"
                            class="fmanage js"
                            id="a-annotate"
                            title="<?php echo $annotateTitle; ?>">
                            <span>
                                <?php echo Lang::txt('PLG_PROJECTS_FILES_ANNOTATE'); ?>
                            </span>
                        </a>
                    <?php endif;
                    if ($this->connection->id) : ?>
                        <?php
                        $compileUrl = Route::url(
                            $filesLink . $connParam
                            . '&action=compile' . $subdirlink
                        );
                        $compileTitle = Lang::txt(
                            'PLG_PROJECTS_FILES_COMPILE_TOOLTIP'
                        );
                        ?>
                        <a href="<?php echo $compileUrl; ?>"
                            class="fmanage js"
                            id="a-handle"
                            title="<?php echo $compileTitle; ?>">
                            <span>
                                <?php echo Lang::txt('PLG_PROJECTS_FILES_COMPILE'); ?>
                            </span>
                        </a>
                    <?php endif; ?>
                </span>
                <noscript>
                    <span class="faded ipadded">
                        <?php
                        echo Lang::txt(
                            'Enable JavaScript in your browser'
                            . ' for advanced file management.'
                        );
                        ?>
                    </span>
                </noscript>
            <?php endif; ?>
        </p>
    </div>
    <table id="filelist" class="listing">
        <thead>
            <tr>
                <?php if ($this->model->access('content')) : ?>
                    <th class="checkbox">
                        <input type="checkbox"
                            name="toggle"
                            value=""
                            id="toggle"
                            class="js" />
                    </th>
                <?php endif; ?>
                <th class="asset_doc <?php if ($this->sortby == 'filename') {
                    echo ' activesort';
                                     } ?>">
                    <?php
                    $sortNameUrl = Route::url(
                        $browseBase . $subdirlink
                        . '&sortby=filename&sortdir='
                        . $sortbyDir
                    );
                    $sortNameTitle = Lang::txt(
                        'PLG_PROJECTS_FILES_SORT_BY'
                    ) . ' ' . Lang::txt(
                        'PLG_PROJECTS_FILES_NAME'
                    );
                    ?>
                    <a href="<?php echo $sortNameUrl; ?>"
                        class="re_sort"
                        title="<?php echo $sortNameTitle; ?>">
                        <?php echo Lang::txt('PLG_PROJECTS_FILES_NAME'); ?>
                    </a>
                </th>
                <th class="centeralign"></th>
                <th <?php if ($this->sortby == 'size') {
                    echo 'class="activesort"';
                    } ?>>
                    <?php
                    $sortSizeUrl = Route::url(
                        $browseBase . $subdirlink
                        . '&sortby=size&sortdir='
                        . $sortbyDir
                    );
                    $sortSizeTitle = Lang::txt(
                        'PLG_PROJECTS_FILES_SORT_BY'
                    ) . ' ' . Lang::txt(
                        'PLG_PROJECTS_FILES_SIZE'
                    );
                    ?>
                    <a href="<?php echo $sortSizeUrl; ?>"
                        class="re_sort"
                        title="<?php echo $sortSizeTitle; ?>">
                        <?php echo Lang::txt('PLG_PROJECTS_FILES_SIZE'); ?>
                    </a>
                </th>
                <th <?php if ($this->sortby == 'timestamp') {
                    echo 'class="activesort"';
                    } ?>>
                    <?php
                    $sortTimeUrl = Route::url(
                        $browseBase . $subdirlink
                        . '&sortby=timestamp&sortdir='
                        . $sortbyDir
                    );
                    $sortTimeTitle = Lang::txt(
                        'PLG_PROJECTS_FILES_SORT_BY'
                    ) . ' ' . ucfirst(Lang::txt(
                        'PLG_PROJECTS_FILES_MODIFIED'
                    ));
                    ?>
                    <a href="<?php echo $sortTimeUrl; ?>"
                        class="re_sort"
                        title="<?php echo $sortTimeTitle; ?>">
                        <?php
                        echo ucfirst(
                            Lang::txt('PLG_PROJECTS_FILES_MODIFIED')
                        );
                        ?>
                    </a>
                </th>
                <th>
                    <?php
                    echo ucfirst(
                        Lang::txt('PLG_PROJECTS_FILES_BY')
                    );
                    ?>
                </th>
                <th class="centeralign nojs"></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($this->subdir) : ?>
                <tr class="updir">
                    <td></td>
                    <?php $min = $this->model->access('content') ? 1 : 0; ?>
                    <td colspan="<?php echo 6 - $min; ?>" class="mini">
                        <?php
                        $parentUrl = Route::url(
                            $browseBase . '&subdir=' . $parent
                        );
                        $parentTxt = Lang::txt(
                            'PLG_PROJECTS_FILES_BACK_TO_PARENT_DIR'
                        );
                        ?>
                        <a href="<?php echo $parentUrl; ?>"
                            class="uptoparent">
                            <?php echo $parentTxt; ?>
                        </a>
                    </td>
                </tr>
            <?php endif; ?>
            <?php
                // Display contents
            if (count($this->items) > 0) {
                $this->view('_items')
                     ->set('option', $this->option)
                     ->set('model', $this->model)
                     ->set('subdir', $this->subdir)
                     ->set('items', $this->items)
                     ->set('connection', $this->connection)
                     ->set('config', $this->fileparams)
                     ->display();
            }
            ?>
        </tbody>
    </table>
    <?php if (count($this->items) == 0) : ?>
        <p class="noresults">
            <?php
            echo ($this->subdir)
                ? Lang::txt(
                    'PLG_PROJECTS_FILES_THIS_DIRECTORY_IS_EMPTY'
                )
                : Lang::txt(
                    'PLG_PROJECTS_FILES_PROJECT_HAS_NO_FILES'
                );
            ?>
        </p>
    <?php endif; ?>
 </form>
