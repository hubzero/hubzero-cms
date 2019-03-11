<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

// Push the module CSS to the template
$this->css()
     ->js();
?>

<!-- Temporary location of code -->
<li id="account-groups">
  <a href="#" class="component-button"><span class="nav-icon-groups"><?php echo file_get_contents("core/assets/icons/group.svg") ?></span><span>My Groups</span><span class="nav-icon-more"><?php echo file_get_contents("core/assets/icons/chevron-right.svg") ?></span></a>
  <div class="component-panel">
    <header><h2>My Groups</h2></header>
    <a href="#" class="component-button"><span class="nav-icon-back"><?php echo file_get_contents("core/assets/icons/chevron-left.svg") ?></span>Back</a>
      <div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : '';?>>

    <ul class="module-nav grouped">
         <div id="recentgroups<?php echo $this->module->id; ?>" class="tab_panel<?php if (count($this->recentgroups) > 0) { echo ' active'; } ?>">
           <?php if (count($this->recentgroups) > 0) { ?>
                <?php
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
        </div>

      <li>
        <a href="<?php echo Route::url('index.php?option=com_groups'); ?>">All Groups </a>
      </li>

      <?php if ($this->params->get('button_show_add', 1)) { ?>
      			<li>
      				<a class="icon-plus" href="<?php echo Route::url('index.php?option=com_groups&task=new'); ?>"><?php echo Lang::txt('MOD_MYGROUPSMINI_NEW_GROUP'); ?></a></li>
      			</li>
      			<?php /*if ($this->params->get('button_show_all', 1)) { ?>
      				<p><a class="icon-browse" href="<?php echo Route::url('index.php?option=com_groups&task=browse'); ?>"><?php echo Lang::txt('MOD_MYGROUPS_ALL_GROUPS'); ?></a></p>
      			<?php }*/ ?>
      </ul>
      	<?php } ?>
      </div>
  </div>
</li>
