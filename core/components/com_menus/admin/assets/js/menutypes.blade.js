/**
 * com_menus — menu type picker (tmpl=component iframe)
 *
 * Handles:
 *  - Live search filter across type groups
 *  - Menu type selection (data-menutype links)
 *  - Picker close button (data-picker-close)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
(function () {
  'use strict';

  function closePopup() {
    if (window.parent && window.parent.$ && window.parent.$.fancybox) {
      window.parent.$.fancybox.close();
    }
  }

  document.addEventListener('DOMContentLoaded', function () {

    /* ── Live search filter ───────────────────────────────── */
    var input  = document.getElementById('type-search');
    var groups = document.querySelectorAll('details.type-group');

    if (input) {
      input.addEventListener('input', function () {
        var q = this.value.trim().toLowerCase();

        groups.forEach(function (group) {
          var items   = group.querySelectorAll('.type-item');
          var visible = 0;

          items.forEach(function (item) {
            var match = !q || item.textContent.toLowerCase().includes(q);
            item.style.display = match ? '' : 'none';
            if (match) visible++;
          });

          if (!q) {
            group.removeAttribute('open');
            group.style.display = '';
          } else if (visible > 0) {
            group.setAttribute('open', '');
            group.style.display = '';
          } else {
            group.style.display = 'none';
          }
        });
      });

      input.focus();
    }

    /* ── Menu type selection ──────────────────────────────── */
    document.addEventListener('click', function (e) {
      var link = e.target.closest('[data-menutype]');
      if (!link) return;
      e.preventDefault();

      var payload = link.getAttribute('data-menutype');
      if (window.parent && window.parent.Hubzero) {
        window.parent.Hubzero.submitbutton('items.setType', payload);
      }
      closePopup();
    });

    /* ── Close button ─────────────────────────────────────── */
    document.addEventListener('click', function (e) {
      if (e.target.closest('[data-picker-close]')) {
        e.preventDefault();
        closePopup();
      }
    });

  });
})();
