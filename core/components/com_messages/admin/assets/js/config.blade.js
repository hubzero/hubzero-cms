/**
 * com_messages config popup — Blade CSP-safe version
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
document.addEventListener('DOMContentLoaded', function () {
  var cancel = document.getElementById('cfg-cancel');
  if (cancel) {
    cancel.addEventListener('click', function () {
      if (window.parent && window.parent.$ && window.parent.$.fancybox) {
        window.parent.$.fancybox.close();
      }
    });
  }
});
