<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();
?>
<div class="component-demo">
    <h2>Hello, <?php echo $this->escape($this->greeting); ?>!</h2>
    <p>This is the <strong>com_demo</strong> component running through the legacy dispatch pipeline on Laravel.</p>
    <dl>
        <dt>Option</dt>
        <dd><?php echo $this->escape($this->option); ?></dd>
        <dt>Task</dt>
        <dd><?php echo $this->escape($this->task); ?></dd>
        <dt>Controller</dt>
        <dd><?php echo $this->escape($this->controller); ?></dd>
    </dl>
</div>
