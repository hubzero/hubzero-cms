<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

// Push the module CSS to the template
$this->css()
     ->js();
?>

<li class="component-parent" id="mygroupsmini">
  <a class="component-button"><span class="nav-icon-groups"><?php echo file_get_contents(PATH_CORE . DS . "assets/icons/group.svg") ?></span><span>My Groups</span><span class="nav-icon-more"><?php echo file_get_contents(PATH_CORE . DS . "assets/icons/chevron-right.svg") ?></span></a>
  <div class="component-panel">
    <header><h2>My Groups</h2></header>
    <a class="component-button"><span class="nav-icon-back"><?php echo file_get_contents(PATH_CORE . DS . "assets/icons/chevron-left.svg") ?></span>Back</a>
    <div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : '';?>>
    
    <ul class="module-nav grouped">
      <?php $total = count($this->allgroups);
      if (count($this->recentgroups) > 0) {
        foreach ($this->recentgroups as $group)
        {
          if ($group->published)
          {
            $status = $this->getStatus($group);
            
            require $this->getLayoutPath('_item');
          }
        }
        ?>
      <?php } else { ?>
        <p><em><?php echo Lang::txt('MOD_MYGROUPSMINI_NO_RECENT_GROUPS'); ?></em></p>
      <?php } ?>
      
      <?php if ($total > $this->limit) { ?>
  			<li class="note"><?php echo Lang::txt('MOD_MYGROUPSMINI_YOU_HAVE_MORE', $this->limit, $total, Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=groups')); ?></li>
  		<?php } ?>
      
      <li>
        <a class="icon-browse" href="<?php echo Route::url('index.php?option=com_groups'); ?>">
          <?php echo Lang::txt('MOD_MYGROUPSMINI_ALL_GROUPS'); ?>
        </a>
      </li>
      
      <?php if ($this->params->get('button_show_add', 1)) { ?>
        <li>
          <a class="icon-plus" href="<?php echo Route::url('index.php?option=com_groups&task=new'); ?>">
            <?php echo Lang::txt('MOD_MYGROUPSMINI_NEW_GROUP'); ?>
          </a>
        </li>
      </ul>
    <?php } ?>
  </div>
</div>
</li>
