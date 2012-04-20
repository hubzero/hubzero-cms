/**
 * @package     hubzero-cms
 * @file        plugins/members/dashboard/dashboard.jquery.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//-------------------------------------------------------------
// My Hub (singleton)
//-------------------------------------------------------------

HUB.Myhub = {
	baseURL: '/index.php?option=com_members&active=dashboard&no_html=1&init=1',
	
	jQuery : $,
    
    settings : {
        columns : '#droppables .sortable',
        moduleSelector: '.draggable',
        handleSelector: '.handle',
		innerSelector: '.cwrap',
        contentSelector: '.body',
        moduleDefault : {
            movable: true,
            removable: true,
            collapsible: false,
            editable: false
        },
        moduleIndividual : {
            intro : {
                movable: false,
                removable: false,
                collapsible: false,
                editable: false
            }
        }
    },
	
	initialize: function () {
		var myhub = this,
			$ = this.jQuery,
			settings = this.settings;

		if (!jQuery().sortable) {
			return;
		}
			
		this.addModuleControls();
        this.makeSortable();
	},
	
	getModuleSettings: function (id) {
        var $ = this.jQuery,
            settings = this.settings;
        return (id && settings.moduleIndividual[id]) ? $.extend({},settings.moduleDefault,settings.moduleIndividual[id]) : settings.moduleDefault;
    },

    addModuleControls: function () {
        var myhub = this,
            $ = this.jQuery,
            settings = this.settings;
		
		$(settings.handleSelector).each(function(i, elm) {
			if (!$(elm).hasClass('movable')) {
				$(elm).addClass('movable');
			}
		});
			
        $(settings.moduleSelector, $(settings.columns)).each(function () {
            var thisModuleSettings = myhub.getModuleSettings(this.id);

			if (thisModuleSettings.removable && $(this).children(settings.handleSelector).children('.remove').length <= 0) {
				$('<a href="#" class="close" title="Close"><span>CLOSE</span></a>').mousedown(function (e) {
                    e.stopPropagation();    
                }).click(function () {
                    if (confirm('This module will be removed, ok?')) {
                        $(this).parents(settings.moduleSelector).animate({
                            opacity: 0    
                        },function () {
                            $(this).wrap('<div/>').parent().slideUp(function () {
                                $(this).remove();
								myhub.saveOrder('rebuild');
                            });
                        });
                    }
                    return false;
                }).insertBefore($(settings.handleSelector, this));
            }

			if ($('.module-params',this).children().length > 0 && $(this).children(settings.handleSelector).children('.edit').length <= 0) {	
				$('<a href="#" class="edit"><span>EDIT</span></a>').mousedown(function (e) {
                    e.stopPropagation();    
                }).toggle(function () {
                    $(this).parents(settings.moduleSelector)
                            .find('.module-params').removeClass('collapsed').find('input').focus();
                    return false;
                },function () {
                    $(this).parents(settings.moduleSelector)
                            .find('.module-params').addClass('collapsed');
					myhub.saveParams($(this).closest(settings.moduleSelector).attr('id'));
                    return false;
                }).appendTo($(settings.handleSelector,this));
            }

            if (thisModuleSettings.collapsible && $(this).children(settings.handleSelector).children('.collapse').length <= 0) {
                $('<a href="#" class="collapse"><span>COLLAPSE</span></a>').mousedown(function (e) {
                    e.stopPropagation();    
                }).toggle(function () {
                    if (!$(this).closest(settings.moduleSelector).hasClass('collapsed')) {
						$(this).closest(settings.moduleSelector)
                            .addClass('collapsed');
					} else {
						$(this).closest(settings.moduleSelector)
                            .removeClass('collapsed');
					}
					myhub.saveToggle($(this).closest(settings.moduleSelector).attr('id'));
                    return false;
                },function () {
                    if ($(this).closest(settings.moduleSelector).hasClass('collapsed')) {
						$(this).closest(settings.moduleSelector)
                            .removeClass('collapsed');
					} else {
						$(this).closest(settings.moduleSelector)
                            .addClass('collapsed');
					}
					myhub.saveToggle($(this).closest(settings.moduleSelector).attr('id'));
                    return false;
                }).prependTo($(settings.handleSelector, this));
            }
        });
    },

	removeModule: function (el) {
		var myhub = this,
			$ = this.jQuery,
			settings = this.settings;
	
		//if (confirm('This module will be removed, ok?')) {
            $(el).parents(settings.moduleSelector).animate({
                opacity: 0    
            },function () {
                $(this).wrap('<div/>').parent().slideUp(function () {
					$(this).remove();
					myhub.saveOrder('rebuild');
                });
            });
        //}
        return false;
	},

	addModule: function (modId) {
		var myhub = this,
	            $ = this.jQuery,
	            settings = this.settings;
	
		var orders = [], col = 0;
		$(settings.columns).each(function(i, elm) {
			orders.push($(elm).children(settings.moduleSelector).length);
		});
		var order = Math.min.apply(Math, orders);
		for (var i = 0; i < orders.length; i++) 
		{
			if (orders[i] == order) {
				col = i; //(i + 1);
				break;
			}
		}

		$.get(myhub.baseURL+'&action=addmodule&id='+$('#uid').val()+'&mid='+modId, {}, function(data) {
			var wrap = $('<div class="draggable" id="mod_'+modId+'"></div>').append(data);
			$('#sortcol_' + col).append(wrap);
			myhub.addModuleControls();
			$(settings.columns).sortable('enable');
			myhub.saveOrder('rebuild');
		});
	},

	saveToggle: function (modId) {
		$.get(myhub.baseURL+'&action=toggle&id='+$('#uid').val()+'&mid='+modId, {});
	},
	
	saveParams: function (modId) {
		data = $('#mod_' + modId + ' .module-params').serialize();
		$.post($('#dashboard-info').attr('action')+'/params', data, function(data) {
		    $('#mod_' + modId + ' .body').html(data);
		});
	},
    
    makeSortable: function () {
       var myhub = this,
            $ = this.jQuery,
            settings = this.settings;

        $(settings.columns).sortable({
            connectWith: $(settings.columns),
            handle: settings.handleSelector,
            placeholder: 'module-placeholder',
            forcePlaceholderSize: true,
            revert: 300,
            delay: 100,
            opacity: 0.8,
            containment: 'document',
            start: function (e, ui) {
                $(ui.helper).addClass('dragging');
            },
            stop: function (e, ui) {
                $(ui.item).css({width:''}).removeClass('dragging');
                $(settings.columns).sortable('enable');
            },
			update: function (e, ui) {
				myhub.saveOrder('save');
			}

        });
    },

	serialize: function () {
		var myhub = this,
			$ = this.jQuery,
			settings = this.settings;
	
		order = [];
		$(settings.columns).each(function(i, elm) {
			col = [];
			$(elm).children(settings.moduleSelector).each(function(id, el) {
				col.push(el.id.split('_')[1])
			});
			order.push(col.join(','));
		});
		
		return order;
	},

	saveOrder: function (task) {
		var myhub = this,
	            $ = this.jQuery,
	            settings = this.settings;
	
		order = myhub.serialize();
		$('#serials').val(order.join(';'));

		$.get(myhub.baseURL+'&action='+task+'&id='+$('#uid').val()+'&mids='+order.join(';'), {}, function(data) {
			if (task == 'rebuild') {
				$('#available').html(data);
			}
		});
	}
};

// a global variable to hold our sortable object
// done so the Myhub singleton can access the sortable object easily
HUB.Sorts = null;

jQuery(document).ready(function($){
	HUB.Myhub.initialize();
});

