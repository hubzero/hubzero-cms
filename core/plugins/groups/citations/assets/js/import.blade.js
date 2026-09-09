/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function () {

    // Review button — confirm overwrite if citations require attention
    var reviewInput = document.getElementById('review-input');
    if (reviewInput) {
        reviewInput.addEventListener('click', function (e) {
            if (document.querySelectorAll('.require-action').length > 0) {
                var confirmed = confirm('Are you sure you want to overwrite with the new import?');
                if (confirmed) {
                    var hubForm = document.getElementById('hubForm');
                    if (hubForm) {
                        hubForm.submit();
                    }
                } else {
                    e.preventDefault();
                }
            }
        });
    }

    // Click citation title to toggle details visibility
    document.querySelectorAll('.upload-list .citation-title').forEach(function (title) {
        title.addEventListener('click', function () {
            var row = title.closest('tr');
            if (!row) {
                return;
            }

            var details = row.querySelector('.citation-details');
            var showMore = row.querySelector('.click-more');

            if (showMore) {
                if (showMore.textContent === '\u2190 Click to show citation details') {
                    showMore.textContent = '\u2190 Click to hide citation details';
                } else {
                    showMore.textContent = '\u2190 Click to show citation details';
                }
            }

            if (details) {
                details.style.display = (details.style.display === 'none' || details.style.display === '') ? 'table-row' : 'none';
            }

            row.classList.toggle('active');
        });
    });

    // Check all checkboxes in the table body
    document.querySelectorAll('.checkall').forEach(function (checkall) {
        checkall.addEventListener('click', function () {
            var table = checkall.closest('table.upload-list');
            if (!table) {
                return;
            }

            var tbody = table.querySelector('tbody');
            if (!tbody) {
                return;
            }

            var checkboxes = tbody.querySelectorAll('input[type=checkbox]');
            checkboxes.forEach(function (cb) {
                cb.checked = checkall.checked;
            });
        });
    });

    // Update checkall state when individual checkboxes change
    document.querySelectorAll('.check-single').forEach(function (single) {
        single.addEventListener('click', function () {
            var tbody = single.closest('table.upload-list tbody');
            if (!tbody) {
                return;
            }

            var checkboxes = tbody.querySelectorAll('input[type=checkbox]');
            var allChecked = true;
            checkboxes.forEach(function (cb) {
                if (!cb.checked) {
                    allChecked = false;
                }
            });

            var table = single.closest('table.upload-list');
            if (table) {
                var checkall = table.querySelector('.checkall');
                if (checkall) {
                    checkall.checked = allChecked;
                }
            }
        });
    });

    // Citation attention options — apply overwrite/discard/both styling
    document.querySelectorAll('.citation_require_attention_option').forEach(function (option) {
        option.addEventListener('click', function () {
            var action = this.value;
            var detailsEl = this.closest('.citation-details');
            if (!detailsEl) {
                return;
            }

            var parent = detailsEl.querySelector('tbody');
            if (!parent) {
                return;
            }

            var newRows = parent.querySelectorAll('.new');
            var oldRows = parent.querySelectorAll('.old');

            switch (action) {
                case 'overwrite':
                    newRows.forEach(function (row) {
                        row.classList.add('insert');
                        row.classList.remove('delete');
                    });
                    oldRows.forEach(function (row) {
                        row.classList.add('delete');
                    });
                    break;
                case 'discard':
                    newRows.forEach(function (row) {
                        row.classList.remove('insert');
                        row.classList.add('delete');
                    });
                    oldRows.forEach(function (row) {
                        row.classList.remove('delete');
                        row.classList.remove('insert');
                    });
                    break;
                case 'both':
                    newRows.forEach(function (row) {
                        row.classList.add('insert');
                        row.classList.remove('delete');
                    });
                    oldRows.forEach(function (row) {
                        row.classList.add('insert');
                        row.classList.remove('delete');
                    });
                    break;
            }
        });
    });

    // Hide/show citation form fields based on type selection
    function hideCitationFields(display) {
        var addCitation = document.querySelector('.add-citation');
        if (!addCitation) {
            return;
        }

        var firstFieldset = addCitation.querySelector('fieldset');
        if (!firstFieldset) {
            return;
        }

        firstFieldset.querySelectorAll('label').forEach(function (label) {
            var forAttr = label.getAttribute('for');
            if (forAttr !== 'type' && forAttr !== 'title') {
                label.style.display = display;
            }
        });
    }

    // Hide fields initially if no citation type is selected
    var typeSelect = document.querySelector('.add-citation #type');
    if (typeSelect && typeSelect.value === '') {
        hideCitationFields('none');
    }

    // On citation type change, show relevant fields
    if (typeSelect) {
        typeSelect.addEventListener('change', function () {
            hideCitationFields('none');
            var type = this.options[this.selectedIndex].text;
            type = type.replace(/\s+/g, '').toLowerCase();

            if (typeof fields !== 'undefined' && fields[type]) {
                fields[type].forEach(function (val) {
                    var el = document.getElementById(val);
                    if (el) {
                        var label = el.closest('label');
                        if (label) {
                            label.style.display = 'block';
                        }
                    }
                });
            } else {
                if (this.value !== '') {
                    hideCitationFields('block');
                }
            }
        });
    }

    // Citation notes tooltip on hover
    document.querySelectorAll('.citation-container').forEach(function (container) {
        var title = container.querySelector('.citation-title');
        var note = container.querySelector('.citation-notes');

        if (title && note) {
            title.appendChild(note);

            title.addEventListener('mouseenter', function () {
                var rect = title.getBoundingClientRect();
                var noteHeight = note.offsetHeight;

                if (rect.top < noteHeight / 2) {
                    note.classList.add('bottom');
                }

                note.style.display = 'block';
            });

            title.addEventListener('mouseleave', function () {
                note.style.display = 'none';
                note.classList.remove('bottom');
            });
        }
    });

    // Show more button — reveal truncated text
    var showMoreButton = document.getElementById('show-more-button');
    if (showMoreButton) {
        showMoreButton.addEventListener('click', function (event) {
            event.preventDefault();
            showMoreButton.remove();

            document.querySelectorAll('.show-more-hellip').forEach(function (el) {
                el.remove();
            });

            document.querySelectorAll('.show-more-text').forEach(function (el) {
                el.style.display = 'inline';
            });
        });
    }

});
