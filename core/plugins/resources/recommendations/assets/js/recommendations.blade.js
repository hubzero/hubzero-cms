/**
 * Recommendations plugin — Blade/daisyUI JavaScript.
 *
 * Replaces recommendations.js when using Blade templates. Vanilla JS, CSP-safe.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function () {

  var sbjt = document.getElementById('recommendations-subject');
  if (!sbjt) return;

  var rid = document.getElementById('rid');
  if (!rid) return;

  var src = sbjt.getAttribute('data-src') || '';
  var url = src + '/index.php?option=com_resources&task=plugin&trigger=onResourcesRecoms&no_html=1&rid=' + rid.value;

  fetch(url)
    .then(function (r) { return r.text(); })
    .then(function (html) {
      sbjt.innerHTML = html;
    });

});
