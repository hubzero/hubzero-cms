var typewatch = (function(){
	var timer = 0;
	return function(callback, ms){
		clearTimeout(timer);
		timer = setTimeout(callback, ms);
	}
})();

(function(jQuery) {

	jQuery.fn.stickyNotes = function(options) {
		jQuery.fn.stickyNotes.options = jQuery.extend({}, jQuery.fn.stickyNotes.defaults, options);
		jQuery.fn.stickyNotes.prepareContainer(this);

		/*
		if (jQuery('#notes-list').length) {
			if (typeof HUB.Presenter !== 'undefined') {
				//start timer
				var timer = setInterval(function() {
					jQuery.fn.stickyNotes.checkTimes();
				}, 5 * 1000);
			}
		}
		*/

		jQuery.each(jQuery.fn.stickyNotes.options.notes, function(index, note){
			jQuery.fn.stickyNotes.renderNote(note);
			jQuery.fn.stickyNotes.notes.push(note);
		});
	};

	jQuery.fn.stickyNotes.checkTimes = function() {
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

		jQuery.each(jQuery.fn.stickyNotes.notes, function(index, note){
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
	}

	jQuery.fn.stickyNotes.getNote = function(note_id) {
		var result = null;
		jQuery.each(jQuery.fn.stickyNotes.notes, function(index, note) {
			if (note.id == note_id) {
				result = note;
				return false;
			}
		});
		return result;
	}

	jQuery.fn.stickyNotes.getNotes = function() {
		return jQuery.fn.stickyNotes.notes;
	}

	jQuery.fn.stickyNotes.removeNote = function(note_id) {
		jQuery.each(jQuery.fn.stickyNotes.notes, function(index, note) {
			if (note.id == note_id) {
				jQuery.fn.stickyNotes.notes.splice(index, 1);
				return false;
			}
		});
	}

	jQuery.fn.stickyNotes.prepareContainer = function(container) {
		jQuery.fn.stickyNotes.container = jQuery(container);

		if (jQuery.fn.stickyNotes.options.controls) {
			jQuery.fn.stickyNotes.container.append('<button id="add_note">Add Note</button>');
			jQuery("#add_note").on('click', function() {
				jQuery.fn.stickyNotes.createNote();
				return false;
			});	
		}
	};

	jQuery.fn.stickyNotes.createNote = function() {
		var pos_x = 0,
			pos_y = 0,
			note_id = jQuery.fn.stickyNotes.notes.length + 1;

		var _note_content = jQuery(document.createElement('textarea')).on('keyup', function () {
				typewatch(function (e) {
						jQuery.fn.stickyNotes.stopEditing(note_id);
					}, 500);
				})
				.on('focus', function (e){
					jQuery(this).parent().parent().css('opacity', 1);
				})
				.on('blur', function (e){
					jQuery(this).parent().parent().css('opacity', 0.85);
				});

		var _div_note 	= 	jQuery(document.createElement('div')).addClass('jStickyNote');

		var _div_header = 	jQuery(document.createElement('div')).addClass('jSticky-header');
		//if (note.timestamp && note.timestamp != '00:00:00') {
			var tm = '00:00:00';
			if (typeof HUB.Presenter !== 'undefined') {
				var tm = HUB.Presenter.formatTime(HUB.Presenter.getCurrent());
				console.log(tm);
				//if (tm > '00:00:06') {
					_div_header.append(jQuery('<span></span>').addClass('time').text(tm));
				//}
			}
			
		//}

		_div_note.append(_note_content);
		var _div_delete = 	jQuery(document.createElement('div'))
							.addClass('jSticky-delete')
							.attr('title', 'Delete note')
							.on('click', function(){jQuery.fn.stickyNotes.deleteNote(this);});

		var _div_wrap 	= 	jQuery(document.createElement('div'))
							.css({'position':'absolute','top':pos_x,'left':pos_y, 'float' : 'left'})
							.attr('id', 'note-' + note_id)
							.attr("data-id", 0)
							.append(_div_header)
							.append(_div_note)
							.append(_div_delete);

		_div_wrap.addClass('jSticky-medium');
		if (jQuery.fn.stickyNotes.options.resizable) {
			_div_wrap.resizable({stop: function(event, ui) { jQuery.fn.stickyNotes.resizedNote(note_id)}});
		}
		_div_wrap.draggable({
			containment: jQuery.fn.stickyNotes.container, 
			scroll: false, 
			handle: 'div.jSticky-header', 
			stop: function(event, ui) {
				jQuery.fn.stickyNotes.movedNote(note_id);
			}
		}); 

		jQuery.fn.stickyNotes.container.append(_div_wrap);

		jQuery("#note-" + note_id).on('click', function() {
			return false;
		})
		jQuery("#note-" + note_id).find("textarea").focus();

		var note = {
			"id": note_id,
			"dataId": 0,
			"text": "",
			"pos_x": pos_x,
			"pos_y": pos_y,	
			"width": jQuery(_div_wrap).width(),
			"height": jQuery(_div_wrap).height(),
			"timestamp": tm
		};
		jQuery.fn.stickyNotes.notes.push(note);

		jQuery(_note_content).css('height', jQuery("#note-" + note_id).height() - 32);

		if (jQuery('#notes-list').length) {
			var _thumbnail = jQuery(document.createElement('div'))
								.attr('id', 'note-tn-' + note_id)
								.addClass('jSticky-medium')
								.addClass('thumbnail')
								.append(jQuery(document.createElement('p')));
			jQuery('#notes-list').append(_thumbnail);
		}

		if (jQuery.fn.stickyNotes.options.createCallback) {
			jQuery.fn.stickyNotes.options.createCallback(note);
		}
	}

	jQuery.fn.stickyNotes.stopEditing = function(note_id) {
		var note = jQuery.fn.stickyNotes.getNote(note_id);
		note.text = jQuery("#note-" + note_id).find('textarea').val();

		if (jQuery.fn.stickyNotes.options.editCallback) {
			jQuery.fn.stickyNotes.options.editCallback(note);
		}
	};

	jQuery.fn.stickyNotes.deleteNote = function(delete_button) {
		var note_id = jQuery(delete_button).parent().attr("id").replace(/note-/, "");
		var note = jQuery.fn.stickyNotes.getNote(note_id);
		jQuery("#note-" + note_id).remove();

		if (jQuery.fn.stickyNotes.options.deleteCallback) {
			jQuery.fn.stickyNotes.options.deleteCallback(note);
		}

		if (jQuery('#note-tn-' + note_id).length) {
			jQuery('#note-tn-' + note_id).remove();
		}

		jQuery.fn.stickyNotes.removeNote(note_id);
	}

	jQuery.fn.stickyNotes.renderNote = function(note) {
		var _p_note_text = 	jQuery(document.createElement('p')).attr("id", "p-note-" + note.id)
							.html( note.text);
		var _div_note 	= 	jQuery(document.createElement('div')).addClass('jStickyNote');

		var _div_header = 	jQuery(document.createElement('div')).addClass('jSticky-header');
		if (note.timestamp && note.timestamp != '00:00:00') {
			_div_header.append(jQuery('<span></span>').addClass('time').text(note.timestamp));
		}

		var _note_content = jQuery(document.createElement('textarea')).val(note.text).on('keyup', function (e) {
				typewatch(function () {
					jQuery.fn.stickyNotes.stopEditing(note.id);
				}, 500);
			})
			.on('focus', function (e){
				jQuery(this).parent().parent().css('opacity', 1);
			})
			.on('blur', function (e){
				jQuery(this).parent().parent().css('opacity', 0.85);
			});
		_div_note.append(_note_content);

		var _div_delete = 	jQuery(document.createElement('div'))
							.text('x')
							.addClass('jSticky-delete')
							.attr('title', 'Delete note')
							.on('click', function(){jQuery.fn.stickyNotes.deleteNote(this);});

		var _div_wrap 	= 	jQuery(document.createElement('div'))
							.css({'position':'absolute','top':note.pos_y,'left':note.pos_x, "width":note.width,"height":note.height}) //'float': 'left',
							.attr("id", "note-" + note.id)
							.attr("data-id", note.id)
							.addClass('jSticky-medium')
							.append(_div_header)
							.append(_div_note)
							.append(_div_delete);

		if (note.timestamp && note.timestamp != '00:00:00') {
			//_div_wrap.hide();
		}

		if (jQuery.fn.stickyNotes.options.resizable) {
			_div_wrap.resizable({stop: function(event, ui) { jQuery.fn.stickyNotes.resizedNote(note.id)}});
		}

		_div_wrap.draggable({
			containment: jQuery.fn.stickyNotes.container, 
			scroll: false, 
			handle: 'div.jSticky-header', 
			stop: function(event, ui){
				jQuery.fn.stickyNotes.movedNote(note.id);
			}
		});

		jQuery.fn.stickyNotes.container.append(_div_wrap);
		jQuery("#note-" + note.id).on('click', function() {
			return false;
		})

		jQuery(_note_content).css('height', jQuery('#note-' + note.id).height() - 32);
	}

	jQuery.fn.stickyNotes.movedNote = function(note_id) {
		var note = jQuery.fn.stickyNotes.getNote(note_id);

		note.pos_x = jQuery('#note-' + note_id).css('left').replace(/px/, '');
		note.pos_y = jQuery('#note-' + note_id).css('top').replace(/px/, '');

		if (jQuery.fn.stickyNotes.options.moveCallback) {
			jQuery.fn.stickyNotes.options.moveCallback(note);
		}
	}

	jQuery.fn.stickyNotes.resizedNote = function(note_id) {
		var note = jQuery.fn.stickyNotes.getNote(note_id);

		note.width  = jQuery("#note-" + note_id).width();
		note.height = jQuery("#note-" + note_id).height();

		if (jQuery.fn.stickyNotes.options.resizeCallback) {
			jQuery.fn.stickyNotes.options.resizeCallback(note);
		}
	}

	jQuery.fn.stickyNotes.defaults = {
		notes: [],
		resizable: true,
		controls: true,
		editCallback: false, 
		createCallback: false,
		deleteCallback: false,
		moveCallback: false,
		resizeCallback: false
	};

	jQuery.fn.stickyNotes.options = null;

	jQuery.fn.stickyNotes.notes = new Array();
})(jQuery);
