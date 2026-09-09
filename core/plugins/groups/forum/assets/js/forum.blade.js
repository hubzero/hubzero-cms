/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
'use strict';

document.addEventListener('DOMContentLoaded', function () {
    // Delete confirmation
    document.querySelectorAll('a.delete').forEach(function (link) {
        link.addEventListener('click', function (e) {
            if (!confirm('Are you sure you wish to delete this item?')) {
                e.preventDefault();
            }
        });
    });

    // Reply form toggle with auth check
    document.querySelectorAll('a.reply').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            var self = this;
            var currentUser = new HUB.User();

            currentUser.isAuthenticated().then(function (userStatus) {
                if (!userStatus.isAuthenticated) {
                    Notify.warn('Please sign in to reply');
                } else {
                    var frm = document.getElementById(self.getAttribute('rel'));
                    if (!frm) return;

                    if (frm.classList.contains('hide')) {
                        frm.classList.remove('hide');
                        self.classList.add('active');
                        self.textContent = self.getAttribute('data-txt-active');
                    } else {
                        frm.classList.add('hide');
                        self.classList.remove('active');
                        self.textContent = self.getAttribute('data-txt-inactive');
                    }
                }
            });
        });
    });

    // Forum options panel
    var optionsBtn = document.querySelector('.edit-forum-options');
    var optionsPanel = document.querySelector('.edit-forum-options-panel');

    if (optionsBtn && optionsPanel) {
        optionsBtn.addEventListener('click', function (e) {
            e.preventDefault();
            optionsPanel.style.display = 'block';
        });

        var cancelBtn = optionsPanel.querySelector('.edit-forum-options-cancel');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function (e) {
                e.preventDefault();
                optionsPanel.style.display = 'none';
                var msg = optionsPanel.querySelector('.response-message');
                if (msg) {
                    msg.className = 'response-message';
                    msg.innerHTML = '';
                }
            });
        }
    }

    // Email checkbox logic
    var receiveEmails = document.querySelector('.edit-forum-options-receive-emails');
    if (receiveEmails) {
        receiveEmails.addEventListener('click', function () {
            var immediate = document.querySelector('.edit-forum-options-immediate');
            var digest = document.querySelector('.edit-forum-options-digest');
            var frequency = document.querySelector('.edit-forum-options-frequency');

            if (this.checked) {
                if (immediate) immediate.disabled = false;
                if (digest) digest.disabled = false;
                if (digest && digest.checked && frequency) {
                    frequency.disabled = false;
                }
            } else {
                if (immediate) immediate.disabled = true;
                if (digest) digest.disabled = true;
                if (frequency) frequency.disabled = true;
            }
        });
    }

    var digestBtn = document.querySelector('.edit-forum-options-digest');
    if (digestBtn) {
        digestBtn.addEventListener('click', function () {
            var frequency = document.querySelector('.edit-forum-options-frequency');
            if (frequency) frequency.disabled = false;
        });
    }

    var immediateBtn = document.querySelector('.edit-forum-options-immediate');
    if (immediateBtn) {
        immediateBtn.addEventListener('click', function () {
            var frequency = document.querySelector('.edit-forum-options-frequency');
            if (frequency) frequency.disabled = true;
        });
    }

    // Forum options form submit
    var optionsForm = document.getElementById('forum-options-extended');
    if (optionsForm) {
        optionsForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var formData = new URLSearchParams(new FormData(this)).toString();
            var responseMsg = document.querySelector('.response-message');

            fetch(this.action + '?no_html=1', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData
            })
            .then(function (r) { return r.json(); })
            .then(function (response) {
                if (response.success) {
                    if (responseMsg) {
                        responseMsg.className = 'response-message passed message';
                        responseMsg.innerHTML = 'Settings saved!';
                    }
                    setTimeout(function () {
                        var panel = document.querySelector('.edit-forum-options-panel');
                        if (panel) panel.style.display = 'none';
                        if (responseMsg) {
                            responseMsg.className = 'response-message';
                            responseMsg.innerHTML = '';
                        }
                    }, 2000);
                } else {
                    if (responseMsg) {
                        responseMsg.className = 'response-message error';
                        responseMsg.innerHTML = 'Save failed!';
                    }
                }
            })
            .catch(function () {
                if (responseMsg) {
                    responseMsg.className = 'response-message error';
                    responseMsg.innerHTML = 'Save failed!';
                }
            });
        });
    }
});
