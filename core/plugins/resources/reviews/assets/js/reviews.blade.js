/**
 * Reviews plugin — Blade/daisyUI JavaScript.
 *
 * Replaces reviews.js when using Blade templates. Uses vanilla JS
 * and data-attribute delegation (CSP-safe, no inline handlers).
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function () {

  // Delete confirmation
  document.addEventListener('click', function (e) {
    var link = e.target.closest('a.delete');
    if (!link) return;

    var msg = link.getAttribute('data-txt-confirm');
    if (msg && !confirm(msg)) {
      e.preventDefault();
    }
  });

  // Reply toggle
  document.addEventListener('click', function (e) {
    var link = e.target.closest('a.reply');
    if (!link) return;
    e.preventDefault();

    var rel = link.getAttribute('data-rel');
    var frm = document.getElementById(rel);
    if (!frm) return;

    if (frm.classList.contains('hide')) {
      frm.classList.remove('hide');
      link.classList.add('active');
      link.textContent = link.getAttribute('data-txt-active');
    } else {
      frm.classList.add('hide');
      link.classList.remove('active');
      link.textContent = link.getAttribute('data-txt-inactive');
    }
  });

  // Report abuse — open in modal dialog
  document.addEventListener('click', function (e) {
    var link = e.target.closest('a.abuse');
    if (!link) return;
    e.preventDefault();

    var href = link.getAttribute('href');
    var sep = href.indexOf('?') === -1 ? '?' : '&';
    var url = href + sep + 'no_html=1';

    // Use native dialog if available, otherwise navigate
    var dialog = document.createElement('dialog');
    if (typeof dialog.showModal !== 'function') {
      window.location = href;
      return;
    }

    dialog.className = 'modal modal-open';
    dialog.innerHTML = '<div class="modal-box"><div class="loading">Loading...</div></div>';
    document.body.appendChild(dialog);
    dialog.showModal();

    fetch(url)
      .then(function (r) { return r.text(); })
      .then(function (html) {
        var box = dialog.querySelector('.modal-box');
        box.innerHTML = html;

        var frm = box.querySelector('form');
        if (frm) {
          frm.addEventListener('submit', function (ev) {
            ev.preventDefault();
            fetch(frm.action, {
              method: 'POST',
              body: new FormData(frm)
            })
              .then(function (r) { return r.json(); })
              .then(function (data) {
                if (data.success) {
                  box.innerHTML = '<p class="text-success">' + data.message + '</p>';
                  var comment = document.getElementById('c' + data.id);
                  if (comment) {
                    var body = comment.querySelector('.comment-body');
                    if (body) {
                      body.innerHTML = '<p class="text-warning">' + link.getAttribute('data-txt-flagged') + '</p>';
                    }
                  }
                  setTimeout(function () { dialog.close(); dialog.remove(); }, 2000);
                } else {
                  box.insertAdjacentHTML('afterbegin', '<p class="text-error">' + data.message + '</p>');
                }
              });
          });
        }

        dialog.addEventListener('click', function (ev) {
          if (ev.target === dialog) {
            dialog.close();
            dialog.remove();
          }
        });
      });
  });

  // Vote buttons (AJAX)
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.vote-button');
    if (!btn) return;
    e.preventDefault();

    var href = btn.getAttribute('href');
    if (!href) return;

    var sep = href.indexOf('?') === -1 ? '?' : '&';
    fetch(href + sep + 'no_html=1')
      .then(function (r) { return r.text(); })
      .then(function (html) {
        var container = btn.closest('.voting');
        if (container) {
          container.innerHTML = html;
        }
      });
  });

});
