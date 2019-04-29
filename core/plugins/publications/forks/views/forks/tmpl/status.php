<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

$url = Route::url($this->publication->link() . '&v=' . $this->publication->versionAlias . '&active=forks&action=fork');
if (User::isGuest())
{
	$url = Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url));
}
else
{
	$this->js();
}
?>
<div class="btn-group item-fork">
	<a class="btn icon-fork" id="fork-this" href="<?php echo $url; ?>"><?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_FORK'); ?></a>
	<a class="btn" href="<?php echo Route::url($this->publication->link() . '&v=' . $this->publication->versionAlias . '&active=forks'); ?>" title="<?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_FORKED_N_TIMES', $this->forks); ?>"><?php echo $this->forks; ?></a>
</div>
