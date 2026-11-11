<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{cell: mixed, parser: mixed} $this
 */

$cell = $this->cell;

// nbformat multiline_string fields are a list of lines OR a single string
$text = function ($value) {
    return is_array($value) ? implode('', $value) : (string) $value;
};

$source = '';
if (isset($cell->source)) :
    $source = $text($cell->source);
elseif (isset($cell->input)) :
        $source = $text($cell->input);
endif;
?>

<?php if ($cell->cell_type == 'markdown') : ?>
    <div class="cell <?php echo $cell->cell_type; ?> rendered">
        <div class="input">
            <div class="prompt input_prompt"> </div>
            <div class="inner_cell">
                <div class="text_cell_render rendered_html">
                    <?= \Hubzero\Utility\Sanitize::html(($this->parser) ? $this->parser->parse($source) : $source) ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php
if ($cell->cell_type == 'code') :
    if (!empty($cell->input)) :
        ?>
        <div class="cell <?php echo $cell->cell_type; ?> rendered">
            <div class="input">
                <div class="prompt input_prompt">
                    In [<?= (isset($cell->prompt_number)) ? $this->escape($cell->prompt_number) : ' ' ?>]:
                </div>
                <div class="inner_cell">
                    <?php
                    $language = (isset($cell->language)) ? $this->escape($cell->language) : 'python';
                    ?>
                    <pre name="code" class="<?= $language ?>:nogutter:nocontrols"><?= $this->escape($source) ?></pre>
                </div>
            </div>
        </div>
        <?php
    endif;
    if (!empty($cell->outputs)) :
        $cls = '';
        if (isset($output->png)) :
            $cls = 'output_png';
        endif;
        ?>
        <div class="cell <?php echo $cell->cell_type; ?> rendered">
            <div class="input">
                <div class="prompt output_prompt">
                    Out[<?= (isset($cell->prompt_number)) ? $this->escape($cell->prompt_number) : ' ' ?>]:
                </div>
                <div class="inner_cell <?php echo $cls; ?> output_execute_result">
                    <?php
                    $out = [];
                    foreach ($cell->outputs as $output) :
                        if ($output->output_type == 'pyout') :
                            ?>
                            <?php if (isset($output->png)) : ?>
                                <?php
                                $sourceUrl = 'data:image/png;base64,'
                                    . preg_replace('/[^A-Za-z0-9+\/=]/', '', (string) $output->png);
                                $altText = $this->escape(isset($output->text) ? $text($output->text) : '');
                                ?>
                                <div class="output_img"><img src="<?= $sourceUrl ?>" alt="<?= $altText ?>" /></div>
                            <?php else : ?>
                                <?php
                                $outputText = $this->escape(isset($output->text) ? $text($output->text) : '');
                                ?>
                                <pre class="output"><?= $outputText ?></pre>
                            <?php endif; ?>
                        <?php elseif ($output->output_type == 'stream') :
                                $out[] = isset($output->text) ? $text($output->text) : '';
                        endif;
                    endforeach;

                    if (!empty($out)) :
                        ?>
                        <pre class="output"><?php echo $this->escape(implode('', $out)); ?></pre>
                        <?php
                    endif;
                    ?>
                </div>
            </div>
        </div>
        <?php
    endif;
endif;
