/**
 * @package     hubzero-cms
 * @file        plugins/courses/notes/notes.jquery.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

var typewatch = (function(){
	var timer = 0;
	return function(callback, ms){
		clearTimeout(timer);
		timer = setTimeout(callback, ms);
	}
})();

;(function(jQuery, window, document, undefined) {

	var pluginName = 'stickyNotes',
		defaults = {
			notes: [],
			resizable: true,
			shareable: false,
			controls: true,
			controlBar: true,
			editCallback: false, 
			createCallback: false,
			deleteCallback: false,
			moveCallback: false,
			resizeCallback: false
		},
		_DEBUG = false;

	function Plugin(container, options) {
		this.container = jQuery(container);
		this.options   = jQuery.extend({}, defaults, options);

		this._defaults = defaults;
		this._name     = pluginName;
		this.notes     = [];

		this.init();
	};

	Plugin.prototype = {
		init: function() {
			var self = this;

			if (this.options.controls) {
				if (this.options.controlBar) {
					this.container.find('.video-player-wrap')
						.append(
							'<div id="notes-list-container">' + 
								'<div id="notes-list"></div>' + 
								'<p id="notes-controls">' + 
									'<button id="toggle_notes" data-show-all="Show all" data-hide-all="Hide all">Hide all</button>' + 
									'<button id="add_note">Add Note</button>' + 
								'</p>' + 
							'</div>'
						);
					jQuery("#toggle_notes").on('click', function(e) {
						e.preventDefault();

						var el = jQuery(this);

						if (el.hasClass('hidden-notes')) {
							el.removeClass('hidden-notes')
								.text(el.attr('data-hide-all'));
							
							jQuery.each(self.notes, function(index, note) {
								jQuery('#note-' + note.id).show();
							});
						} else {
							el.addClass('hidden-notes')
								.text(el.attr('data-show-all'));
							
							jQuery.each(self.notes, function(index, note) {
								jQuery('#note-' + note.id).hide();
							});
						}
					});

					/*
					if (jQuery('#notes-list').length) {
						if (typeof HUB.Presenter !== 'undefined') {
							//start timer
							var timer = setInterval(function() {
								this.checkTimes();
							}, 5 * 1000);
						}
					}
					*/
				} else {
					this.container.find('.video-meta')
						.append('<button id="add_note">Add Note</button>');
				}

				jQuery("#add_note").on('click', function() {
					self.createNote();
					return false;
				});
			}

			jQuery.each(this.options.notes, function(index, note){
				self.renderNote(note);
				self.notes.push(note);
			});

			jQuery(window).on('resize', function(e) {
				var width = jQuery(window).width();
				jQuery.each(self.options.notes, function(index, note){
					var item = jQuery('#note-' + note.id);

					if (parseInt(item.css('left').replace('px', '')) >= width) {
						item.css('left', (width - item.width()) + 'px');
					}
				});
			});
		},

		checkTimes: function() {
			var tm = HUB.Presenter.formatTime(HUB.Presenter.getCurrent());
			if (tm < '00:00:06') {
				return;
			}
			
			var oldDateObj = new Date('01/01/2011 ' + tm);
			//Date.parse('01/01/2011 10:20:45') > Date.parse('01/01/2011 5:10:10')
			
			var startDateObj = new Date();
			startDateObj.setTime(oldDateObj.getTime() - (5 * 1000));
			//tm_start = startDateObj.getHours('HH') + ":" + startDateObj.getMinutes('MM') + ":" + startDateObj.getSeconds('SS');
			tm_start = (startDateObj.getHours() < 10 ? '0' + startDateObj.getHours() : startDateObj.getHours()) + ":" + 
					(startDateObj.getMinutes() < 10 ? '0' + startDateObj.getMinutes() : startDateObj.getMinutes()) + ":" + 
					(startDateObj.getSeconds() < 10 ? '0' + startDateObj.getSeconds() : startDateObj.getSeconds());
			/*var endDateObj = new Date();
			endDateObj.setTime(oldDateObj.getTime() + (5 * 1000));
			tm_end = endDateObj.getHours('HH') + ":" + startDateObj.getMinutes('MM') + ":" + startDateObj.getSeconds('SS');

			var regExp = /(\d{1,2})\:(\d{1,2})\:(\d{1,2})/;
			if (parseInt(tm.replace(regExp, "$1$2$3")) > parseInt(startTime .replace(regExp, "$1$2$3"))){
				alert("End time is greater");
			}*/

			jQuery.each(notes, function(index, note){
				//jQuery.fn.stickyNotes.renderNote(note);
				//jQuery.fn.stickyNotes.notes.push(note);
				if (note.timestamp != '00:00:00') {
					/*var oldDateObj = new Date('01/01/2011 ' + note.timestamp);
					//Date.parse('01/01/2011 10:20:45') > Date.parse('01/01/2011 5:10:10')

					var startDateObj = new Date();
					startDateObj.setTime(oldDateObj.getTime() - (5 * 1000));
					tm_start = startDateObj.toString('HH:MM:SS');*/
					var tDateObj = new Date('01/01/2011 ' + note.timestamp);

					var endDateObj = new Date();
					endDateObj.setTime(tDateObj.getTime() + (5 * 1000));
					tm_end = (endDateObj.getHours() < 10 ? '0' + endDateObj.getHours() : endDateObj.getHours()) + ":" + 
							(endDateObj.getMinutes() < 10 ? '0' + endDateObj.getMinutes() : endDateObj.getMinutes()) + ":" + 
							(endDateObj.getSeconds() < 10 ? '0' + endDateObj.getSeconds() : endDateObj.getSeconds());
					
	//console.log(tm_start + ' :: ' + tm_end);
					if (note.timestamp >= tm_start) {
						jQuery('#note-' + note.id).fadeIn(500);
					}
					if (tm > tm_end) {
						jQuery('#note-' + note.id).fadeOut(500);
					}
				}
			});
		},

		getNote: function(note_id) {
			var result = null;
			jQuery.each(this.notes, function(index, note) {
				if (note.id == note_id) {
					result = note;
					return false;
				}
			});
			return result;
		},

		getNotes: function() {
			return this.notes;
		},

		removeNote: function(note_id) {
			var notes = this.notes;
			jQuery.each(notes, function(index, note) {
				if (note.id == note_id) {
					notes.splice(index, 1);
					return false;
				}
			});
		},

		createNote: function() {
			var self = this,
				pos_y = (this.container.width() / 2),
				pos_x = 0,
				note_id = this.notes.length + 1;

			if (jQuery('#notes-list-container').length) {
				pos_x = (jQuery('#notes-list-container').offset().top - this.container.offset().top + 20);
			}

			var _note_content = jQuery(document.createElement('textarea'))
					.on('keyup', function () {
						typewatch(function (e) {
								self.stopEditing(note_id);
							}, 500);
					})
					.on('focus', function (e){
						jQuery(this).parent().parent().css('opacity', 1);
					})
					.on('blur', function (e){
						jQuery(this).parent().parent().css('opacity', 0.85);
					});

			var _div_note = jQuery(document.createElement('div')).addClass('jStickyNote');

			var _div_header = jQuery(document.createElement('div')).addClass('jSticky-header');

			//if (note.timestamp && note.timestamp != '00:00:00') {
				var tm = '00:00:00';

				if (typeof HUB.Presenter !== 'undefined' || typeof HUB.Video !== 'undefined') {
					tm = (typeof HUB.Presenter !== 'undefined') 
						? HUB.Presenter.getCurrent()
						: HUB.Video.getCurrent();

					if (_DEBUG) {
						window.console && console.log(tm);
					}
					if (tm) {
						tm = (typeof HUB.Presenter !== 'undefined') 
						              ? HUB.Presenter.formatTime(tm)
						              : HUB.Video.formatTime(tm);
						_div_header.append(jQuery('<span></span>').addClass('time').text(tm));
					}
				}
			//}

			_div_note.append(_note_content);
			var _div_delete = jQuery(document.createElement('div'))
								.addClass('jSticky-delete')
								.attr('title', 'Delete note')
								.on('click', function(){
									self.deleteNote(this);
								});

			var _div_wrap = jQuery(document.createElement('div'))
								.css({'position':'absolute','top':pos_x,'left':pos_y, 'float' : 'left'})
								.attr('id', 'note-' + note_id)
								.attr("data-id", 0)
								.addClass('jSticky-medium')
								.append(_div_header)
								.append(_div_note)
								.append(_div_delete);

			if (this.options.shareable) {
				var _div_share = jQuery(document.createElement('div'))
									.text('Share')
									.attr('title', 'Share note with all users')
									.addClass('jSticky-share')
									.on('click', function(){
										if (jQuery(this).parent().hasClass('annotation')) {
											jQuery(this).parent().removeClass('annotation')
											jQuery(this)
												.text('Share')
												.attr('title', 'Share note with all users');
											jQuery('#note-tn-' + jQuery(this).parent().attr('data-id')).removeClass('annotation');
										} else {
											jQuery(this).parent().addClass('annotation');
											jQuery(this)
												.text('Stop sharing')
												.attr('title', 'Stop sharing note with all users');
											jQuery('#note-tn-' + jQuery(this).parent().attr('data-id')).addClass('annotation');
										}

										self.stopEditing(jQuery(this).parent().attr("id").replace(/note-/, ""));
									});
				_div_wrap.append(_div_share);
			}

			if (this.options.resizable) {
				_div_wrap.resizable({
					maxHeight: 1000,
					maxWidth: 1000,
					minHeight: 130,
					minWidth: 130,
					stop: function(event, ui) { 
						jQuery(this).find('textarea').css('height', jQuery(this).height() - 32);
						self.resizedNote(note_id)
					}
				});
			}
			_div_wrap.draggable({
				containment: this.container, 
				scroll: false, 
				handle: 'div.jSticky-header', 
				stop: function(event, ui) {
					self.movedNote(note_id);
				}
			}); 

			this.container.append(_div_wrap);

			jQuery("#note-" + note_id)
				.on('click', function() {
					return false;
				})
				.find("textarea")
				.focus();

			var note = {
				"id": note_id,
				"dataId": 0,
				"text": "",
				"pos_x": pos_x,
				"pos_y": pos_y,	
				"width": jQuery(_div_wrap).width(),
				"height": jQuery(_div_wrap).height(),
				"timestamp": tm,
				"access": 0,
				"editable": true
			};
			this.notes.push(note);

			jQuery(_note_content).css('height', jQuery("#note-" + note_id).height() - 32);

			if (jQuery('#notes-list').length) {
				var _thumbnail = jQuery(document.createElement('div'))
									.attr('id', 'note-tn-' + note_id)
									.attr('data-id', note.id)
									.addClass('jSticky-medium')
									.addClass('thumbnail')
									.append(jQuery(document.createElement('p')));
				jQuery('#notes-list').append(_thumbnail);
			}

			if (this.options.createCallback) {
				this.options.createCallback(note);
			}
		},

		stopEditing: function(note_id) {
			var note = this.getNote(note_id);
			note.text = jQuery("#note-" + note_id).find('textarea').val();

			if (jQuery("#note-" + note_id).hasClass('annotation')) {
				note.access = 1;
			} else {
				note.access = 0;
			}

			var thumb = jQuery('#note-tn-' + note.id);
			if (thumb) {
				thumb.find('p').text(note.text);
			}

			if (this.options.editCallback) {
				this.options.editCallback(note);
			}
		},

		deleteNote: function(delete_button) {
			var note_id = jQuery(delete_button).parent().attr("id").replace(/note-/, ""),
				real_id = jQuery(delete_button).parent().attr("data-id").replace(/note-/, ""),
				note = this.getNote(note_id);

			if (_DEBUG) {
				window.console && console.log("deleting note: #" + note_id + ', real id: #' + real_id);
			}

			if (this.options.deleteCallback) {
				note.id = real_id;
				this.options.deleteCallback(note);
			}

			jQuery("#note-" + note_id).remove();
			if (jQuery('#note-tn-' + note_id).length) {
				jQuery('#note-tn-' + note_id).remove();
			}

			this.removeNote(note_id);
		},

		renderNote: function(note) {
			var self = this;

			var _p_note_text = jQuery(document.createElement('p'))
				.attr("id", "p-note-" + note.id)
				.html(note.text);

			var _div_note = jQuery(document.createElement('div')).addClass('jStickyNote');

			var _div_header = jQuery(document.createElement('div')).addClass('jSticky-header');
			if (note.timestamp && note.timestamp != '00:00:00') {
				_div_header.append(jQuery('<span></span>').addClass('time').text(note.timestamp));
			}

			if (!this.options.shareable && note.access == 1) {
				var _note_content = jQuery(document.createElement('p')).text(note.text)
					.on('focus', function (e){
						jQuery(this).parent().parent().css('opacity', 1);
					})
					.on('blur', function (e){
						jQuery(this).parent().parent().css('opacity', 0.85);
					});
			} else {
				var _note_content = jQuery(document.createElement('textarea')).val(note.text).on('keyup', function (e) {
						typewatch(function () {
							self.stopEditing(note.id);
						}, 500);
					})
					.on('focus', function (e){
						jQuery(this).parent().parent().css('opacity', 1);
					})
					.on('blur', function (e){
						jQuery(this).parent().parent().css('opacity', 0.85);
					});
			}
			_div_note.append(_note_content);

			var _div_delete = jQuery(document.createElement('div'))
								.text('x')
								.addClass('jSticky-delete')
								.attr('title', 'Delete note')
								.on('click', function(){
									self.deleteNote(this);
								});

			if (parseInt(note.pos_x.replace('px', '')) >= jQuery(window).width()) {
				note.pos_x = (jQuery(window).width() - note.width) + 'px';
			}

			var _div_wrap 	= jQuery(document.createElement('div'))
								.css({'position':'absolute','top':note.pos_y,'left':note.pos_x, "width":note.width,"height":note.height}) //'float': 'left',
								.attr("id", "note-" + note.id)
								.attr("data-id", note.id)
								.addClass('jSticky-medium')
								.append(_div_header)
								.append(_div_note);
			if (note.editable) {
				_div_wrap.append(_div_delete);
			}

			if (this.options.shareable) {
				var _div_share = jQuery(document.createElement('div'))
									.text('Share')
									.attr('title', 'Share note with all users')
									.addClass('jSticky-share')
									.on('click', function(){
										if (jQuery(this).parent().hasClass('annotation')) {
											jQuery(this).parent().removeClass('annotation')
											jQuery(this)
												.text('Share')
												.attr('title', 'Share note with all users');
											jQuery('#note-tn-' + jQuery(this).parent().attr('data-id')).removeClass('annotation');
										} else {
											jQuery(this).parent().addClass('annotation');
											jQuery(this)
												.text('Stop sharing')
												.attr('title', 'Stop sharing note with all users');
											jQuery('#note-tn-' + jQuery(this).parent().attr('data-id')).addClass('annotation');
										}

										self.stopEditing(jQuery(this).parent().attr('data-id'));
									});
				if (note.access == 1) {
					_div_share.text('Stop sharing')
								.attr('title', 'Stop sharing note with all users');
				}
				_div_wrap.append(_div_share);
			}

			if (note.timestamp && note.timestamp != '00:00:00') {
				//_div_wrap.hide();
			}

			if (this.options.resizable) {
				_div_wrap.resizable({
					maxHeight: 1000,
					maxWidth: 1000,
					minHeight: 130,
					minWidth: 130,
					stop: function(event, ui) { 
						jQuery(this).find('textarea').css('height', jQuery(this).height() - 32);
						self.resizedNote(note.id);
					}
				});
			}

			_div_wrap.draggable({
				containment: this.container, 
				scroll: false, 
				handle: 'div.jSticky-header', 
				stop: function(event, ui){
					self.movedNote(note.id);
				}
			});

			_div_thumbnail = jQuery(document.createElement('div'))
								.addClass('jSticky-medium')
								.addClass('thumbnail')
								.attr('id', 'note-tn-' + note.id)
								.attr('data-id', note.id)
								.append(jQuery(document.createElement('p')).text(note.text))
								.on('click', function() {
									jQuery('#note-' + note.id).find('textarea').focus();
								});
			if (note.access == 1) {
				_div_wrap.addClass('annotation');
				_div_thumbnail.addClass('annotation');
			}
			jQuery("#notes-list").append(_div_thumbnail);
			this.container.append(_div_wrap);
			jQuery("#note-" + note.id).on('click', function() {
				return false;
			});

			jQuery(_note_content).css('height', jQuery('#note-' + note.id).height() - 32);
		},

		movedNote: function(note_id) {
			var note = this.getNote(note_id);

			note.pos_x = jQuery('#note-' + note_id).css('left').replace(/px/, '');
			note.pos_y = jQuery('#note-' + note_id).css('top').replace(/px/, '');

			if (_DEBUG) {
				window.console && console.log('moving note: #' + note_id + ' to x:' + note.pos_x + ', y: ' + note.pos_y);
			}

			if (this.options.moveCallback) {
				this.options.moveCallback(note);
			}
		},

		resizedNote: function(note_id) {
			var note = this.getNote(note_id);

			note.width  = jQuery("#note-" + note_id).width();
			note.height = jQuery("#note-" + note_id).height();

			if (_DEBUG) {
				window.console && console.log('resizing note: #' + note_id + ' to w:' + note.width + ', h: ' + note.height);
			}

			if (this.options.resizeCallback) {
				this.options.resizeCallback(note);
			}
		}

	};

	jQuery.fn[pluginName] = function(options) {
		return this.each(function() {
			if (!jQuery.data(this, 'plugin_' + pluginName)) {
				jQuery.data(this, 'plugin_' + pluginName, new Plugin(this, options));
			}
		});
	};

})(jQuery, window, document);
