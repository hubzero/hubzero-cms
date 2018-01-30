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
?>

<div class="card [ is-collapsed ]">
    <div class="card__inner [ js-expander ]">
		<img src="<?php echo 'app/site/media/images/partners/' . $this->record->get('logo_img') ?>" alt="<?php echo $this->record->get('name'); ?>" class="card-logo">
    </div>
	<div class="card__expander">
	   <i class="fa fa-close [ js-collapser ]" aria-hidden="true"></i>
	   <div class="inner-expander">
	       <?php echo 'About: ' . '<p>' . $this->record->get('about') . '</p>'; ?>
            <div class="liason">
            Partner Liaison:
			 <p><?php echo $this->record->get('partner_liason_primary'); ?></p>
            </div>
            <div class="liason">
			QUBES Liaison:
			 <p><?php echo $this->record->get('QUBES_liason_primary'); ?></p>
            </div>
	   </div>
	   <div class="inner-expander">
		<?php echo 'Activities: ' . '<p>' . $this->record->get('activities') . '</p>'; ?>
            <div class="social">
			 <a class="card-link" href="<?php echo Route::url('groups' . DS . $this->record->get('groups_cn')); ?>">Learn more</a><br>
			 <a class="social-icon" href="https://twitter.com/<?php echo $this->record->get('twitter_handle'); ?>" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i></a>
            </div>
	   </div>
    </div>
</div>