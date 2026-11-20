<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{editor: mixed} $this
 */

$this->js('preview');
?>

<table class="center" width="90%" id="preview-body" data-parent-text="<?php echo $this->editor->getContent('text'); ?>">
    <tbody>
        <tr>
            <td class="contentheading" colspan="2"><span id="preview-title"></span></td>
        </tr>
        <tr>
            <td valign="top" height="90%" colspan="2">
                <span id="preview-text"></span>
            </td>
        </tr>
    </tbody>
</table>
