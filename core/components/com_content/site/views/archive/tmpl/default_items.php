<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// no direct access
defined('_HZEXEC_') or die();

Html::addIncludePath(PATH_COMPONENT . '/helpers');
$params = &$this->params;
?>

<ul id="archive-items">
    <?php foreach ($this->items as $i => $item) : ?>
        <li class="row<?php echo $i % 2; ?>">
            <h3>
                <?php if ($params->get('link_titles')) : ?>
                    <?php
                    $articleUrl = Route::url(
                        \Components\Content\Site\Helpers\Route::getArticleRoute(
                            $item->slug,
                            $item->catslug,
                            $item->language
                        )
                    );
                    ?>
                    <a href="<?php echo $articleUrl; ?>">
                        <?php echo $this->escape($item->title); ?>
                    </a>
                <?php else : ?>
                    <?php echo $this->escape($item->title); ?>
                <?php endif; ?>
            </h3>

            <?php if (
                $params->get('show_author')
                or $params->get('show_parent_category')
                or $params->get('show_category')
                or $params->get('show_create_date')
                or $params->get('show_modify_date')
                or $params->get('show_publish_date')
                or $params->get('show_hits')
) : ?>
                <dl class="article-info">
                    <dt class="article-info-term"><?php echo Lang::txt('COM_CONTENT_ARTICLE_INFO'); ?></dt>
            <?php endif; ?>
                <?php if ($params->get('show_parent_category')) : ?>
                    <dd class="parent-category-name">
                        <?php
                        $title = $this->escape($item->parent_title);
                        $catUrl = Route::url(
                            \Components\Content\Site\Helpers\Route::getCategoryRoute(
                                $item->parent_slug
                            )
                        );
                        $url = '<a href="' . $catUrl . '">'
                            . $title . '</a>';
                        ?>
                        <?php if ($params->get('link_parent_category') && $item->parent_slug) : ?>
                            <?php echo Lang::txt('COM_CONTENT_PARENT', $url); ?>
                        <?php else : ?>
                            <?php echo Lang::txt('COM_CONTENT_PARENT', $title); ?>
                        <?php endif; ?>
                    </dd>
                <?php endif; ?>
                <?php if ($params->get('show_category')) : ?>
                    <dd class="category-name">
                        <?php
                        $title = $this->escape($item->category_title);
                        $catUrl = Route::url(
                            \Components\Content\Site\Helpers\Route::getCategoryRoute(
                                $item->catslug
                            )
                        );
                        $url = '<a href="' . $catUrl . '">'
                            . $title . '</a>';
                        ?>
                        <?php if ($params->get('link_category') && $item->catslug) : ?>
                            <?php echo Lang::txt('COM_CONTENT_CATEGORY', $url); ?>
                        <?php else : ?>
                            <?php echo Lang::txt('COM_CONTENT_CATEGORY', $title); ?>
                        <?php endif; ?>
                    </dd>
                <?php endif; ?>
                <?php if ($params->get('show_create_date')) : ?>
                    <dd class="create">
                        <?php
                        $dateStr = Date::of($item->created)->toLocal(Lang::txt('DATE_FORMAT_LC2'));
                        echo Lang::txt('COM_CONTENT_CREATED_DATE_ON', $dateStr);
                        ?>
                    </dd>
                <?php endif; ?>
                <?php if ($params->get('show_modify_date')) : ?>
                    <dd class="modified">
                        <?php
                        $dateStr = Date::of($item->modified)->toLocal(Lang::txt('DATE_FORMAT_LC2'));
                        echo Lang::txt('COM_CONTENT_LAST_UPDATED', $dateStr);
                        ?>
                    </dd>
                <?php endif; ?>
                <?php if ($params->get('show_publish_date')) : ?>
                    <dd class="published">
                        <?php
                        $dateStr = Date::of($item->publish_up)->toLocal(Lang::txt('DATE_FORMAT_LC2'));
                        echo Lang::txt('COM_CONTENT_PUBLISHED_DATE_ON', $dateStr);
                        ?>
                    </dd>
                <?php endif; ?>
                <?php if ($params->get('show_author') && !empty($item->author)) : ?>
                    <dd class="createdby">
                        <?php $author = $item->author; ?>
                        <?php $author = ($item->created_by_alias ? $item->created_by_alias : $author); ?>

                        <?php if (!empty($item->contactid) &&  $params->get('link_author') == true) : ?>
                            <?php
                            $contactUrl = Route::url(
                                'index.php?option=com_contact&view=contact&id='
                                . $item->contactid
                            );
                            $authorLink = '<a href="' . $contactUrl . '">'
                                . $author . '</a>';
                            echo Lang::txt('COM_CONTENT_WRITTEN_BY', $authorLink);
                            ?>
                        <?php else : ?>
                            <?php echo Lang::txt('COM_CONTENT_WRITTEN_BY', $author); ?>
                        <?php endif; ?>
                    </dd>
                <?php endif; ?>
                <?php if ($params->get('show_hits')) : ?>
                    <dd class="hits">
                        <?php echo Lang::txt('COM_CONTENT_ARTICLE_HITS', $item->hits); ?>
                    </dd>
                <?php endif; ?>
            <?php if (
                $params->get('show_author')
                or $params->get('show_category')
                or $params->get('show_create_date')
                or $params->get('show_modify_date')
                or $params->get('show_publish_date')
                or $params->get('show_hits')
) : ?>
                </dl>
            <?php endif; ?>

            <?php if ($params->get('show_intro')) : ?>
                <div class="intro">
                    <?php echo Hubzero\Utility\Str::truncate($item->introtext, $params->get('introtext_limit')); ?>
                </div>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>

<div class="pagination">
    <?php echo $this->pagination->render(); ?>
</div>
