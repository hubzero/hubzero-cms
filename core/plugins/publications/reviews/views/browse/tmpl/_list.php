<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Generic.Files.LineLength.TooLong

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

?>
<ol class="comments" id="t<?php echo (isset($this->parent)) ? $this->parent : '0'; ?>">
<?php
if (isset($this->comments) && $this->comments) {
    $cls = 'odd';
    if (isset($this->cls)) {
        $cls = ($this->cls == 'odd') ? 'even' : 'odd';
    }

    $this->depth++;

    foreach ($this->comments as $comment) {
        $this->view('_comment')
             ->set('option', $this->option)
             ->set('comment', $comment)
             ->set('config', $this->config)
             ->set('depth', $this->depth)
             ->set('publication', $this->publication)
             ->set('cls', $cls)
             ->set('base', $this->base)
             ->display();
    }
}
?>
</ol>