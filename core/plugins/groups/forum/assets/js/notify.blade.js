/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
'use strict';

class Notify {
    static error(message) {
        this._notification(message, 'alert-error');
    }

    static success(message) {
        this._notification(message, 'alert-success');
    }

    static warn(message) {
        this._notification(message, 'alert-warning');
    }

    static _notification(message, alertClass) {
        var el = document.createElement('div');
        el.className = 'alert ' + alertClass + ' fixed top-4 right-4 z-[50000] max-w-md shadow-lg';
        el.setAttribute('role', 'alert');
        el.innerHTML = '<span>' + message + '</span>'
            + '<button class="btn btn-ghost btn-xs" data-dismiss="alert">\u00d7</button>';

        el.querySelector('[data-dismiss="alert"]').addEventListener('click', function () {
            el.remove();
        });

        document.body.appendChild(el);

        setTimeout(function () {
            if (el.parentNode) {
                el.remove();
            }
        }, 5000);
    }
}
