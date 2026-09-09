/**
 * Blog media manager — file list selection logic.
 *
 * Handles click delegation on .file-row elements inside the listing
 * iframe, highlighting the selected row and passing file metadata
 * up to the parent frame via window.parent.showFileDetail().
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
(function () {
  'use strict';

  var selected = null;

  document.addEventListener('click', function (e) {
    var row = e.target.closest('.file-row');
    if (!row) return;
    if (selected) selected.style.background = '';
    selected = row;
    row.style.background = 'color-mix(in srgb, var(--color-primary) 10%, transparent)';
    if (window.parent && typeof window.parent.showFileDetail === 'function') {
      window.parent.showFileDetail({
        filename: row.dataset.filename,
        ext: row.dataset.ext,
        size: row.dataset.size,
        date: row.dataset.date,
        ref: row.dataset.ref,
        isImage: row.dataset.isImage === '1',
        deleteUrl: row.dataset.deleteUrl,
        confirm: row.dataset.confirm
      });
    }
  });
})();
