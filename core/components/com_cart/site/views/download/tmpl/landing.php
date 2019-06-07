<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die('Restricted access');

setlocale(LC_MONETARY, 'en_US.UTF-8');

$this->css()
     ->js()
	->js('download.js');

$link = Route::url('index.php?option=com_cart', true, 0) . 'download/' . $this->tId . '/' . $this->sId . '/direct';

?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_CART'); ?>: Download</h2>
</header>

<section class="section">
<p>Thank you for requesting a file. Your download will begin shortly.</p>
<p>Problems with the download? Please use the <a href="<?php echo $link; ?>" id="cartRedirectUrl">direct link</a></p>
</section>