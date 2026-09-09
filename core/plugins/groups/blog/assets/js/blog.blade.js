/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function () {
    // Initialize flatpickr on date/time inputs
    document.querySelectorAll('[data-flatpickr="datetime"]').forEach(function (el) {
        if (typeof flatpickr === 'undefined') {
            return;
        }
        flatpickr(el, {
            enableTime: true,
            time_24hr: true,
            dateFormat: 'Y-m-d H:i:S',
            allowInput: true
        });
    });

    // Toggle reply forms
    document.querySelectorAll('.below').forEach(function (container) {
        container.addEventListener('click', function (e) {
            var link = e.target.closest('a.reply');
            if (!link) {
                return;
            }
            e.preventDefault();

            var frm = document.getElementById(link.getAttribute('rel'));
            if (!frm) {
                return;
            }

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

        // Confirm delete
        container.addEventListener('click', function (e) {
            var link = e.target.closest('a.delete');
            if (!link) {
                return;
            }
            if (!confirm(link.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        });
    });
});
