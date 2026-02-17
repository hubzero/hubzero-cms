<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('ipynb.css');
$this->js('highlighter/shCore.js')
     ->js('highlighter/shBrushPython.js')
     ->js('ipynb.js');
?>
<div class="file-preview ipynb">
    <div class="file-preview-code">
        <div class="cells">
            <?php
            $contents = json_decode($this->file->read());

            $parser = new cebe\markdown\GithubMarkdown();

            $output = array();

            if (isset($contents->worksheets)) :
                foreach ($contents->worksheets as $worksheet) :
                    foreach ($worksheet->cells as $cell) :
                        $output[] = $this->view('cell')
                                    ->set('cell', $cell)
                                    ->set('parser', $parser)
                                    ->loadTemplate();
                    endforeach;
                endforeach;
            elseif (isset($contents->cells)) :
                foreach ($contents->cells as $cell) :
                    $output[] = $this->view('cell')
                                    ->set('cell', $cell)
                                    ->set('parser', $parser)
                                    ->loadTemplate();
                endforeach;
            endif;

            echo implode("\n", $output);
            ?>
        </div>
    </div>
</div>