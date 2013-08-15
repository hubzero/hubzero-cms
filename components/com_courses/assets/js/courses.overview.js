/**
 * @package     hubzero-cms
 * @file        components/com_courses/assets/js/courses.jquery.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
//  Members scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq) {
	var $ = jq,
		ellipsestext = "...",
		moretext = "more",
		lesstext = "less",
		container = $("div.course-instructors");

	if (!container.length) {
		return;
	}

	function truncate(element) {
		$(element).find('p').css({display: 'inline'});

		var theText = $(element).html(),		// Original Text
			item,							   // Current tag or text area being iterated
			convertedText = '<span class="revealText">',	// String that will represent the finished result
			limit = $("div.course-instructors").attr('data-bio-length'),						// Max characters (though last word is retained in full)
			counter = 0,						// Track how far we've come (compared to limit)
			lastTag,							// Hold a reference to the last opening tag
			lastOpenTags = [],				  // Stores an array of all opening tags (they get removed as tags are closed)
			nowHiding = false;				  // Flag to set to show that we're now in the hiding phase

		theText = theText.replace(/[\s\n\r]{2,}/g, ' ');			// Consolidate multiple white-space characters down to one. (Otherwise the counter will count each of them.)
		theText = theText.replace(/(<[^<>]+>)/g,'|*|SPLITTER|*|$1|*|SPLITTER|*|');					  // Find all tags, and add a splitter to either side of them.
		theText = theText.replace(/(\|\*\|SPLITTER\|\*\|)(\s*)\|\*\|SPLITTER\|\*\|/g,'$1$2');		   // Find consecutive splitters, and replace with one only.
		theText = theText.replace(/^[\s\t\r]*\|\*\|SPLITTER\|\*\||\|\*\|SPLITTER\|\*\|[\s\t\r]*$/g,''); // Get rid of unnecessary splitter (if any) at beginning and end.
		theText = theText.split(/\|\*\|SPLITTER\|\*\|/);			// Split theText where there's a splitter. Now we have an array of tags and words.

		for (var i in theText) {									 // Iterate over the array of tags and words.
			item = theText[i];									  // Store current iteration in a variable (for convenience)
			lastTag = lastOpenTags[lastOpenTags.length - 1];		// Store last opening tag in a variable (for convenience)
			if (!item.match(/<[^<>]+>/) ) {						 // If 'item' is not a tag, we have text
				//if (lastTag && item.charAt(0) == ' ' && !lastTag[1].match(/span|SPAN/)) item = item.substr(1);   // Remove space from beginning of block elements (like IE does) to make results match cross browser
				if (!nowHiding) {										// If we haven't started hiding yet...
					counter += item.length;							 // Add length of text to counter.
					if (counter >= limit) {							  // If we're past the limit...
						var length = item.length - 1;				   // Store the current item's length (minus one).
						var position = (length) - (counter - limit);	// Get the position in the text where the limit landed.
						while (position != length) {					 // As long as we haven't reached the end of the text...
							if ( !!item.charAt(position).match(/[\s\t\n]/) || position == length )   // Check if we have a space, or are at the end.
								break;								  // If so, break out of loop.
							else position++;							// Otherwise, increment position.
						}
						if (position != length) position--;
						var closeTag = '', openTag = '';				// Initialize open and close tag for last tag.
						if (lastTag) {								   // If there was a last tag,
							closeTag = '</' + lastTag[1] + '>';		 // set the close tag to whatever the last tag was,
							openTag = '<' + lastTag[1] + lastTag[2] + '>';  // and the open tag too.
						}
						// Create transition from revealed to hidden with the appropriate tags, and add it to our result string
						var transition = '<span class="morecontent">' + 
											'<span class="moreellipses">' + ellipsestext + '&nbsp;</span>' + 
											'<a href="#" class="more">' + moretext + '</a>' + 
										'</span>' + 
									closeTag + 
								'</span>' +
								'<span class="hide">' + openTag;
						convertedText += (position == length) ? (item).substr(0) + transition : (item).substr(0,position + 1) + transition + (item).substr(position + 1).replace(/^\s/, '&nbsp;');
						nowHiding = true; // Now we're hiding.
						continue; // Break out of this iteration.
					}
				}
			} else {												// Item wasn't text. It was a tag.
				if (!item.match(/<br>|<BR>/)) {					  // If it is a <br /> tag, ignore it.
					if (!item.match(/\//)) {						 // If it is not a closing tag...
						lastOpenTags.push(item.match(/<(\w+)(\s*[^>]*)>/));	 // Store it as the most recent open tag we've found.
					} else {													// If it is a closing tag.
						if (item.match(/<\/(\w+)>/)[1] == lastOpenTags[lastOpenTags.length - 1][1]) {	// If the closing tag is a paired match with the last opening tag...
							lastOpenTags.pop();														 // ...remove the last opening tag.
						}
						if (item.match(/<\/[pP]>/)) {			// Check if it is a closing </p> tag
							convertedText += ('<span class="paragraphBreak"><br /> <br /> </span>');	// If so, add two line breaks to form paragraph
						}
					}
				}
			}
			convertedText += (item);			// Add the item to the result string.
		}
		convertedText += ('</span>');		// After iterating over all tags and text, close the hiddenText tag.
		$(element).html(convertedText);		// Update the container with the result.
	}

	container.find('div.course-instructor-bio').each(function(i, el) {
		truncate(el);
	});

	container.find('a.more').on('click', function(e) {
		e.preventDefault();

		var self   = $(this),
			wrap   = self.closest('div.course-instructor-bio'),
			hidden = wrap.find('.hide');

		if (hidden.is(':hidden')) {
			hidden.show();
			self.siblings().hide();
			self
				.text(lesstext)
				.parent()
					.appendTo(wrap);
		} else {
			hidden.hide();
			self.siblings().show();
			self
				.text(moretext)
				.parent()
					.appendTo(wrap.find('.revealText'));
		}
	});
});
