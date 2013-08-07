jQuery(function($) {
	var pages = $('#pages li'),
		tabs = $('#page-tabs li a'),
		currentPage = $('#page-1');
		// @FIXME: offset hack for iframe (i think), although could be browser-specific issue (chrome?)
		//         offset().top not getting set correctly within iframe in chrome
		var iframeHack = 0;

	// Register some changes based on whether we're in an iframe or not
	if(window.location != window.parent.location) {
		// Move Pagination, save and done (navigation bar) if in iframe
		$('.navbar').css({
			'position': 'fixed',
			'bottom': 0,
			'left': 0,
			'right': 0,
			'background': '#222'
		});
		$('.navbar .question-info').css('color', '#FFF');
		$('.main.section.courses-form').css("margin-bottom", 60);

		iframeHack = 20;
	}

	// pagination
	tabs.click(function(evt) {
		pages.hide();
		$('#page-tabs .current').removeClass('current');
		$(evt.target).addClass('current');
		currentPage = $($(evt.target).attr('href'));
		currentPage.show();
		currentPage.css('display', 'block');
		return false;
	});
	$(pages[0]).css('display', 'block');

	var place = $('#place-inputs'),
		group = $('#group-inputs'),
		basePos = currentPage.offset();
		basePos.top -= iframeHack;

	var groupBox = null, groupOrigin = {}, x, y;
	// start drawing the box, and save one of its corners
	pages.bind('dragstart', function(evt) {
		groupOrigin.x = x;
		groupOrigin.y = y;
		groupBox = $('<div id="group-box"></div>');
		groupBox.css({ 'left': x, 'top': y });
		currentPage.append(groupBox);
		evt.preventDefault();
	});
	// modify the box to extend from the current cursor position to its origin point
	pages.bind('mousemove', function(evt) {
		x = evt.pageX - basePos.left, y = evt.pageY - basePos.top;
		if (groupBox) {
			groupBox.css({ 'left': Math.min(x, groupOrigin.x), 'top': Math.min(y, groupOrigin.y), 'width': Math.abs(x - groupOrigin.x), 'height': Math.abs(y - groupOrigin.y)});
		}
	});

	var remover = function(evt) {
		evt.preventDefault();

		var markerClass = $(evt.target.parentNode)[0].className;

		if (markerClass.search('group-marker') !== -1) {
			// decrement question count
			var questionsTotal = parseInt($('.questions-total').html(), 10);
			$('.questions-total').html(--questionsTotal);
			var questionsUnSaved = parseInt($('.questions-unsaved').html(), 10);

			if (markerClass.search('new-group-marker') === -1) {
				$('.questions-unsaved').html(++questionsUnSaved);
			} else {
				$('.questions-unsaved').html(--questionsUnSaved);
			}

			if(questionsUnSaved > 0) {
				$('#save').addClass('unsaved');
			} else {
				$('#save').removeClass('unsaved');
			}
		}

		$(evt.target.parentNode).remove();
	};

	var answerHighlighter = function(evt) {
		var prnt = $(evt.target.parentNode);
		prnt.parent().children('div').removeClass('selected');
		prnt.addClass('selected');
	};

	var groupId = 0;
	var addGroup = function(evt) {
		var marker = $(evt.target);
		var markerBase = marker.offset();
		if (!marker.hasClass('group-marker')) {
			return;
		}
		var existingRadios = marker.children('.radio-container').children('.placeholder');
		var name, value;
		if (existingRadios.length > 0) {
			name = $(existingRadios[0]).attr('name');
			value = existingRadios.length;
		}
		else {
			name = 'question-' + groupId++;
			value = 1;
		}

		var  inpDiv = $('<div class="radio-container"></div>'),
			inp = $('<input name="' + name + '" value="' + value + '" class="placeholder" type="radio" />'),
		removeInput = $('<button class="remove">x</button>');
		inp.click(answerHighlighter);
		inpDiv.append(removeInput).append(inp);
		removeInput.click(remover);
		marker.append(inpDiv);
		var x = evt.pageX - markerBase.left - inpDiv.width() + ((inpDiv.width() - 28)/2), y = evt.pageY - markerBase.top - inpDiv.height()/2;
		// snap to previous nearby inputs along the x axis to make things neater
		marker.children('.radio-container').each(function(idx, div) {
			if (Math.abs(x - parseInt($(div).css('left'))) < 10) {
				x = $(div).css('left');
			}
		});
		inpDiv.css({'top': y, 'left': x});
		evt.preventDefault();
	};

	// bind events to elements that were added on the server side
	$('.remove').click(remover);
	$('.placeholder').click(answerHighlighter);
	$('.group-marker').click(addGroup);

	var questionNum = 0;
	// make the selection box into a group area
	pages.bind('mouseup', function(evt) {
		if (!groupBox) {
			return;
		}
		// position marker over the select box
		var marker = $('<div class="group-marker new-group-marker"></div>');
		currentPage.append(marker);
		marker
			.css(groupBox.position())
			.css('height', groupBox.css('height'))
			.css('width', groupBox.css('width'));

		// add remove button
		var remove = $('<button class="remove">x</button>');
		remove.click(remover);
		marker.append(remove);

		// remove selection box
		groupBox.remove();
		groupBox = null;

		// increment question count
		var questionsTotal = parseInt($('.questions-total').html(), 10);
		$('.questions-total').html(++questionsTotal);
		var questionsUnSaved = parseInt($('.questions-unsaved').html(), 10);
		$('.questions-unsaved').html(++questionsUnSaved);
		if(questionsUnSaved > 0) {
			$('#save').addClass('unsaved');
		} else {
			$('#save').removeClass('unsaved');
		}

		marker.click(addGroup);
	});

	var saveButton = $('#save');
	saveButton.click(function(evt) {
		evt.preventDefault();
		saveButton.text('Saving...').attr('disabled', true);
		var serialized = {
			'formId': window.location.search.toString().match(/formId=(\d+)/)[1],
			'task'  : 'saveLayout',
			'pages' : [],
			'title' : $('#title').val().replace(/^\s+|\s+$/g, ''),
			'type'  : $('#asset-type').val()
		};
		if (serialized.title == '') {
			$('#title-error').text('Please enter a title for this document').show();
			$('#title').addClass('fieldWithErrors').focus();
			location.hash = '#title';
			saveButton.text('Save and Close').attr('disabled', false);
			return;
		}
		var errors = false;
		currentPage.hide();
		pages.each(function(pageNum, el) {
			el = $(el);
			el.show();
			serialized.pages[pageNum] = [];
			el.children('.group-marker').each(function(groupNum, group) {
				if (errors) {
					return;
				}
				var off = $(group).offset();
				off.top -= basePos.top;
				off.left -= basePos.left;
				off.top = Math.round(off.top);
				off.left = Math.round(off.left);
				off.height = $(group).height();
				off.width = $(group).width();
				off.answers = [];

				serialized.pages[pageNum][groupNum] = off;
				var foundCorrect = false;
				$(group).children('.radio-container').each(function(ansNum, ans) {
					off = $(ans).children('.placeholder').offset();
					off.top -= basePos.top;
					off.left -= basePos.left;
					off.top = Math.round(off.top);
					off.left = Math.round(off.left);
					off.correct = $(ans).hasClass('selected');
					foundCorrect = foundCorrect || off.correct;
					serialized.pages[pageNum][groupNum].answers.push(off);
				});
				if (!foundCorrect) {
					if (currentPage.attr('id') != $(el).attr('id')) {
						tabs[pageNum].click();
					}
					$('#layout-error').text('Ensure you have selected a correct answer for each question group').show();
					$(group).addClass('missing-answer');
					errors = true;
				}
			});
			el.hide();
		});
		currentPage.show();
		currentPage.css('display', 'block');

		if (!errors) {
			$('.error').hide();
			$.post('/courses/form', serialized, function(response) {
				saveButton.text('Save and Close').attr('disabled', false);
				$('#saved-notification').slideDown('slow');
				if (response && response.result && response.result == 'success') {
					setTimeout(function() {
						$('#saved-notification').slideUp('slow');
						parent.$('body').trigger('savesuccessful');
						window.location.href = '/courses/form';
					}, 2000);
				}
			}, 'JSON');
			$('.questions-unsaved').html(0);
			$('#save').removeClass('unsaved');
		}
	});
});
