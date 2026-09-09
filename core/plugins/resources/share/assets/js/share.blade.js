/**
 * Share plugin — Blade/daisyUI JavaScript.
 *
 * Replaces share.js when using Blade templates. Vanilla JS, CSP-safe.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function () {

  var shareSection = document.querySelector('.share');
  var shareInfo = document.querySelector('.shareinfo');

  if (shareSection && shareInfo) {
    shareSection.addEventListener('mouseenter', function () {
      shareInfo.classList.add('active');
    });
    shareSection.addEventListener('mouseleave', function () {
      shareInfo.classList.remove('active');
    });
  }

});
