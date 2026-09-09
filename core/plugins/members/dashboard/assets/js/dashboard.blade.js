/**
 * Member Dashboard — daisyUI mode.
 *
 * Initializes Gridster grid layout, drag-drop reordering,
 * module settings toggle, add/remove modules via native
 * <dialog>, and auto-save of positions.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
    var jq = $;
}

if (!HUB) {
    var HUB = {};
}
if (!HUB.Plugins) {
    HUB.Plugins = {};
}

HUB.Plugins.MemberDashboard = {
    jQuery: jq,

    modules: null,

    settings: {
        max_cols: 3,
        col_margin_vert: 0,
        col_margin_horz: 0,
        col_width: 300,
        col_height: 150,
        remove_timeout: 4000
    },

    initialize: function () {
        var $ = this.jQuery;

        if (window.innerWidth <= 800) {
            return;
        }

        // tell the modules we have js
        $('.modules-container').addClass('js-enabled');

        // calculate working area
        this._calculateWorkingArea();

        // init grid
        this.grid();

        // init add dialog
        this.add();

        // handle module events
        this.moduleClickEvents();

        // do we have any modules?
        this.emptyStateCheck();
    },

    grid: function () {
        var $         = this.jQuery,
            dashboard = this;

        // instantiate gridster
        dashboard.modules = $('.modules').gridster({
            widget_selector: '.module',
            widget_margins: [dashboard.settings.col_margin_horz, dashboard.settings.col_margin_vert],
            widget_base_dimensions: [dashboard.settings.col_width, dashboard.settings.col_height],
            max_cols: dashboard.settings.max_cols,
            serialize_params: function (element, specs) {
                var params = {};
                $.each($(element).find('.module-settings form').serializeArray(), function () {
                    var name = this.name.replace(/params\[([^\]]*)\]/g, "$1");
                    params[name] = this.value;
                });

                return {
                    module: $(element).data('moduleid'),
                    col: specs.col,
                    row: specs.row,
                    size_x: specs.size_x,
                    size_y: specs.size_y,
                    parameters: params
                };
            },
            draggable: {
                handle: 'h3',
                items: '.gs-w:not(.static)',
                stop: function () {
                    dashboard.save();
                }
            },
            resize: {
                enabled: true,
                stop: function () {
                    dashboard.save();
                }
            }
        }).data('gridster');

        // is the dashboard customizable?
        if (!$('.modules').hasClass('customizable')) {
            dashboard.modules.disable();
            dashboard.modules.disable_resize();
        }

        this.modules = dashboard.modules;

        // handle window resize events
        this.windowResize();
    },

    add: function () {
        var $         = this.jQuery,
            dashboard = this;

        // Use native <dialog> instead of fancybox
        $('.add-module').on('click', function (event) {
            event.preventDefault();

            var href = $(this).attr('href');
            if (href.indexOf('?') === -1) {
                href += '?no_html=1';
            } else {
                href += '&no_html=1';
            }

            var dialog = document.getElementById('add-modules-dialog');
            if (!dialog) {
                return;
            }

            var body = dialog.querySelector('.dialog-body');
            body.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;width:100%"><span class="loading loading-spinner loading-lg"></span></div>';
            dialog.showModal();

            $.ajax({
                type: 'get',
                url: href,
                success: function (data) {
                    body.innerHTML = data;

                    // Click first category tab
                    var firstTrigger = body.querySelector('.module-list-triggers a');
                    if (firstTrigger) {
                        firstTrigger.click();
                    }
                },
                error: function () {
                    body.innerHTML = '<div style="padding:2rem;text-align:center">Failed to load modules.</div>';
                }
            });
        });

        // Dialog close button
        $('body').on('click', '.add-modules-dialog .dialog-close', function (event) {
            event.preventDefault();
            var dialog = document.getElementById('add-modules-dialog');
            if (dialog) {
                dialog.close();
            }
        });

        // Close on backdrop click
        $('body').on('click', '#add-modules-dialog', function (event) {
            if (event.target === this) {
                this.close();
            }
        });

        // Category tab switching
        $('body').on('click', '.module-list-triggers a', function (event) {
            event.preventDefault();
            var module = $(this).attr('data-module');

            $('.module-list-triggers a').removeClass('active');
            $(this).addClass('active');

            $('.module-list-content li').hide();
            $('.module-list-content li.' + module).show();
        });

        // Install module button
        $('body').on('click', '.install-module', function (event) {
            event.preventDefault();
            var moduleid = $(this).attr('data-module');
            dashboard.loadModule(moduleid);
        });
    },

    loadModule: function (moduleid) {
        var $         = this.jQuery,
            userid    = $('.modules').attr('data-userid'),
            dashboard = this;

        $.ajax({
            type: 'post',
            url: 'index.php?option=com_members&id=' + userid + '&active=dashboard&action=module',
            dataType: 'json',
            data: {
                moduleid: moduleid
            },
            success: function (data) {
                dashboard.addModuleAssets(data.assets);
                dashboard.addModule(data.html);
            },
            error: function () {
                // silently fail
            }
        });
    },

    refreshModule: function (moduleid) {
        var $         = this.jQuery,
            userid    = $('.modules').attr('data-userid'),
            dashboard = this;

        $.ajax({
            type: 'post',
            url: 'index.php?option=com_members&id=' + userid + '&active=dashboard&action=module',
            dataType: 'json',
            data: {
                moduleid: moduleid
            },
            success: function (data) {
                dashboard.addModuleAssets(data.assets);
                $('.module[data-moduleid=' + moduleid + ']').html($(data.html).html());
            },
            error: function () {
                // silently fail
            }
        });
    },

    addModule: function (moduleHtml) {
        var $         = this.jQuery,
            dashboard = this;

        var colRow = dashboard._calculateColumnRow();

        // Close dialog
        var dialog = document.getElementById('add-modules-dialog');
        if (dialog) {
            dialog.close();
        }

        // Add module and save prefs
        dashboard.modules.add_widget(moduleHtml, 1, 2, colRow[0], colRow[1]);
        dashboard.emptyStateCheck();
        dashboard.save();
    },

    addModuleAssets: function (assets) {
        var $ = this.jQuery;

        var head = document.getElementsByTagName('head')[0];
        $.each(assets.scripts, function (index, s) {
            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = s;
            head.appendChild(script);
        });
        $.each(assets.stylesheets, function (index, s) {
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = s;
            link.media = 'screen';
            head.appendChild(link);
        });
    },

    removeModule: function (module) {
        var $         = this.jQuery,
            dashboard = this;

        dashboard.modules.remove_widget(module, function () {
            dashboard.save();
            dashboard.emptyStateCheck();
        });
    },

    windowResize: function () {
        var $         = this.jQuery,
            dashboard = this;

        $(window).resizeEnd(function () {
            dashboard._calculateWorkingArea();
            dashboard.modules.resize_widget_dimensions({
                widget_base_dimensions: [dashboard.settings.col_width, dashboard.settings.col_height]
            });
        });

        $(window).trigger('resize');
    },

    save: function (callback) {
        var $      = this.jQuery,
            userid = $('.modules').attr('data-userid'),
            dashboard = this,
            params = dashboard.modules.serialize(),
            module_data = JSON.stringify(params);

        $.ajax({
            type: 'post',
            url: 'index.php?option=com_members&id=' + userid + '&active=dashboard&action=save&no_html=1&' + $('.modules').attr('data-token') + '=1',
            dataType: 'json',
            data: {
                modules: module_data
            },
            complete: function () {
                if (callback) {
                    callback.call();
                }
            },
            success: function () {
                // saved
            },
            error: function (jqXHR, status, error) {
                console.log(status);
                console.log(error);
            }
        });
    },

    emptyStateCheck: function () {
        var $     = this.jQuery,
            count = $('.modules .module').length;

        if (count === 0) {
            $('.modules').height(0);
            $('.modules-empty').show();
        } else {
            $('.modules-empty').hide();
        }
    },

    moduleClickEvents: function () {
        var $         = this.jQuery,
            dashboard = this;

        $('.modules')
            .on('click', '.module-links .remove', function (event) {
                event.preventDefault();
                var $this = $(this);
                if (!$this.hasClass('confirm')) {
                    $this.addClass('confirm');
                    setTimeout(function () {
                        $this.removeClass('confirm');
                    }, dashboard.settings.remove_timeout);
                }
            })
            .on('click', '.module-links .confirm', function (event) {
                event.preventDefault();
                var module = $(this).parents('.module');
                dashboard.removeModule(module);
            })
            .on('click', '.module-links .settings', function (event) {
                event.preventDefault();
                $(this).parents('.module')
                    .toggleClass('modifying-settings')
                    .find('.module-settings')
                    .slideToggle("fast");
            })
            .on('click', '.module-settings .save', function (event) {
                event.preventDefault();
                var button = $(this);
                button
                    .attr('disabled', 'disabled')
                    .html('Saving...');

                dashboard.save(function () {
                    button
                        .parents('.module')
                        .toggleClass('modifying-settings')
                        .find('.module-settings')
                        .slideToggle("fast");

                    button.removeAttr('disabled')
                        .html('Save');

                    var moduleid = button.parents('.module').attr('data-moduleid');
                    dashboard.refreshModule(moduleid);
                });
            })
            .on('click', '.module-settings .cancel', function (event) {
                event.preventDefault();
                $(this).parents('.module')
                    .removeClass('modifying-settings')
                    .find('.module-settings')
                    .slideToggle("fast");
            });
    },

    _calculateColumnRow: function () {
        var $         = this.jQuery,
            dashboard = this,
            map       = dashboard.modules.gridmap;

        var max = [];

        for (var i = 1; i < map.length; i++) {
            var col = map[i];
            for (var n = 0; n < col.length; n++) {
                if (col[n] === false) {
                    max.push(n);
                    break;
                }
            }
        }

        var row = Math.min.apply(Math, max);
        var col;
        if (max[0] === row) {
            col = 1;
        } else if (max[1] === row) {
            col = 2;
        } else {
            col = 3;
        }

        return [col, row];
    },

    _calculateWorkingArea: function () {
        var $ = this.jQuery;

        $('.modules').hide();

        var innerWrapWidth = $('.modules-container').innerWidth();

        $('.modules').show();

        var moduleBaseWidth = parseInt(innerWrapWidth / this.settings.max_cols, 10);
        moduleBaseWidth -= (this.settings.col_margin_horz * 2);

        var moduleBaseHeight = parseInt(moduleBaseWidth, 10);

        this.settings.col_width  = moduleBaseWidth;
        this.settings.col_height = moduleBaseHeight;
    }
};

jQuery(document).ready(function ($) {
    HUB.Plugins.MemberDashboard.initialize();
});
