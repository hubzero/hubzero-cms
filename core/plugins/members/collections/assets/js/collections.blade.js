/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 *
 * Collections plugin - daisyUI compatible JS
 * Replaces fancybox with native <dialog> elements
 */

String.prototype.nohtml = function () {
    if (this.indexOf('?') == -1) {
        return this + '?no_html=1';
    } else {
        return this + '&no_html=1';
    }
};

jQuery(document).ready(function (jq) {
    var $ = jq,
        container = $('#posts'),
        isActive = true;

    // Ensure dialogs exist, create them if not
    function ensureDialog(id, cssClass, maxWidth) {
        var dialog = document.getElementById(id);
        if (!dialog) {
            dialog = document.createElement('dialog');
            dialog.id = id;
            dialog.className = 'collections-dialog ' + (cssClass || '');
            if (maxWidth) {
                dialog.style.maxWidth = maxWidth;
            }
            dialog.innerHTML = '<button class="dialog-close" aria-label="Close">\u00D7</button><div class="dialog-body"></div>';
            document.body.appendChild(dialog);

            // Close on backdrop click
            dialog.addEventListener('click', function (e) {
                if (e.target === dialog) {
                    dialog.close();
                }
            });

            // Close button
            dialog.querySelector('.dialog-close').addEventListener('click', function () {
                dialog.close();
            });
        }
        return dialog;
    }

    // Lightbox dialog for images
    function ensureLightboxDialog() {
        var dialog = document.getElementById('lightbox-dialog');
        if (!dialog) {
            dialog = document.createElement('dialog');
            dialog.id = 'lightbox-dialog';
            dialog.className = 'lightbox-dialog';
            dialog.innerHTML = '<img src="" alt="" /><div class="lightbox-caption"></div>';
            document.body.appendChild(dialog);

            dialog.addEventListener('click', function (e) {
                if (e.target === dialog || e.target.tagName === 'IMG') {
                    dialog.close();
                }
            });
        }
        return dialog;
    }

    if (container.length > 0) {
        // Image lightbox - replace fancybox
        $(document).on('click', 'a.img-link[data-rel]', function (e) {
            e.preventDefault();
            var dialog = ensureLightboxDialog();
            var img = dialog.querySelector('img');
            var caption = dialog.querySelector('.lightbox-caption');

            img.src = this.href;
            img.alt = $(this).find('img').attr('alt') || '';

            var downloadUrl = $(this).attr('data-download');
            var downloadText = $(this).attr('data-downloadtext') || 'Download';
            caption.innerHTML = downloadUrl
                ? '<a href="' + downloadUrl + '" download>' + downloadText + '</a>'
                : '';

            dialog.showModal();
        });

        // Check if list view is selected
        var opts = $('.view-options a');
        opts.each(function () {
            if ($(this).hasClass('selected') && $(this).hasClass('icon-list')) {
                isActive = false;
            }
        });

        // Masonry
        if (isActive && $.fn.masonry) {
            container.masonry({
                itemSelector: '.post'
            });
        }

        // Sortable for reordering
        if (jQuery.ui && jQuery.ui.sortable) {
            $('#posts').sortable({
                handle: '.sort-handle',
                items: 'div.post:not(.new-post)',
                update: function () {
                    var col = $('#posts').sortable('serialize');
                    $.getJSON($('#posts').attr('data-update').nohtml() + '&' + col);
                }
            });
        }

        // Infinite scroll
        if ($.fn.infinitescroll) {
            container.infinitescroll(
                {
                    navSelector: '.list-footer',
                    nextSelector: '.list-footer .next a',
                    itemSelector: '#posts div.post',
                    loading: {
                        finishedMsg: 'No more pages to load.',
                        img: container.attr('data-base') + '/core/components/com_collections/assets/img/spinner.gif'
                    },
                    path: function () {
                        var path = $('.list-footer .next a').attr('href');
                        var limit = path.match(/limit[-=]([0-9]*)/).slice(1);
                        return path.replace(/start[-=]([0-9]*)/, 'no_html=1&start=' + (limit * arguments[0] - limit));
                    },
                    debug: false
                },
                function (newElements) {
                    var $newElems = $(newElements).css({ opacity: 0 });
                    $newElems.animate({ opacity: 1 });
                    if (isActive && $.fn.masonry) {
                        container.masonry('appended', $newElems, true);
                    }
                }
            );
        }

        if (container.hasClass('loggedin')) {
            // Vote (like/unlike)
            container.on('click', 'a.vote', function (e) {
                e.preventDefault();
                var el = $(this);
                $.get(el.attr('href').nohtml(), {}, function (data) {
                    var like = el.attr('data-text-like'),
                        unlike = el.attr('data-text-unlike');

                    if (el.children('span').text() == like) {
                        el.removeClass('like').addClass('unlike').children('span').text(unlike);
                    } else {
                        el.removeClass('unlike').addClass('like').children('span').text(like);
                    }
                    $('#post_' + el.attr('data-id') + ' .likes').text(data);
                });
            });

            // Collect/Repost - open in dialog
            $(document).on('click', '#page_content a.repost', function (e) {
                e.preventDefault();
                var el = $(this);
                var dialog = ensureDialog('repost-dialog', '', '500px');
                var body = dialog.querySelector('.dialog-body');

                $.get(el.attr('href').nohtml(), function (html) {
                    body.innerHTML = html;
                    dialog.showModal();
                    $(document).trigger('ajaxLoad');

                    // Handle form submission
                    $(body).find('#hubForm').on('submit', function (ev) {
                        ev.preventDefault();
                        $.post($(this).attr('action'), $(this).serialize(), function (data) {
                            $('#b' + el.attr('data-id') + ' .reposts').text(data);
                            dialog.close();
                        });
                    });
                });
            });

            // Delete - open in dialog
            container.on('click', 'a.delete', function (e) {
                e.preventDefault();
                var el = $(this);
                var dialog = ensureDialog('delete-dialog', '', '400px');
                var body = dialog.querySelector('.dialog-body');

                $.get(el.attr('href').nohtml(), function (html) {
                    body.innerHTML = html;
                    dialog.showModal();

                    $(body).find('#hubForm').on('submit', function (ev) {
                        ev.preventDefault();
                        $.post($(this).attr('action'), $(this).serialize(), function (data) {
                            dialog.close();
                            if (data) {
                                window.location = data;
                            }
                        });
                    });
                });
            });
        }

        // Comment - open post detail in dialog
        container.on('click', 'a.comment', function (e) {
            e.preventDefault();
            var el = $(this);
            var dialog = ensureDialog('comment-dialog', 'post-detail-dialog', '800px');
            var body = dialog.querySelector('.dialog-body');

            $.get(el.attr('href').nohtml(), function (html) {
                body.innerHTML = html;
                dialog.showModal();
                $(document).trigger('ajaxLoad');

                // Handle comment form submission
                $(body).on('submit', '#comment-form', function (ev) {
                    ev.preventDefault();
                    $.post($(this).attr('action'), $(this).serialize(), function (data) {
                        body.innerHTML = data;

                        // Update metadata counts
                        var metadata = el.closest('.meta');
                        if (metadata.length && metadata.attr('data-metadata-url')) {
                            $.getJSON(metadata.attr('data-metadata-url').nohtml(), function (counts) {
                                metadata.find('.likes').text(counts.likes);
                                metadata.find('.comments').text(counts.comments);
                                metadata.find('.reposts').text(counts.reposts);
                            });
                        }
                    });
                });
            });
        });
    }

    // Follow/unfollow
    $(document).on('click', '#page_content a.follow, #page_content a.unfollow', function (e) {
        e.preventDefault();
        var el = $(this);

        $.getJSON(el.attr('href').nohtml(), {}, function (data) {
            if (data.success) {
                var follow = el.attr('data-text-follow'),
                    unfollow = el.attr('data-text-unfollow');

                if (el.children('span').text() == follow) {
                    el.removeClass('follow').addClass('unfollow')
                        .attr('href', data.href)
                        .children('span').text(unfollow);
                    if (el.hasClass('icon-follow')) {
                        el.removeClass('icon-follow').addClass('icon-unfollow');
                    }
                } else {
                    el.removeClass('unfollow').addClass('follow')
                        .attr('href', data.href)
                        .children('span').text(follow);
                    if (el.hasClass('icon-unfollow')) {
                        el.removeClass('icon-unfollow').addClass('icon-follow');
                    }
                }
            }
        });
    });

    // Asset list sorting in edit forms
    if ($.fn.sortable) {
        $('#ajax-uploader-list').sortable({
            handle: '.asset-handle'
        });
    }
});
