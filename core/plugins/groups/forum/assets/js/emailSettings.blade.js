/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
'use strict';

document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('email-settings');
    var preexistingEl = document.getElementById('preexisting-subscriptions');

    if (!form || !preexistingEl) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var delta = determineDelta();
        if (delta.delete.length > 0 || delta.create.length > 0) {
            updateSubscriptions(delta);
        }
    });

    function getPreexistingIds() {
        var val = preexistingEl.value;
        return val === '' ? [] : val.split(',');
    }

    function getUpdatedSubscriptions() {
        var formData = new FormData(form);
        var ids = [];
        formData.forEach(function (value, key) {
            if (value === 'on') {
                ids.push(key);
            }
        });
        return ids;
    }

    function determineDelta() {
        var existing = getPreexistingIds();
        var updated = getUpdatedSubscriptions();
        return {
            delete: existing.filter(function (id) { return !updated.includes(id); }),
            create: updated.filter(function (id) { return !existing.includes(id); })
        };
    }

    function updateSubscriptions(delta) {
        var userId = form.querySelector('#user-id').value;
        var promises = [];

        if (delta.delete.length > 0) {
            promises.push(
                fetch('/api/v2.0/forum/userscategories/destroy', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ userId: userId, categoriesIds: delta.delete }).toString()
                }).then(function (r) { return r.json(); })
            );
        } else {
            promises.push(Promise.resolve({ status: 'success', records: [], null: true }));
        }

        if (delta.create.length > 0) {
            promises.push(
                fetch('/api/v2.0/forum/userscategories/create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ userId: userId, categoriesIds: delta.create }).toString()
                }).then(function (r) { return r.json(); })
            );
        } else {
            promises.push(Promise.resolve({ status: 'success', records: [], null: true }));
        }

        Promise.all(promises).then(function (responses) {
            var delResp = responses[0];
            var createResp = responses[1];

            notifyUser(delResp, createResp);
            updatePreexistingIds(delResp, createResp);
        });
    }

    function updatePreexistingIds(delResp, createResp) {
        var ids = getPreexistingIds();

        if (!delResp.null && delResp.status === 'success') {
            delResp.records.forEach(function (rec) {
                var idx = ids.indexOf(rec.category_id);
                if (idx > -1) ids.splice(idx, 1);
            });
        }

        if (!createResp.null && createResp.status === 'success') {
            createResp.records.forEach(function (rec) {
                ids.push(rec.category_id);
            });
        }

        ids.sort();
        preexistingEl.value = ids.join(',');
    }

    function notifyUser(delResp, createResp) {
        var messages = [];
        var type = 'success';

        if (!delResp.null) {
            if (delResp.status === 'error') {
                messages.push('Error deleting subscriptions' + (delResp.errors ? ': ' + delResp.errors.join(', ') : ''));
                type = 'error';
            } else {
                messages.push('The specified subscriptions were deleted.');
            }
        }

        if (!createResp.null) {
            if (createResp.status === 'error') {
                messages.push('Error creating subscriptions' + (createResp.errors ? ': ' + createResp.errors.join(', ') : ''));
                type = (type === 'error') ? 'error' : 'warn';
            } else {
                messages.push('Subscriptions created.');
            }
        }

        if (messages.length > 0) {
            Notify[type](messages.join('<br>'));
        }
    }
});
