<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// No direct access.
defined('_HZEXEC_') or die();

$attachments = 0;
$authors     = 0;
$tags        = 0;
$state       = 'draft';

if ($this->progress == null) {
    $this->progress = array();
    $this->progress['submitted'] = 0;
}

if ($this->resource->get('id')) {
    switch ($this->resource->get('published')) {
        case 1:
            $state = 'published';
            break;  // published
        case 2:
            $state = 'draft';
            break;  // draft
        case 3:
            $state = 'pending';
            break;  // pending
        case 0:
        default:
            $state = 'unpublished';
            break;  // unpublished
    }

    $attachments = $this->resource->children()->total();
    $authors =  $this->resource->authors()->total();
    $tags = count($this->resource->tags());
}

$this->group_cn = (isset($this->group_cn) ? $this->group_cn : Request::getString('group', ''));

?>
<div class="meta-container">
    <table class="meta">
        <thead>
            <tr>
                <th scope="col"><?php echo Lang::txt('COM_CONTRIBUTE_STEP_TYPE'); ?></th>
                <th scope="col"><?php echo Lang::txt('COM_CONTRIBUTE_TITLE'); ?></th>
                <th scope="col" colspan="3"><?php echo Lang::txt('COM_CONTRIBUTE_ASSOCIATIONS'); ?></th>
                <th scope="col"><?php echo Lang::txt('COM_CONTRIBUTE_STATUS'); ?></th>
            <?php if ($this->progress['submitted'] != 1) { ?>
                <th></th>
            <?php } ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <?php echo $this->resource->type()->get('type', Lang::txt('COM_CONTRIBUTE_NONE')); ?>
                </td>
                <td>
                    <?php
                    if ($this->resource->get('title')) {
                        $titleStr = stripslashes($this->resource->get('title'));
                        $titleStr = \Hubzero\Utility\Str::truncate($titleStr, 150);
                        echo $this->escape($titleStr);
                    } else {
                        echo Lang::txt('COM_CONTRIBUTE_NONE');
                    }
                    ?>
                </td>
                <td>
                    <?php echo Lang::txt('%s attachment(s)', $attachments); ?>
                </td>
                <td>
                    <?php echo Lang::txt('%s author(s)', $authors); ?>
                </td>
                <td>
                    <?php echo Lang::txt('%s tag(s)', $tags); ?>
                </td>
                <td>
                    <span class="<?php echo $state; ?> status"><?php echo $state; ?></span>
                </td>
            <?php if ($this->progress['submitted'] != 1) { ?>
                <td>
                <?php if ($this->step == 'discard') { ?>
                    <strong><?php echo Lang::txt('JCANCEL'); ?></strong>
                <?php } else { ?>
                    <?php
                    $hrefUrl = Route::url(
                        'index.php?option='
                        . $this->option
                        . '&task=discard&id='
                        . $this->id
                    );
                    ?>
                    <a class="icon-delete" href="<?php echo $hrefUrl; ?>"><?php echo Lang::txt('JCANCEL'); ?></a>
                <?php } ?>
                </td>
            <?php } ?>
            </tr>
        </tbody>
    </table>
</div>

<ol id="steps">
    <li id="start">
        <?php
        if ($this->progress['submitted'] == 1) {
            $startUrl = Route::url('index.php?option=com_resources&id=' . $this->id);
        } else {
            $startUrl = Route::url('index.php?option=' . $this->option . '&task=new');
        }
        ?>
        <a href="<?php echo $startUrl; ?>">
            <?php echo Lang::txt('COM_CONTRIBUTE_START'); ?>
        </a>
    </li>
    <?php
    $laststep = (count($this->steps) - 1);

    $html  = '';
    for ($i = 1, $n = count($this->steps); $i < $n; $i++) {
        $html .= "\t" . '<li';
        if ($this->step == $i) {
            $html .= ' class="active"';
        } elseif (isset($this->progress[$this->steps[$i]]) && $this->progress[$this->steps[$i]] == 1) {
            $html .= ' class="completed"';
        }
        $html .= '>';
        if ($this->step == $i) {
            $html .= '<strong>' . Lang::txt('COM_CONTRIBUTE_STEP_' . strtoupper($this->steps[$i])) . '</strong>';
        } elseif (
            (isset($this->progress[$this->steps[$i]])
            && $this->progress[$this->steps[$i]] == 1)
            || $this->step > $i
        ) {
            $html .= '<a href="' . Route::url('index.php?option=' . $this->option . '&task=draft&step=' . $i . '&id=' .
            $this->id . '&group=' . $this->group_cn) . '">' . Lang::txt('COM_CONTRIBUTE_STEP_' .
            strtoupper($this->steps[$i])) . '</a>';
        } else {
            if ($this->progress['submitted'] == 1) {
                $html .= '<a href="'
                    .  Route::url('index.php?option=' . $this->option . '&task=draft&step=' . $i . '&id=' . $this->id)
                    .  '">' .  Lang::txt('COM_CONTRIBUTE_STEP_' . strtoupper($this->steps[$i])) .  '</a>';
            } else {
                $html .= '<span>' . $this->steps[$i] . '</span>';
            }
        }
        $html .= '</li>' . "\n";
    }
    echo $html;
    ?>
</ol>
<div class="clear"></div>
