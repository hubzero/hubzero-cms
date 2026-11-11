<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Components\Courses\Helpers\Form;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{base: mixed, dep: mixed, pdf: mixed, resp: mixed} $this
 */

/**
 * Set by the code that includes this file.
 *
 * @var mixed $limit
 * @var mixed $realLimit
 */
?>
<p>
    You have <strong><?php echo Form::timeDiff($realLimit * 60); ?></strong> to complete this form.
    There are <strong><?php echo $this->pdf->getQuestionCount(); ?></strong> questions.
    <?php if ($this->dep->getAllowedAttempts() > 1) : ?>
        You are allowed <strong><?php echo $this->dep->getAllowedAttempts(); ?></strong> attempts.
        This is your <strong><?php echo Form::toOrdinal((int) $this->resp->getAttemptNumber()); ?></strong> attempt.
    <?php endif; ?>
</p>
<?php if ($realLimit == $limit) : ?>
    <p><em>Time will begin counting when you click 'Continue' below.</em></p>
<?php else : ?>
    <p><em>Time is already running because the form is close to expiring!</em></p>
<?php endif; ?>
<form action="<?php echo Route::url($this->base); ?>" method="post">
    <fieldset>
        <input type="hidden" name="task" value="startWork" />
        <input type="hidden" name="crumb" value="<?php echo $this->dep->getCrumb(); ?>" />
        <input type="hidden" name="attempt" value="<?php echo (int) $this->resp->getAttemptNumber(); ?>" />
        <input type="hidden" name="controller" value="form" />
        <?php
        echo (isset($_GET['tmpl']) && is_scalar($_GET['tmpl']))
            ? '<input type="hidden" name="tmpl" value="' . htmlspecialchars((string) $_GET['tmpl'], ENT_QUOTES) . '" />'
            : '';
        ?>
        <button type="submit">Continue</button>
    </fieldset>
</form>
