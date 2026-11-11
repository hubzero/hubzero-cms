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
 * @var \Hubzero\Component\View&object{facetCounts: mixed, filter: mixed, selectedOptions: mixed} $this
 */
?>
<fieldset class="search-filters">
<legend><?php echo $this->escape($this->filter->label); ?></legend>
<?php foreach ($this->filter->options as $option) : ?>
        <label>
            <?php $checked = in_array($option->value, $this->selectedOptions) ? 'checked' : ''; ?>
            <input type="checkbox"
                class="checkbox"
                name="filters[<?php echo $this->filter->field; ?>][<?php echo $option->id; ?>]"
                value="<?php echo $option->value; ?>"
                <?php echo $checked; ?>
            />
            <?php
                $countIndex = $this->filter->field . '_' . $option->id;
                $count = isset($this->facetCounts[$countIndex]) ? $this->facetCounts[$countIndex] : '';
            ?>
            <?php echo $option->value . ' ' . $count; ?>
        </label>
<?php endforeach; ?>
</fieldset>

