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

// No direct access
defined('_HZEXEC_') or die();
$this->id = ($this->comment ? $this->comment->get('id') : 0);
?>

<?php if ($this->context != 'new'):?>
<div class="addcomment hide" id="<?php echo $this->context; ?>-form<?php echo $this->id; ?>">
<?php endif;?>
    <form action="<?php echo $this->url; ?>" method="post" id="<?php echo ($this->context == 'new' ? 'commentform' : '')?>" enctype="multipart/form-data">
        <?php if ($this->context == 'new'): ?>
        <p class="comment-member-photo">
			<img src="<?php echo User::picture(); ?>" alt="" />
        </p>
        <?php endif;?>
        <fieldset>
            <legend>
                <span><?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_YOUR_' . strtoupper($this->context)); ?></span>
            </legend>

            <input type="hidden" name="comment[id]" value="<?php echo ($this->context != 'edit' ? 0 : $this->id); ?>" />
            <input type="hidden" name="comment[parent]" value="<?php echo ($this->context != 'edit' ? $this->id : $this->comment->get('parent')); ?>" />
            <input type="hidden" name="action" value="<?php echo $this->context; ?>" />
            <input type="hidden" name="no_html" value="1" />

            <?php echo Html::input('token'); ?>

            <label for="<?php echo $this->context; ?>_<?php echo $this->id; ?>_content">
                <?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_YOUR_' . strtoupper($this->context)); ?>:
                <?php
                if (!User::isGuest())
                {
                    echo $this->editor('comment[content]', ($this->context != 'edit' ? '' : $this->comment->get('content')), 35, 4, $this->context . '_' . $this->id . '_content', array('class' => 'minimal no-footer'));
                }
                ?>
            </label>

            <div class="file-inputs">
                <button class="btn btn-secondary <?php echo ($this->file ? 'detach-file' : 'attach-file')?>"></button>
                <input type="file" name="comment_file" id="<?php echo $this->context; ?>-<?php echo $this->id; ?>-file" style="display:none;" />
                <span><?php echo ($this->file ? $this->file : 'No attachment')?></span>
            </div>

            <?php if ($this->params->get('comments_anonymous')): ?>
            <label class="comment-anonymous-label" for="<?php echo $this->context; ?>-<?php echo $this->id; ?>-anonymous">
                <input class="option" type="checkbox" name="comment[anonymous]" id="<?php echo $this->context; ?>-<?php echo $this->id; ?>-anonymous" value="1" <?php echo (($this->context == 'edit') && $this->comment->get('anonymous') ? 'checked' : ''); ?>/>
                <?php echo ($this->context != 'edit' ? Lang::txt('PLG_PUBLICATIONS_COMMENTS_POST_COMMENT_ANONYMOUSLY') : Lang::txt('PLG_PUBLICATIONS_COMMENTS_MAKE_COMMENT_ANONYMOUS')); ?>
            </label>
            <?php endif; ?>

            <p class="submit">
                <input type="submit" value="<?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_POST_' . strtoupper($this->context)); ?>" />
            </p>
            <?php if ($this->context == 'new'):?>
            <div class="sidenote">
                <p>
                    <strong><?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_KEEP_RELEVANT'); ?></strong>
                </p>
            </div>
            <?php endif;?>
        </fieldset>
    </form>
<?php if ($this->context != 'new'):?>
</div><!-- / .addcomment -->
<?php endif;