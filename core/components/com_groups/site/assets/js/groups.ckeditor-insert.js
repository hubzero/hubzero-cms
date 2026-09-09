/**
 * CKEditor file insert handler for group media browser.
 *
 * Extracted from inline script in filelist.php for CSP compliance.
 * Delegates to HUB.GroupsMediaList.ckeditorInsert().
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-ckeditor-insert]');
    if (!btn) {
        return;
    }
    e.preventDefault();
    var file   = btn.getAttribute('data-ckeditor-insert');
    var opener = window.parent;
    if (typeof HUB !== 'undefined' && HUB.GroupsMediaList) {
        HUB.GroupsMediaList.ckeditorInsert(file, opener);
    }
});
