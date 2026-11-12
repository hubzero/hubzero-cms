<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

// No direct access
defined('_HZEXEC_') or die('Restricted access');

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{notifications: mixed} $this
 */

setlocale(LC_MONETARY, 'en_US.UTF-8');

$this
     ->css()
     ->js();
?>

<header id="content-header">
    <h2><?php echo Lang::txt('COM_CART'); ?>: Download</h2>
</header>

<?php

if (!empty($this->notifications)) {
    $view = new \Hubzero\Component\View(['name' => 'shared', 'layout' => 'notifications']);
    $view->set('notifications', $this->notifications);
    $view->display();
}
