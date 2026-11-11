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
 * @var \Hubzero\Plugin\View&object{
 *     fileparams: mixed,
 *     items: mixed,
 *     model: mixed,
 *     option: mixed,
 *     params: mixed,
 *     publishing: mixed,
 *     repo: mixed,
 *     subdir: mixed,
 * } $this
 */

if (is_array($this->items)) {
    foreach ($this->items as $item) {
        $this
         ->view('_item')
         ->set('option', $this->option)
         ->set('model', $this->model)
         ->set('subdir', $this->subdir)
         ->set('item', $item)
         ->set('repo', $this->repo)
         ->set('params', $this->params)
         ->set('fileparams', $this->fileparams)
         ->set('publishing', $this->publishing)
         ->display();
    }
}
