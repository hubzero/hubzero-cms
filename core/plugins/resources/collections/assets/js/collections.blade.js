/**
 * Collections plugin — Blade/daisyUI JavaScript.
 *
 * Replaces collections.js when using Blade templates. Vanilla JS, CSP-safe.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function () {

  // Collection form — toggle new series field based on select
  document.addEventListener('change', function (e) {
    if (!e.target.matches('#collectionForm #pid')) return;

    var selected = e.target.value;
    var newSeries = document.getElementById('new-series-add');
    if (!newSeries) return;

    if (selected.length) {
      newSeries.style.display = 'none';
      var titleInput = document.querySelector('input[name="resource-title"]');
      if (titleInput) titleInput.value = '';
    } else {
      newSeries.style.display = '';
    }
  });

  // Collection form AJAX submit
  document.addEventListener('submit', function (e) {
    var frm = e.target.closest('#collectionForm');
    if (!frm) return;
    e.preventDefault();

    fetch(frm.action, {
      method: 'POST',
      body: new URLSearchParams(new FormData(frm)).toString() + '&no_html=1',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        var parent = frm.parentNode;
        var cls = data.success ? 'text-success' : 'text-error';
        parent.innerHTML = '<p class="' + cls + ' p-4">' + data.message + '</p>';
        setTimeout(function () { location.reload(); }, 2000);
      });
  });

});
