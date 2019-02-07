/**
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

/* For compatibility mode */
if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq) {
	var $ = jq,
		timer = null;

	if ($('#curl').length > 0) {
		var img = $('#curl-img');

		$('#curl')
			.on('mouseover', function() {
				if (timer) {
					clearInterval(timer);
				}
				img.attr('src', img.attr('data-img-big'));
			})
			.on('mouseout', function() {
				timer = setTimeout(function() {
					img.attr('src', img.attr('data-img-small'));
				}, 800);
			});
	}

	if ($('#questions').length > 0) {
		if ($('#questions').attr('data-redirect')) {
			setTimeout(function() {
				var divs = ['overlay', 'questions'];
				for (var idx = 0; idx < divs.length; ++idx) {
					var div = document.getElementById(divs[idx]);
					div.parentNode.removeChild(div);
				}
			}, 4000);
		}
	}
});
