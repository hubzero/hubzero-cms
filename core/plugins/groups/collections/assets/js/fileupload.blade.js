/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
'use strict';

document.addEventListener('DOMContentLoaded', function () {
    var attach = document.getElementById('ajax-uploader');
    if (!attach) {
        return;
    }

    var list = document.getElementById('ajax-uploader-list');
    var linkr = document.getElementById('link-adder');
    var dirField = document.getElementById('field-dir');
    var idField = document.getElementById('field-id');

    // Delete asset handler
    if (list) {
        list.addEventListener('click', function (e) {
            var del = e.target.closest('a.delete, .btn-error');
            if (!del) {
                return;
            }
            e.preventDefault();
            if (del.getAttribute('data-id')) {
                fetch(del.getAttribute('href'));
            }
            var row = del.closest('.flex, .item-asset, p');
            if (row) {
                row.remove();
            }
        });
    }

    // Link adder
    if (linkr) {
        var linkerBtn = document.createElement('div');
        linkerBtn.className = 'linker';
        linkerBtn.innerHTML = '<div class="linker-button btn btn-ghost btn-sm gap-2">'
            + '<span>' + linkr.getAttribute('data-txt-instructions') + '</span></div>';
        linkr.appendChild(linkerBtn);

        linkerBtn.addEventListener('click', function () {
            var i = document.querySelectorAll('.item-asset').length + 1000;
            var row = document.createElement('div');
            row.className = 'flex items-center gap-2 mb-2 p-2 bg-base-200 rounded-box item-asset';
            row.innerHTML = '<span class="flex-1">'
                + '<input type="text" name="assets[' + i + '][filename]" '
                + 'class="input input-bordered input-sm w-full" value="http://" placeholder="http://" />'
                + '</span>'
                + '<input type="hidden" name="assets[' + i + '][type]" value="link" />'
                + '<input type="hidden" name="assets[' + i + '][id]" value="0" />'
                + '<a class="btn btn-error btn-xs btn-outline" '
                + 'href="' + linkr.getAttribute('data-base') + '/collections/delete/asset/" '
                + 'data-id="" title="' + linkr.getAttribute('data-txt-delete') + '">'
                + linkr.getAttribute('data-txt-delete') + '</a>';
            if (list) {
                list.appendChild(row);
            }
        });
    }

    // File uploader
    if (typeof qq !== 'undefined') {
        var running = 0;

        var uploader = new qq.FileUploader({
            element: attach,
            action: attach.getAttribute('data-action'),
            params: {
                dir: dirField ? dirField.value : '',
                i: document.querySelectorAll('.item-asset').length
            },
            multiple: true,
            debug: false,
            template: '<div class="qq-uploader">'
                + '<div class="qq-upload-button"><span>'
                + attach.getAttribute('data-txt-instructions') + '</span></div>'
                + '<div class="qq-upload-drop-area"><span>'
                + attach.getAttribute('data-txt-instructions') + '</span></div>'
                + '<ul class="qq-upload-list"></ul>'
                + '</div>',
            onSubmit: function () {
                running++;
            },
            onComplete: function (id, file, response) {
                running--;

                if (dirField && response.id != dirField.value) {
                    if (idField) {
                        idField.value = response.id;
                    }
                    dirField.value = response.id;
                    uploader.setParams({ dir: dirField.value });
                }

                var html = response.html;
                html = html.replace(/&gt;/g, '>');
                html = html.replace(/&lt;/g, '<');

                if (list) {
                    list.insertAdjacentHTML('beforeend', html);
                }

                if (running === 0) {
                    var ul = document.querySelector('ul.qq-upload-list');
                    if (ul) {
                        ul.innerHTML = '';
                    }
                }
            }
        });
    }
});
