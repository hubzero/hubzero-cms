<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>

<?php if ($this->params->get('access-view-comment')) { ?>
    <section class="below section">
        <div class="section-inner">
            <div class="subject thread container">

                <?php if ($this->params->get('access-create-comment')) { ?>
                    <h3 class="post-comment-title" id="post-comment">
                        <?php echo Lang::txt('PLG_HUBZERO_COMMENTS_POST_A_COMMENT'); ?>
                    </h3>
                    <form method="post"
                        action="<?php echo Route::url($this->url); ?>"
                        id="commentform"
                        enctype="multipart/form-data">
                        <p class="comment-member-photo">
                            <?php
                            $edit = 0;
                            // Make sure editing capabilities
                            // are available before accepting
                            // an ID to edit
                            $canEdit = $this->params->get(
                                'access-edit-comment'
                            );
                            $canManage = $this->params->get(
                                'access-manage-comment'
                            );
                            if ($canEdit || $canManage) {
                                $edit = Request::getInt(
                                    'commentedit',
                                    0
                                );
                            }

                            // Load the comment
                            $commentClass = '\Plugins\Hubzero'
                                . '\Comments\Models\Comment';
                            $comment = $commentClass::oneOrNew(
                                $edit
                            );
                            // If the comment exists and the
                            // editor is NOT the creator and
                            // the editor is NOT a manager...
                            $isCreator = $comment->get('created_by')
                                == User::get('id');
                            if (
                                $comment->get('id')
                                && !$isCreator
                                && !$canManage
                            ) {
                                // Disallow editing
                                $comment = $commentClass::blank();
                            }

                            if ($comment->isNew()) {
                                $comment->set('parent', Request::getInt('commentreply', 0));
                                $comment->set('created_by', (!User::isGuest() ? User::get('id') : 0));
                                $comment->set('anonymous', (!User::isGuest() ? 0 : 1));
                            }
                            $avatarSrc = $comment->creator->picture(
                                $comment->get('anonymous')
                            );
                            ?>
                            <img src="<?php echo $avatarSrc; ?>"
                                alt="" />
                        </p>
                        <fieldset>
                            <?php
                            if (!User::isGuest()) {
                                $replyto = Request::getInt(
                                    'commentreply',
                                    0
                                );
                                if ($replyto) {
                                    $reply = $commentClass::oneOrNew(
                                        $replyto
                                    );

                                    $name = Lang::txt(
                                        'COM_KB_ANONYMOUS'
                                    );
                                    if (!$reply->get('anonymous')) {
                                        $name = $this->escape(
                                            stripslashes(
                                                $repy->creator->get('name')
                                            )
                                        );
                                        $viewLevels = User::getAuthorisedViewLevels();
                                        $replyAccess = $reply->creator->get(
                                            'access'
                                        );
                                        if (in_array($replyAccess, $viewLevels)) {
                                            $name = '<a href="'
                                                . Route::url($reply->creator->link())
                                                . '">'
                                                . $name
                                                . '</a>';
                                        }
                                    }
                                    ?>
                                    <?php
                                    $replyDatetime = $reply->created();
                                    $replyTime = $reply->created('time');
                                    $replyDate = $reply->created('date');
                                    $atTxt = Lang::txt('COM_ANSWERS_AT');
                                    $onTxt = Lang::txt('COM_ANSWERS_ON');
                                    ?>
                                    <blockquote cite="c<?php echo $reply->get('id'); ?>">
                                        <p>
                                            <strong><?php echo $name; ?></strong>
                                            <span class="comment-date-at">
                                                <?php echo $atTxt; ?>
                                            </span>
                                            <span class="time">
                                                <time datetime="<?php echo $replyDatetime; ?>">
                                                    <?php echo $replyTime; ?>
                                                </time>
                                            </span>
                                            <span class="comment-date-on">
                                                <?php echo $onTxt; ?>
                                            </span>
                                            <span class="date">
                                                <time datetime="<?php echo $replyDatetime; ?>">
                                                    <?php echo $replyDate; ?>
                                                </time>
                                            </span>
                                        </p>
                                        <p><?php echo $reply->content; ?></p>
                                    </blockquote>
                                    <?php
                                }
                            }
                            ?>
                            <div class="form-group">
                                <label for="commentcontent">
                                    <?php echo Lang::txt('PLG_HUBZERO_COMMENTS_YOUR_COMMENTS'); ?>:
                                    <?php
                                    if (!User::isGuest()) {
                                        $editorContent = $this->escape(
                                            $comment->get('content')
                                        );
                                        $editorAttrs = array(
                                            'class' => 'form-control'
                                                . ' minimal no-footer',
                                        );
                                        echo $this->editor(
                                            'comment[content]',
                                            $editorContent,
                                            35,
                                            15,
                                            'commentcontent',
                                            $editorAttrs
                                        );
                                    }
                                    ?>
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="comment_file">
                                    <?php echo Lang::txt('PLG_HUBZERO_COMMENTS_ATTACH_FILE'); ?>
                                    <input type="file"
                                        class="form-control-file"
                                        name="comment_file"
                                        id="comment_file"/>
                                </label>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <label id="comment-anonymous-label" class="form-check-label">
                                        <input class="option form-check-input"
                                            type="checkbox"
                                            name="comment[anonymous]"
                                            id="comment-anonymous"
                                            value="1"<?php if ($comment->get('anonymous')) {
                                                echo ' checked="checked"';
                                                     } ?> />
                                        <?php echo Lang::txt('PLG_HUBZERO_COMMENTS_POST_ANONYMOUSLY'); ?>
                                    </label>
                                </div>
                            </div>

                            <p class="submit">
                                <input type="submit"
                                    class="btn btn-success"
                                    name="submit"
                                    value="<?php echo Lang::txt('PLG_HUBZERO_COMMENTS_POST_COMMENT'); ?>"/>
                            </p>

                            <input type="hidden" name="comment[id]" value="<?php echo $comment->get('id'); ?>" />
                            <input type="hidden" name="comment[item_id]" value="<?php echo $this->obj_id; ?>" />
                            <input type="hidden" name="comment[item_type]" value="<?php echo $this->obj_type; ?>" />
                            <input type="hidden"
                                name="comment[parent]"
                                value="<?php echo $comment->get('parent'); ?>"/>
                            <input type="hidden"
                                name="comment[created_by]"
                                value="<?php echo $comment->get('created_by'); ?>"/>
                            <input type="hidden"
                                name="comment[state]"
                                value="<?php echo $comment->get('state', 1); ?>"/>
                            <input type="hidden" name="comment[access]" value="1" />
                            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                            <input type="hidden" name="action" value="commentsave" />

                            <?php echo Html::input('token'); ?>

                            <div class="sidenote">
                                <p>
                                    <strong><?php echo Lang::txt('PLG_HUBZERO_COMMENTS_KEEP_RELEVANT'); ?></strong>
                                </p>
                            </div>
                        </fieldset>
                    </form>
                <?php } ?>

                <?php if ($this->params->get('comments_locked', 0) == 1) { ?>
                    <p class="info"><?php echo Lang::txt('PLG_HUBZERO_COMMENTS_SORTING_ASC'); ?></p>
                <?php } ?>

                <h3 class="post-comment-title">
                    <?php echo Lang::txt('PLG_HUBZERO_COMMENTS'); ?>
                </h3>

                <?php
                if ($this->comments->count() > 1) :
                    $stateKey = 'Plugins.Hubzero.Comments'
                        . '.Models.Comment.orderdir';
                    $defaultDir = $commentClass::blank()
                        ->orderDir;
                    $currentOrderDir = \Hubzero\Facades\User::getState(
                        $stateKey,
                        $defaultDir
                    );
                    ?>
                <nav class="entries-filters" aria-label="<?php echo Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS'); ?>">
                    <form method="post" action="<?php echo Route::url($this->url); ?>" id="commentformcomments">
                    <?php
                    $showTxt = Lang::txt(
                        'PLG_HUBZERO_COMMENTS_SHOW'
                    );
                    $descTxt = Lang::txt(
                        'PLG_HUBZERO_COMMENTS_SORTING_DESC'
                    );
                    $ascTxt = Lang::txt(
                        'PLG_HUBZERO_COMMENTS_SORTING_ASC'
                    );
                    ?>
                    <ul class="entries-menu sort-options">
                        <?php if ($currentOrderDir == 'asc') : ?>
                            <li>
                                <button type="submit"
                                    name="orderdir"
                                    value="desc"
                                    class="as-link"
                                ><?php echo $showTxt . ' ' . $descTxt; ?></button>
                            </li>
                        <?php else : ?>
                            <li>
                                <button type="submit"
                                    name="orderdir"
                                    value="asc"
                                    class="as-link"
                                ><?php echo $showTxt . ' ' . $ascTxt; ?></button>
                            </li>
                        <?php endif; ?>
                    </ul>
                    </form>
                </nav>

                <?php endif; ?>

                <?php if ($this->comments->count()) {
                    $this->view('list')
                         ->set('option', $this->option)
                         ->set('comments', $this->comments)
                         ->set('obj_type', $this->obj_type)
                         ->set('obj_id', $this->obj_id)
                         ->set('obj', $this->obj)
                         ->set('params', $this->params)
                         ->set('depth', $this->depth)
                         ->set('url', $this->url)
                         ->set('cls', 'odd')
                         ->display();
                } elseif ($this->depth <= 1) { ?>
                    <p class="no-comments">
                        <?php echo Lang::txt('PLG_HUBZERO_COMMENTS_NO_COMMENTS'); ?>
                    </p>
                <?php } ?>

            </div><!-- / .subject -->
            <div class="aside">
            </div><!-- / .aside -->
        </div>
    </section><!-- / .below section -->
<?php } else { ?>
    <p class="warning">
        <?php echo Lang::txt('PLG_HUBZERO_COMMENTS_MUST_BE_LOGGED_IN'); ?>
    </p>
<?php }
