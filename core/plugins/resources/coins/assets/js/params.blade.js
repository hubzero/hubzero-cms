/**
 * COinS plugin — Blade/daisyUI JavaScript.
 *
 * Replaces params.js when using Blade templates. Vanilla JS, CSP-safe.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function () {

  var tpl = document.getElementById('plg_resources_coins');
  if (!tpl) return;

  var container = tpl.parentElement;
  var btn = document.getElementById('add-resource-type');
  if (!btn) return;

  btn.addEventListener('click', function (e) {
    e.preventDefault();

    var index = container.querySelectorAll('.coinstypes').length;
    var source = tpl.innerHTML;
    var html = source.replace(/\{\{index\}\}/g, index);

    btn.insertAdjacentHTML('beforebegin', html);
  });

});
