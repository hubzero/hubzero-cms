/**
 * @package     hubzero-cms
 * @file        plugins/hubzero/wikieditorwykiwyg/wikieditorwykiwyg.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!String.prototype.trim) {
	String.prototype.trim = function() {
		return this.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
	};
}
if (!String.prototype.ltrim) {
	String.prototype.ltrim = function() {
		return this.replace(/^\s+/,'');
	};
}
if (!String.prototype.rtrim) {
	String.prototype.rtrim = function() {
		return this.replace(/\s+$/,'');
	};
}

var WYKIWYG = {};

function T$(i) {
	return document.getElementById(i);
}
function T$$$() {
	return document.all ? 1 : 0;
}

WYKIWYG.converter = function() {
	// HTML to Wiki syntax
	this.makeWiki = function(string) {
		var columns = {
			count: 0,
			cursor: 0
		};
		
		var htmlblocks = [];
		
		var ELEMENTS = [
			{
				patterns: 'p',
				replacement: function(str, attrs, innerHTML) {
					return innerHTML ? '\n\n' + innerHTML + '\n' : '';
				}
			},
			{
				patterns: 'span',
				replacement: function(str, attrs, innerHTML) {
					var style = attrs.match(attrRegExp('style')),
							id = attrs.match(attrRegExp('id'));
					//return innerHTML ? '[[Span(' + innerHTML + (style && style[1] ? ', style=' + style[1] : '' ) + (id && id[1] ? ', id=' + id[1] : '' ) + ')]]' : str;
					return innerHTML ? innerHTML : '';
				}
			},
			{
				patterns: 'div([1-6])',
				replacement: function(str, dlevel, attrs, innerHTML) {
					var style = attrs.match(attrRegExp('style')),
						id = attrs.match(attrRegExp('id')),
						cls = attrs.match(attrRegExp('class')),
						replace = '';

					if (innerHTML == '<!-- columns -->') {
						return replace;
					}
					
					innerHTML = innerHTML.replace(/^\n\n/, '\n');
					
					if (cls && cls[1].indexOf('columns') == -1) {
						if (cls[1] == 'html') {
							htmlblocks.push('{{{\n#!html\n' + innerHTML.rtrim() + '\n}}}\n');
							return '#!HTML' + (htmlblocks.length - 1);
						}
						replace += innerHTML ? '\n\n' + '[[Div(start' + (style && style[1] ? ', style=' + style[1] : '' ) + (cls && cls[1] ? ', class=' + cls[1] : '' ) + (id && id[1] ? ', id=' + id[1] : '' ) + ')]]' + innerHTML + '[[Div(end)]]' : '';
					} else if (cls && cls[1]) {
						columns.cursor++;
						
						var num = cls[1].match(/two|three|four|five|six/gi);
						
						cls[1] = cls[1].replace(/columns/, '') // remove columns declaration
										.replace(/first|second|third|fourth|fifth|sixth/, '') // remove column count
										.replace(/two|three|four|five|six/, '') // remove coulmn number declaration
										.replace(/^\s+|\s+$/g,""); // trim whitespace
						switch (num[0])
						{
							case 'six':   columns.count = 6; break;
							case 'five':  columns.count = 5; break;
							case 'four':  columns.count = 4; break;
							case 'three': columns.count = 3; break;
							case 'two':   columns.count = 2; break;
							case '': 
							default: break;
						}
						
						if (columns.cursor == 1) {
							replace += '[[Column('+columns.count+')]]\n';
						} else if (columns.cursor == columns.count) {
							columns.cursor = 0;
							columns.count = 0;
						}
						replace += innerHTML ? '\n' + '[[Column(start' + (style && style[1] ? ', style=' + style[1] : '' ) + (cls && cls[1] ? ', class=' + cls[1] : '' ) + (id && id[1] ? ', id=' + id[1] : '' ) + ')]]' + innerHTML + '[[Column(end)]]\n' : '';
					} else {
						replace += innerHTML + '\n';
					}

					return replace;
				}
			},
			{
				patterns: 'br',
				type: 'void',
				replacement: '[[br]]'
			},
			{
				patterns: 'h([1-6])',
				replacement: function(str, hLevel, attrs, innerHTML) {
					var hPrefix = '';
					for(var i = 0; i < hLevel; i++) {
						hPrefix += '=';
					}
					return '\n\n' + hPrefix + ' ' + innerHTML + ' ' + hPrefix + '\n';
				}
			},
			{
				patterns: 'hr',
				type: 'void',
				replacement: '\n\n----\n'
			},
			{
				patterns: 'a',
				replacement: function(str, attrs, innerHTML) {
					var href = attrs.match(attrRegExp('href')),
						title = attrs.match(attrRegExp('title')),
						rel = attrs.match(attrRegExp('rel'));
					if (rel && rel[1] == 'filemacro') {
						return '[[File(' + href[1] + (href[1] == innerHTML ? '' : ', desc="' + innerHTML + '"') + ')]]';
					}
					return href ? '[' + href[1] + (href[1] == innerHTML ? '' : ' ' + innerHTML) + ']' : str;
				}
			},
			{
				patterns: ['b', 'strong'],
				replacement: function(str, attrs, innerHTML) {
					return innerHTML ? "'''" + innerHTML + "'''" : '';
				}
			},
			{
				patterns: ['i', 'em'],
				replacement: function(str, attrs, innerHTML) {
					return innerHTML ? "''" + innerHTML + "''" : '';
				}
			},
			{
				patterns: ['code', 'tt'],
				replacement: function(str, attrs, innerHTML) {
					var cls = attrs.match(attrRegExp('class'));
					if (cls && cls[1] == 'wiki-math') {
						return innerHTML ? '<math>' + innerHTML + '</math>' : '';
					}
					return innerHTML ? '`' + innerHTML + '`' : '';
				}
			},
			{
				patterns: 'sup',
				replacement: function(str, attrs, innerHTML) {
					return innerHTML ? '^' + innerHTML + '^' : '';
				}
			},
			{
				patterns: 'sub',
				replacement: function(str, attrs, innerHTML) {
					return innerHTML ? ',,' + innerHTML + ',,' : '';
				}
			},
			{
				patterns: 'u',
				replacement: function(str, attrs, innerHTML) {
					return innerHTML ? '__' + innerHTML + '__' : '';
				}
			},
			{
				patterns: 'ins',
				replacement: function(str, attrs, innerHTML) {
					return innerHTML ? '+' + innerHTML + '+' : '';
				}
			},
			{
				patterns: 'cite',
				replacement: function(str, attrs, innerHTML) {
					return innerHTML ? '??' + innerHTML + '??' : '';
				}
			},
			{
				patterns: ['del', 'strike'],
				replacement: function(str, attrs, innerHTML) {
					return innerHTML ? '~~' + innerHTML + '~~' : '';
				}
			},
			{
				patterns: 'img',
				type: 'void',
				replacement: function(str, attrs, innerHTML) {
					var src = attrs.match(attrRegExp('src')),
						alt = attrs.match(attrRegExp('alt')),
						title = attrs.match(attrRegExp('title'));
					return '[[Image(' + src[1] + (title && title[1] ? ' "' + title[1] + '"' : '') + (alt && alt[1] ? ', alt="' + alt[1] + '"' : '') + ')]]';
				}
			}
		];

		//string = string.replace(/((^|\s)\[([^\[])(.*?)\])/g, '$2[!$3$4]');

		string = string.replace(/\&nbsp;/ig, ' ');
		string = string.replace(/<div><br(\s*)\/?><\/div>/ig, '\n');

		// THandle nested DIVs
		string = countDivs(string);
		
		function countDivs(text) {
			var j = 0;

			lines = text.split('\n');
			
			for (var i = 0, len = lines.length; i < len; i++) 
			{
				lines[i] = lines[i].replace(/(<div\b([^>]*)>)/g,
					function(wholeMatch, m1, m2) {
						j++;
						if (j == 1) {
							//wholeMatch += j;
							wholeMatch = '<div1 ' + m2 + '>';
						}
						return wholeMatch;
					}
				);

				lines[i] = lines[i].replace(/(<\/div>)/g,
					function(wholeMatch) {
						if (j == 1) {
							//wholeMatch = j + wholeMatch;
							wholeMatch = '</div1>';
						}
						j--;
						return wholeMatch;
					}
				);
			}
			
			text = lines.join('\n');
			
			return text;
		}

		for (var i = 0, len = ELEMENTS.length; i < len; i++) {
			if (typeof ELEMENTS[i].patterns === 'string') {
				string = replaceEls(string, { tag: ELEMENTS[i].patterns, replacement: ELEMENTS[i].replacement, type: ELEMENTS[i].type });
			}
			else {
				for (var j = 0, pLen = ELEMENTS[i].patterns.length; j < pLen; j++) {
					string = replaceEls(string, { tag: ELEMENTS[i].patterns[j], replacement: ELEMENTS[i].replacement, type: ELEMENTS[i].type });
				}
			}
		}

		function replaceEls(html, elProperties) {
			var pattern = elProperties.type === 'void' ? '<' + elProperties.tag + '\\b([^>]*)\\/?>' : '<' + elProperties.tag + '\\b([^>]*)>([\\s\\S]*?)<\\/' + elProperties.tag + '>',
					regex = new RegExp(pattern, 'gi'),
					markdown = '';
			if (typeof elProperties.replacement === 'string') {
				markdown = html.replace(regex, elProperties.replacement);
			}
			else {
				markdown = html.replace(regex, function(str, p1, p2, p3) {
					return elProperties.replacement.call(this, str, p1, p2, p3);
				});
			}
			return markdown;
		}

		function attrRegExp(attr) {
			return new RegExp(attr + '\\s*=\\s*["\']?([^"\']*)["\']?', 'i');
		}

		// Pre code blocks
		string = string.replace(/<pre\b[^>]*>([\s\S]*?)<\/pre>/gi, function(str, innerHTML) {
			innerHTML = innerHTML.replace(/^\t+/g, '	'); // convert tabs to spaces (you know it makes sense)
			return '\n\n{{{\n' + innerHTML.rtrim() + '\n}}}\n';
		});

		// Lists
		// Escape numbers that could trigger an ol
		//string = string.replace(/(\d+). /g, '$1\\. ');
		string = string.replace(/<li>\s*<div>(.+?)<\/div><\/li>/g, '<li>$1</li>');

		// Converts lists that have no child lists (of same type) first, then works it's way up
		var noChildrenRegex = /<(ul|ol)\b[^>]*>(?:(?!<ul|<ol)[\s\S])*?<\/\1>/gi;
		while(string.match(noChildrenRegex)) {
			string = string.replace(noChildrenRegex, function(str) {
				return replaceLists(str);
			});
		}

		function replaceLists(html) {
			html = html.replace(/<(ul|ol)\b[^>]*>([\s\S]*?)<\/\1>/gi, function(str, listType, innerHTML) {
				var lis = innerHTML.split('</li>');
				lis.splice(lis.length - 1, 1);
				for (i = 0, len = lis.length; i < len; i++) {
					if (lis[i]) {
						var prefix = (listType === 'ol') ? " # " : " * ";
						lis[i] = lis[i].replace(/\s*<li[^>]*>([\s\S]*)/i, function(str, innerHTML) {
							innerHTML = innerHTML.replace(/^\s+/, '');
							innerHTML = innerHTML.replace(/\n\n/g, '\n\n		');
							// indent nested lists
							innerHTML = innerHTML.replace(/\n([ ]*)+(\*|#|\d+\.) /g, '\n$1 $2 ');
							return prefix + innerHTML;
						});
					}
				}
				return lis.join('\n');
			});
			return html; //html.replace(/[ \t]+\n|\s+$/g, '');
		}

		// Converts lists that have no child lists (of same type) first, then works it's way up
		var noDefChildrenRegex = /<(dl)\b[^>]*>(?:(?!<dl)[\s\S])*?<\/\1>/gi;
		while(string.match(noDefChildrenRegex)) {
			string = string.replace(noDefChildrenRegex, function(str) {
				return replaceDefLists(str);
			});
		}

		function replaceDefLists(html) {
			html = html.replace(/<(dl)\b[^>]*>([\s\S]*?)<\/\1>/gi, function(str, listType, innerHTML) {
				var dts = innerHTML.split('</dt>');
				for (i = 0, len = dts.length; i < len; i++) {
					if (dts[i]) {
						var prefix = " ";
						dts[i] = dts[i].replace(/\s*<dt[^>]*>([\s\S]*)/i, function(str, innerHTML) {
							innerHTML = innerHTML.replace(/^\s+/, '');
							return ' ' + innerHTML + '::';
						});
						dts[i] = dts[i].replace(/\s*<dd[^>]*>([\s\S]*)<\/dd>/i, function(str, innerHTML) {
							innerHTML = innerHTML.replace(/^\s+/, '');
							return '   ' + innerHTML + '\n';
						});
					}
				}
				return dts.join('\n');
			});
			return '\n\n' + html.replace(/[ \t]+\n|\s+$/g, '');
		}

		// Tables
		var deepest = /<table\b[^>]*>((?:(?!<table)[\s\S])*?)<\/table>/gi;
		while(string.match(deepest)) {
			string = string.replace(deepest, function(str) {
				return replaceTables(str);
			});
		}

		function replaceTables(html) {
			html = html.replace(/<table\b[^>]*>([\s\S]*?)<\/table>/gi, function(str, inner) {
				inner = inner.replace(/^\s+|\s+$/g, '');
				inner = inner.replace(/<tbody>/g, '');
				inner = inner.replace(/<\/tbody>/g, '');
				//inner = inner.replace(/<tr><t[dh]>(.+?)<\/t[dh]><\/tr>/g, '||$1||');
				//inner = inner.replace(/<\/t[dh]><t[dh]>/g, '||');
				inner = inner.replace(/<tr>(.+?)<\/tr>/g, '|$1|');
				inner = inner.replace(/<\/?td>/g, '|');
				inner = inner.replace(/<th>/g, '|=');
				inner = inner.replace(/<\/th>/g, '=|');
				inner = cleanUp(inner);
				return inner;
			});
			return html;
		}

		// Blockquotes
		var deepest = /<blockquote\b[^>]*>((?:(?!<blockquote)[\s\S])*?)<\/blockquote>/gi;
		while(string.match(deepest)) {
			string = string.replace(deepest, function(str) {
				return replaceBlockquotes(str);
			});
		}

		function replaceBlockquotes(html) {
			html = html.replace(/<blockquote\b[^>]*>([\s\S]*?)<\/blockquote>/gi, function(str, inner) {
				inner = inner.replace(/^\s+|\s+$/g, '');
				inner = cleanUp(inner);
				inner = inner.replace(/^/gm, '  ');
				inner = inner.replace(/^(([ \t]{3,})+)/gm, '    ');
				return inner;
			});
			return html;
		}

		function cleanUp(string) {
			string = string.replace(/<div>(.+?)<\/div>/ig, '\n\n$1\n');
			string = string.replace(/^[\t\r\n]+|[\t\r\n]+$/g, ''); // trim leading/trailing whitespace
			string = string.replace(/\n\s+\n/g, '\n\n');
			string = string.replace(/\n{3,}/g, '\n\n'); // limit consecutive linebreaks to 2
			return string;
		}

		string = cleanUp(string);

		// Put back HTML blocks
		for (i = 0, len = htmlblocks.length; i < len; i++) {
			string = string.replace('#!HTML' + i, htmlblocks[i]);
		}
		string = string.replace(/&amp;/g,"&");
		string = string.replace(/&lt;/g,"<");
		string = string.replace(/&gt;/g,">");
		string = string.replace(/\n{3,}/g, '\n\n'); // limit consecutive linebreaks to 2

		return string;
	},

	// Wiki syntax to HTML
	this.makeHtml = function(text) {
		// Global hashes, used by various utility routines
		var g_urls;
		var g_titles;
		var g_html_blocks;
		var h_html_blocks;
		var g_macros;

		// Used to track when we're inside an ordered or unordered list
		// (see _ProcessListItems() for details):
		var g_list_level = 0;
		var g_table_level = 0;
		
		function _StripMacros(text) {
			var text = text.replace(/(\[\[(.+)\]\])/gm,
				function (wholeMatch, m1, m2) {
					var key = Math.floor(Math.random()*10001);

					g_macros[key] = m1;

					// Completely remove the definition from the text
					return "MACRO%%"+key+"%%";
				}
			);

			return text;
		}

		function _UnStripMacros(text) {
			var text = text.replace(/(MACRO%%(\d+)%%)/gm,
				function (wholeMatch, m1, m2) {
					return g_macros[m2];
				}
			);

			return text;
		}
		
		function _HashHTMLBlocks(text) {
			// Double up blank lines to reduce lookaround
			text = text.replace(/\n/g,"\n\n");

			// Hashify HTML blocks:
			// We only want to do this for block-level HTML tags, such as headers,
			// lists, and tables. That's because we still want to wrap <p>s around
			// "paragraphs" that are wrapped in non-block-level tags, such as anchors,
			// phrase emphasis, and spans. The list of tags we're looking for is
			// hard-coded:
			var block_tags_a = "p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|script|noscript|form|fieldset|iframe|math|ins|del"
			var block_tags_b = "p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|script|noscript|form|fieldset|iframe|math"

			// First, look for nested blocks, e.g.:
			//   <div>
			//     <div>
			//     tags for inner block must be indented.
			//     </div>
			//   </div>
			//
			// The outermost tags must start at the left margin for this to match, and
			// the inner nested divs must be indented.
			// We need to do this before the next, more liberal match, because the next
			// match will start at the first `<div>` and stop at the first `</div>`.
			// 
			// This regex can be expensive when it fails.
			text = text.replace(/^(<(p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|script|noscript|form|fieldset|iframe|math|ins|del)\b[^\r]*?\n<\/\2>[ \t]*(?=\n+))/gm,hashElement);

			// Now match more liberally, simply from `\n<tag>` to `</tag>\n`
			text = text.replace(/^(<(p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|script|noscript|form|fieldset|iframe|math)\b[^\r]*?.*<\/\2>[ \t]*(?=\n+)\n)/gm,hashElement);

			// Special case just for <hr />. It was easier to make a special case than
			// to make the other regex more complicated.  
			text = text.replace(/(\n[ ]{0,3}(<(hr)\b([^<>])*?\/?>)[ \t]*(?=\n{2,}))/g,hashElement);

			// Special case for standalone HTML comments:
			text = text.replace(/(\n\n[ ]{0,3}<!(--[^\r]*?--\s*)+>[ \t]*(?=\n{2,}))/g,hashElement);

			// PHP and ASP-style processor instructions (<?...?> and <%...%>)
			text = text.replace(/(?:\n\n)([ ]{0,3}(?:<([?%])[^\r]*?\2>)[ \t]*(?=\n{2,}))/g,hashElement);

			//  Undo double lines (see comment at top of this function)
			text = text.replace(/\n\n/g,"\n");
			return text;
		}

		function _DoMath(text) {
			text = text.replace(/(<(math)>(.*?)<\/\2>)/g, function(wholeMatch, m1, m2, m3){
				var tag = m2,
					content = m3;

				return '<code class="wiki-math">' + content + '</code>';
			});
			return text;
		}

		function hashElement(wholeMatch,m1) {
			var blockText = m1;

			// Undo double lines
			blockText = blockText.replace(/\n\n/g,"\n");
			blockText = blockText.replace(/^\n/,"");

			// strip trailing blank lines
			blockText = blockText.replace(/\n+$/g,"");

			// Replace the element text with a marker ("~KxK" where x is its key)
			blockText = "\n\n~K" + (g_html_blocks.push(blockText)-1) + "K\n\n";

			return blockText;
		};

		function _RunBlockGamut(text) {
			// These are all the transformations that form block-level
			// tags like paragraphs, headers, and list items.

			text = _DoHeaders(text);

			// Do Horizontal Rules:
			var key = hashBlock("<hr />");
			text = text.replace(/^[ ]{0,2}([ ]?\-[ ]?){4,}[ \t]*$/gm,key);

			/*text = _stripCodeBlocks(text);
			text = _DoHtmlCodeBlocks(text);
			text = _DoCodeBlocks(text);
			$('#wtest').val(text);*/
			
			text = _DoLists(text);
			text = _DoDefinitionLists(text);
			text = _DoTables(text);
			//text = _DoCodeBlocks(text);
			text = _DoBlockQuotes(text);

			// We already ran _HashHTMLBlocks() before, but that
			// was to escape raw HTML in the original source. This time,
			// we're escaping the markup we've just created, so that we don't wrap
			// <p> tags around block-level tags.
			text = _HashHTMLBlocks(text);
			text = _FormParagraphs(text);

			return text;
		}

		function _RunSpanGamut(text) {
			// These are all the transformations that occur *within* block-level
			// tags like paragraphs, headers, and list items.

			text = _DoCodeSpans(text);
			//text = _DoSpans(text);
			text = _EscapeSpecialCharsWithinTagAttributes(text);
			text = _EncodeBackslashEscapes(text);
			//text = text.replace(/\[\[br\]\]/gi,"<br />");
			// Process anchor tags.
			text = _DoAnchors(text);

			// Make links out of things like `http://example.com/`
			// Must come after _DoAnchors(), otherwise declared links [http://something.com]
			// will get converted to [<a href="http://something.com">http://something.com</a>]
			//text = _DoAutoLinks(text);
			text = _EncodeAmpsAndAngles(text);
			text = _DoItalicsAndBold(text);

			// Do hard breaks:
			text = text.replace(/  +\n/g," <br />\n");
			

			return text;
		}

		function _EscapeSpecialCharsWithinTagAttributes(text) {
			// Within tags -- meaning between < and > -- encode [\ ` * _] so they
			// don't conflict with their use in Markdown for code, italics and strong.

			// Build a regex to find HTML tags and comments.  See Friedl's 
			// "Mastering Regular Expressions", 2nd Ed., pp. 200-201.
			var regex = /(<[a-z\/!$]("[^"]*"|'[^']*'|[^'">])*>|<!(--.*?--\s*)+>)/gi;

			text = text.replace(regex, function(wholeMatch) {
				var tag = wholeMatch.replace(/(.)<\/?code>(?=.)/g,"$1`");
				tag = escapeCharacters(tag,"\\`*_");
				return tag;
			});

			return text;
		}

		function _DoSpans(text) {
			// Turn Wiki image macros [[(url, alt="optional title")]] into <img> tags.

			// Don't forget: encode * and _
			text = text.replace(/(\[\[Span\((.*?)\)\]\])/gi, function(wholeMatch, m1, m2){
				var atts = m2,
					a = [],
					content = m2;

				atts = atts.split(',');

				for (var i=0; i < atts.length; i++) {
					atts[i] = atts[i].replace(/^\s+|\s+$/g,"");
					if (atts[i] == '') {
						continue;
					}
					if (i == 0) {
						content = atts[i];
					}
					var item = atts[i].split('=');
					if (item.length > 1) {
						item[0] = item[0].replace(/^\s+|\s+$/g,"").replace(/^['"]|['"]$/g,'').replace(/"/g,"&quot;");
						item[1] = item[1].replace(/^\s+|\s+$/g,"").replace(/^['"]|['"]$/g,'').replace(/"/g,"&quot;");
						a.push(item[0] + '="' + escapeCharacters(item[1],"*_") + '"');
					}
				}
				
				return '<span' + (a.length > 0 ? ' ' + a.join(' ') : '') + '>' + _RunSpanGamut(content) + '</span>';
			});
			return text;
		}

		function _DoAnchors(text) {
			// Turn Wiki links [url link text] into XHTML <a> tags.
			text = text.replace(/(\[[ \t]*()()(.*?)[ \t]*()([ ](.*?)[ \t]*)?\])/g,writeAnchorTag);

			return text;
		}
		
		function writeAnchorTag(wholeMatch,m1,m2,m3,m4,m5,m6,m7) {
			if (m7 == undefined) m7 = "";
			var whole_match = m1;
			var link_text   = m2;
			var link_id	 = m3.toLowerCase();
			var url		= m4;
			var title	= m7;

			if (url[0] == '!') {
				return '[' + whole_match.substr(2);
			}

			if (url == "") {
				if (whole_match.search(/\(\s*\)$/m)>-1) {
					// Special case for explicit empty url
					url = "";
				} else {
					return whole_match;
				}
			}	

			url = escapeCharacters(url,"*_");
			var result = (whole_match.substr(0, 1) == ' ' ? ' ' : '') + "<a href=\"" + url + "\"";
			
			if (title != "" && title != url) {
				title = title.replace(/"/g,"&quot;");
				title = escapeCharacters(title,"*_");
				result +=  " title=\"" + title + "\"";
			} else {
				title = url;
			}

			if (!link_text || link_text == ' ') {
				link_text = title;
			}

			result += ">" + link_text + "</a>" + (whole_match.substr(-1, 1) == ' ' ? ' ' : '');

			return result;
		}

		function _DoImages(text) {
			// Turn Wiki image macros [[(url, alt="optional title")]] into <img> tags.

			// Don't forget: encode * and _
			text = text.replace(/(\[\[Image\((.*?)\)\]\])/g,writeImageTag);
			return text;
		}

		function writeImageTag(wholeMatch,m1,m2) {
			var whole_match = m1,
				url         = m2;

			if (!title) title = '';

			if (url == '') {
				return whole_match;
			}	

			alt_text = alt_text.replace(/"/g,"&quot;");
			url = escapeCharacters(url,"*_");
			var result = '<img src="' + url + '" alt="' + alt_text + '"';

			if (title != '') {
				title = title.replace(/"/g,"&quot;");
				title = escapeCharacters(title,"*_");
				result +=  ' title="' + title + '"';
			}

			result += ' />';

			return result;
		}
		
		function _DoFiles(text) {
			// Handle macros:  [[File(url, alt="optional title")]]
			// Don't forget: encode * and _
			text = text.replace(/(\[\[\F\i\l\e\((.*?)\)\]\])/g,writeFileTag);
			return text;
		}

		function writeFileTag(wholeMatch,m1,m2) {
			var whole_match = m1,
				url	        = m2;

			if (!title) title = '';

			if (url == '') {
				return whole_match;
			}	

			url = escapeCharacters(url,"*_");
			var result = '<a rel="filemacro" href="' + url + '"';

			if (title != '') {
				title = title.replace(/"/g,"&quot;");
				title = escapeCharacters(title,"*_");
				result +=  ' title="' + title + '"';
			} else {
				title = url;
			}

			result += '>' + title + '</a>';

			return result;
		}
		
		function _DoDivs(text) {
			// Turn Wiki div macros [[Div(start, attribute=value)]]content[[Div(end)]] into <div> tags.

			// Don't forget: encode * and _
			text = text.replace(/(?:\n|^)(\[\[Div\(start(.*?)\)\]\]([\s\S]*?)\[\[Div\(end\)\]\])/gi, writeDivTag);
			return text;
		}

		function writeDivTag(wholeMatch, m1, m2, m3, m4) {
			var whole_match = m1,
				atts        = m2,
				content     = m3;

			var a = [];
			atts = atts.split(',');
			
			for (var i=0; i < atts.length; i++) {
				atts[i] = atts[i].replace(/^\s+|\s+$/g,"");
				if (atts[i] == '') {
					continue;
				}
				var item = atts[i].split('=');
				if (item.length > 1) {
					item[0] = item[0].replace(/^\s+|\s+$/g,"").replace(/^['"]|['"]$/g,'').replace(/"/g,"&quot;");
					item[1] = item[1].replace(/^\s+|\s+$/g,"").replace(/^['"]|['"]$/g,'').replace(/"/g,"&quot;");
					a.push(item[0] + '="' + escapeCharacters(item[1],"*_") + '"');
				}
			}
			
			//var result = '<div' + (a.length > 0 ? ' ' + a.join(' ') : '') + '>' + "\n" + _RunBlockGamut(content) + "\n" + '</div>';
			var result = '<div' + (a.length > 0 ? ' ' + a.join(' ') : '') + '>' + "\n" + _RunBlockGamut(content) + "\n" + '</div>';

			return result;
		}
		
		var columns = {
			count: 0,
			cls: '',
			current: 0
		};
		
		function _DoColumns(text) {
			// Turn Wiki column macros [[Column(start, attribute=value)]]content[[column(end)]] into <div> tags.
			//text = text.replace(/(\[\[Column\((\d)\)\]\]([\s\S]*?)\]\](?=((\n{2,}[^\[]|\[\[Column\(\d|$))))/gi, function(wholeMatch, m1, m2, m3, m4) {
			text = text.replace(/(\[\[Column\((\d)\)\]\]([\s\S]*?)(?=(\[\[Column\(\d|$)))/gi, function(wholeMatch, m1, m2, m3, m4) {
				var whole_match = m1,
					cols        = parseInt(m2),
					content     = m3;

				columns.current = 0;
				columns.count = cols;

				switch (cols)
				{
					case 6: columns.cls = 'six'; break;
					case 5: columns.cls = 'five'; break;
					case 4: columns.cls = 'four'; break;
					case 3: columns.cls = 'three'; break;
					case 2: columns.cls = 'two'; break;
					case 1: 
					default: columns.cls = ''; break;
				}

				result = content.replace(/(?:\n\n|^)(\[\[Column\(start(.*?)\)\]\]([\s\S]*?)\[\[Column\(end\)\]\])/gi, writeColumnTag);

				columns.cls = '';
				columns.count = 0;
				columns.current = 0;

				return result;
			});
			return text;
		}

		function writeColumnTag(wholeMatch, m1, m2, m3, m4) {
			var whole_match = m1,
				atts        = m2,
				content     = m3,
				a = [],
				cls = [columns.cls, 'columns'];

			columns.current++;
			
			atts = atts.split(',');
			
			for (var i=0; i < atts.length; i++) {
				atts[i] = atts[i].replace(/^\s+|\s+$/g,"");
				if (atts[i] == '') {
					continue;
				}
				var item = atts[i].split('=');
				if (item.length > 1) {
					item[0] = item[0].replace(/^\s+|\s+$/g,"").replace(/^['"]|['"]$/g,'').replace(/"/g,"&quot;");
					item[1] = item[1].replace(/^\s+|\s+$/g,"").replace(/^['"]|['"]$/g,'').replace(/"/g,"&quot;");
					
					if (item[0] == 'class') {
						cls.push(escapeCharacters(item[1],"*_"));
						continue;
					}
					
					a.push(item[0] + '="' + escapeCharacters(item[1],"*_") + '"');
				}
			}
			
			switch (columns.current)
			{
				case 6: cls.push('sixth'); break;
				case 5: cls.push('fifth'); break;
				case 4: cls.push('fourth'); break;
				case 3: cls.push('third'); break;
				case 2: cls.push('second'); break;
				case 1: 
				default: cls.push('first'); break;
			}
			
			var result = '<div' + (cls.length > 0 ? ' class="' + cls.join(' ') + '"' : '') + (a.length > 0 ? ' ' + a.join(' ') : '') + '>' + _RunBlockGamut(content) + '</div>' + "\n\n";
			if (columns.current == columns.count) {
				result += "\n\n~K" + (g_html_blocks.push('<div class="clear"><!-- columns --></div>')-1) + "K\n\n"; //'<div class="clear">\n<!-- columns -->\n</div>';
			}

			return result;
		}

		function _DoHeaders(text) {
			//  = Header 1 =
			//  == Header 2 ==
			//  ...
			//  ====== Header 6 ======

			//text = text.replace(/^((\={1,6})(.+?)\=*[ \t]*[\#]*(\S*))\n+/gm,
			for (var i = 6; i >= 1; --i)
			{
				var regex = new RegExp('^(={' + i + '})(.+)(={' + i + '})[ \t]*[#]*(\S*)\n*', 'gm');
				text = text.replace(regex,
					function(wholeMatch, m1, m2, m3, m4) {
						//console.log('"' + wholeMatch + '" "' + m2 + '"');
						var h_level = m1.length;
						//console.log("<h" + h_level + (m4 ? ' id="'+m4+'"' : '') + ">" + _RunSpanGamut(m2) + "</h" + h_level + ">");
						return hashBlock("<h" + h_level + (m4 ? ' id="'+m4+'"' : '') + ">" + _RunSpanGamut(m2.trim()) + "</h" + h_level + ">");
					});
			}

			return text;
		}

		// This declaration keeps Dojo compressor from outputting garbage:
		var _ProcessListItems;

		function _DoLists(text) {
			// Form HTML ordered (numbered) and unordered (bulleted) lists.

			// add sentinel to hack around khtml/safari bug:
			// http://bugs.webkit.org/show_bug.cgi?id=11231
			text += "~0";

			// Re-usable pattern to match any entirel ul or ol list:
			//var whole_list = /^(([ ]+([*\*]|[*\#]|\d\.)[ \t]+)[^\r]+?(~0|\n{2,}(?=\S)(?![ \t]*(?:[*\*]|[*\#]|\d\.)[ \t]+)))/gm;
			var whole_list = /^(([ ]+([*#]|\d\.)[ \t]+)[^\r]+?(~0|\n{2,}(?=\S)(?![ \t]*(?:[*#]|\d\.)[ \t]+)))/gm;

			if (g_list_level) {
				//console.log('list level');
				text = text.replace(whole_list,function(wholeMatch,m1,m2) {
					var list = m1;
					var list_type = (m2.search(/[*+\*]/g)>-1) ? "ul" : "ol";

					// Turn double returns into triple returns, so that we can make a
					// paragraph for the last item in a list, if necessary:
					list = list.replace(/\n{2,}/g,"\n\n\n");;
					var result = _ProcessListItems(list);

					// Trim any trailing whitespace, to put the closing `</$list_type>`
					// up on the preceding line, to get it past the current stupid
					// HTML block parser. This is a hack to work around the terrible
					// hack that is the HTML block parser.
					result = result.replace(/\s+$/,"");
					result = "<"+list_type+">" + result + "</"+list_type+">\n";
					return result;
				});
			} else {
				//whole_list = /(\n|^\n?)(([ ]+([*\*]|[*\#]|\d\.)[ \t]+)[^\r]+?(~0|\n{2,}(?=\S)(?![ \t]*(?:[*\*]|[*\#]|\d\.)[ \t]+)))/g;
				//whole_list = /(\n|^\n?)(([ ]+([*#]|\d\.)[ \t]+)[^\r]+?(~0|\n{2,}(?=\S)(?![ \t]*(?:[*#]|\d\.)[ \t]+)))/g;
				whole_list = /(^|\n)(([ ]+([*#]|\d\.)[ \t]+)[^\r]+?(~0|\n+)(?![ \t]+([*#]|\d\.)))/g;
				text = text.replace(whole_list,function(wholeMatch,m1,m2,m3) {
					var runup = m1;
					var list = m2;

					//var list_type = (m3.search(/[*\*]/g)>-1) ? "ul" : "ol";
					var list_type = (m3.search(/[*]/g)>-1) ? "ul" : "ol";
					// Turn double returns into triple returns, so that we can make a
					// paragraph for the last item in a list, if necessary:
					var list = list.replace(/\n{2,}/g,"\n\n\n");
					var result = _ProcessListItems(list);
					result = runup + "<"+list_type+">\n" + result + "</"+list_type+">\n\n";	
					return result;
				});
			}

			//  strip sentinel
			text = text.replace(/~0/,"");

			return text;
		}

		function _ProcessListItems(list_str) {
			//  Process the contents of a single ordered or unordered list, splitting it
			//  into individual list items.

			// The $g_list_level global keeps track of when we're inside a list.
			// Each time we enter a list, we increment it; when we leave a list,
			// we decrement. If it's zero, we're not in a list anymore.
			//
			// We do this because when we're not inside a list, we want to treat
			// something like this:
			//
			//    I recommend upgrading to version
			//    8. Oops, now this line is treated
			//    as a sub-list.
			//
			// As a single paragraph, despite the fact that the second line starts
			// with a digit-period-space sequence.
			//
			// Whereas when we're inside a list (or sub-list), that line will be
			// treated as the start of a sub-list. What a kludge, huh? This is
			// an aspect of Markdown's syntax that's hard to parse perfectly
			// without resorting to mind-reading. Perhaps the solution is to
			// change the syntax rules such that sub-lists must start with a
			// starting cardinal number; e.g. "1." or "a.".

			g_list_level++;

			// trim trailing blank lines:
			list_str = list_str.replace(/\n{2,}$/,"\n");

			//  add sentinel to emulate \z
			list_str += "~0";

			//list_str = list_str.replace(/(\n)?(^[ \t]*)([*\*]|[*\#]|\d\.)[ \t]+([^\r]+?(\n{1,2}))(?=\n*(~0|\2([*\*]|[*\#]|\d\.)[ \t]+))/gm,
			list_str = list_str.replace(/(\n)?(^[ \t]*)([*#]|\d\.)[ \t]+([^\r]+?(\n{1,2}))(?=\n*(~0|\2([*#]|\d\.)[ \t]+))/gm,
				function(wholeMatch,m1,m2,m3,m4){
					var item = m4;
					var leading_line = m1;
					var leading_space = m2;

					if (leading_line || (item.search(/\n{2,}/)>-1)) {
						item = _RunBlockGamut(_Outdent(item));
					}
					else {
						// Recursion for sub-lists:
						//console.log(_Outdent(item));
						item = _DoLists(item);
						item = item.replace(/\n$/,""); // chomp(item)
						item = _RunSpanGamut(item);
					}

					return  "<li>" + item + "</li>\n";
				}
			);

			//  strip sentinel
			list_str = list_str.replace(/~0/g,"");

			g_list_level--;
			return list_str;
		}
		
		function _DoDefinitionLists(text) {
			// Form HTML definition lists.

			// add sentinel to hack around khtml/safari bug:
			// http://bugs.webkit.org/show_bug.cgi?id=11231
			text += "~0";

			// Re-usable pattern to match any entire dl list:
			var whole_list = /^(([ ](.+?)\:\:[ \n]+)[^\r]+?(~0|\n{2,}(?=\S)(?![ \t]*(?:[ ]{3,})[ \t]+)))/gm;

			if (g_list_level) {
				text = text.replace(whole_list,function(wholeMatch,m1,m2) {
					var list = m1;
					var list_type = "dl";

					// Turn double returns into triple returns, so that we can make a
					// paragraph for the last item in a list, if necessary:
					list = list.replace(/\n{2,}/g,"\n\n\n");
					var result = _ProcessDefListItems(list);

					// Trim any trailing whitespace, to put the closing `</$list_type>`
					// up on the preceding line, to get it past the current stupid
					// HTML block parser. This is a hack to work around the terrible
					// hack that is the HTML block parser.
					result = result.replace(/\s+$/,"");
					result = "<" + list_type + ">" + result + "</" + list_type + ">\n";
					return result;
				});
			} else {
				whole_list = /(\n)?((.+?)::[\s\S]+?(?=\n{2}|$))/g; //(\n)?([ ]{1}(.+?)[:]{2}[^\r]+?(.+\n)*)/g;
				text = text.replace(whole_list,function(wholeMatch,m1,m2,m3) {
					var runup = m1,
						list = m2,
						list_type = "dl";
					// Turn double returns into triple returns, so that we can make a
					// paragraph for the last item in a list, if necessary:
					var list = list.replace(/\n{2,}/g,"\n\n\n");
					var result = _ProcessDefListItems(list);
					result = runup + "<" + list_type + ">\n" + result + "</" + list_type + ">\n";	
					return result;
				});
			}

			//  strip sentinel
			text = text.replace(/~0/,"");

			return text;
		}

		function _ProcessDefListItems(list_str) {
			// Process the contents of a single definition, splitting it
			// into individual term and definition pairs
			g_list_level++;

			// trim trailing blank lines:
			list_str = list_str.replace(/\n{2,}$/,"\n");

			// add sentinel to emulate \z
			list_str += "~0";

			list_str = list_str.replace(/((\s)*(.+?)::[^\r]+?(.+))/g,
			//list_str = list_str.replace(/(\n)?(\s{1})(.+?):{2}[^\r]+?(~0|(\s{3}(.+))+)/gm,
				function(wholeMatch,m1,m2,m3,m4){
					var leading_line = m1,
						leading_space = m2,
						term = m3,
						definition = m4;

					term = term.replace(/\n$/,"");
					term = _RunSpanGamut(term);
						
					definition = definition.replace(/\n$/,"");
					definition = _RunSpanGamut(definition);

					return  "<dt>" + term + "</dt><dd>" + definition + "</dd>\n";
				}
			);

			// strip sentinel
			list_str = list_str.replace(/~0/g,"");

			g_list_level--;
			return list_str;
		}
		
		function _DoTables(text) {
			// Form HTML tables.

			// add sentinel to hack around khtml/safari bug:
			// http://bugs.webkit.org/show_bug.cgi?id=11231
			text += "~0";

			// Re-usable pattern to match any entire table:
			var whole_list = /^(([ \t]*([\|]{2,})[ \t]*)[^\r]+?(~0|\n{2,}(?=\S)(?![ \t]*(?:[\|]{2,})[ \t]*)))/gm;

			if (g_table_level) {
				text = text.replace(whole_list,function(wholeMatch,m1,m2) {
					var list = m1;
					var list_type = "table";

					// Turn double returns into triple returns, so that we can make a
					// paragraph for the last item in a list, if necessary:
					list = list.replace(/\n{2,}/g,"\n\n\n");;
					var result = _ProcessTableItems(list);

					// Trim any trailing whitespace, to put the closing `</$list_type>`
					// up on the preceding line, to get it past the current stupid
					// HTML block parser. This is a hack to work around the terrible
					// hack that is the HTML block parser.
					result = result.replace(/\s+$/,"");
					result = "<"+list_type+">" + result + "</"+list_type+">\n";
					return result;
				});
			} else {
				whole_list = /(\n\n|^\n?)(([ \t]*([\|]{2,})[ \t]*)[^\r]+?(~0|\n{2,}(?![ \t]*(?:[\|]{2,}))))/g;
				text = text.replace(whole_list,function(wholeMatch,m1,m2,m3) {
					var runup = m1,
						list = m2;

					// Turn double returns into triple returns, so that we can make a
					// paragraph for the last item in a list, if necessary:
					var list = list.replace(/\n{2,}/g,"\n\n\n");
					var result = _ProcessTableItems(list);
					result = runup + "<table>\n" + result + "</table>\n";	
					return result;
				});
			}

			//  strip sentinel
			text = text.replace(/~0/,"");

			return text;
		}

		function _ProcessTableItems(list_str) {
			//  Process the contents of a single table, splitting it
			//  into individual rows.

			g_table_level++;

			// trim trailing blank lines:
			list_str = list_str.replace(/\n{2,}$/,"\n");

			// add sentinel to emulate \z
			list_str += "~0";

			list_str = list_str.replace(/(\n)?^(\|{2})(.+?)(\|{2})$/gm,
				function(wholeMatch,m1,m2,m3,m4){
					var leading_space = m1,
						row_start = m2,
						row = m3,
						row_end = m4,
						item = '',
						i = 0;

					cells = row.split('||');
					for (i; i < cells.length; i++) {
						if (cells[i][0] == '=') {
							item += '<th>' + _RunSpanGamut(cells[i].replace(/=(.*)=/g,"$1")) + '</th>';
						} else {
							item += '<td>' + _RunSpanGamut(cells[i]) + '</td>';
						}
					}
					
					return  "<tr>" + item + "</tr>\n";
				}
			);

			// strip sentinel
			list_str = list_str.replace(/~0/g,"");

			g_table_level--;
			return list_str;
		}

		function _stripCodeBlocks(text) {
			var j = 0;
			// sentinel workarounds for lack of \A and \Z, safari\khtml bug
			text += "~0";
			
			lines = text.split('\n');
			
			for (var i = 0, len = lines.length; i < len; i++) 
			{
				lines[i] = lines[i].replace(/({{{)/g,
					function(wholeMatch) {
						j++;
						if (j == 1) {
							wholeMatch += j;
						}
						return wholeMatch;
					}
				);

				lines[i] = lines[i].replace(/(}}})/g,
					function(wholeMatch) {
						if (j == 1) {
							wholeMatch = j + wholeMatch;
						}
						j--;
						return wholeMatch;
					}
				);
			}
			
			text = lines.join('\n');
			
			// strip sentinel
			text = text.replace(/~0/,"");
			
			return text;
		}

		function _DoHtmlCodeBlocks(text) {
			//  Process Wiki `<pre>` blocks.

			// sentinel workarounds for lack of \A and \Z, safari\khtml bug
			text += "~0";
			
			text = text.replace(/\{\{\{1[\s]*#!html[\s]*([\s\S]*?)1\}\}\}(\n*[ ]{0,3})/g,
				function(wholeMatch, m1, m2) {
					var codeblock = m1;
					var nextChar = m2;

					// encode <pre> blocks in the HTML
					codeblock = codeblock.replace(/<pre>([\s\S]*?)<\/pre>(\n*[ ]{0,3})/g,
						function(wholeMatch, m1, m2) {
							var block = m1;
							var nextChar = m2;

							block = _EncodeCode(block);
							
							return '<pre>' + block + '</pre>' + nextChar;
						}
					);
					codeblock = codeblock.replace(/^\n+/g,""); // trim leading newlines
					codeblock = codeblock.replace(/\n+$/g,""); // trim trailing whitespace

					codeblock = '<div class="html">' + codeblock + "\n</div>";

					return hashBlock(codeblock) + nextChar;
				}
			);

			// strip sentinel
			text = text.replace(/~0/,"");

			return text;
		}

		function _DoCodeBlocks(text) {
			//  Process Wiki `<pre>` blocks.

			// sentinel workarounds for lack of \A and \Z, safari\khtml bug
			text += "~0";

			text = text.replace(/\{\{\{1([\s\S]*?)1\}\}\}(\n*[ ]{0,3})/g,
				function(wholeMatch, m1, m2) {
					var codeblock = m1;
					var nextChar = m2;

					codeblock = _EncodeCode(codeblock);
					codeblock = codeblock.replace(/^\n+/g,""); // trim leading newlines
					codeblock = codeblock.replace(/\n+$/g,""); // trim trailing whitespace

					codeblock = "<pre>" + codeblock + "\n</pre>";

					return hashBlock(codeblock) + nextChar;
				}
			);

			// strip sentinel
			text = text.replace(/~0/,"");

			return text;
		}

		function hashBlock(text) {
			text = text.replace(/(^\n+|\n+$)/g,"");
			return "\n\n~K" + (g_html_blocks.push(text)-1) + "K\n\n";
		}

		function _DoCodeSpans(text) {
			//
			//   *  Backtick quotes are used for <code></code> spans.
			// 
			//   *  You can use multiple backticks as the delimiters if you want to
			//      include literal backticks in the code span. So, this input:
			//
			//     Just type ``foo `bar` baz`` at the prompt.
			//
			//     Will translate to:
			//
			//     <p>Just type <code>foo `bar` baz</code> at the prompt.</p>
			//
			//  There's no arbitrary limit to the number of backticks you
			//  can use as delimters. If you need three consecutive backticks
			//  in your code, use four for delimiters, etc.
			//
			//  *  You can use spaces to get literal backticks at the edges:
			//
			//      ... type `` `bar` `` ...
			//
			//     Turns to:
			//
			//      ... type <code>`bar`</code> ...
			//

			text = text.replace(/(^|[^\\])(`+)([^\r]*?[^`])\2(?!`)/gm,
				function(wholeMatch,m1,m2,m3,m4) {
					var c = m3;
					c = c.replace(/^([ \t]*)/g,"");	// leading whitespace
					c = c.replace(/[ \t]*$/g,"");	// trailing whitespace
					c = _EncodeCode(c);
					return m1+"<code>"+c+"</code>";
				});

			return text;
		}

		function _EncodeCode(text) {
			// Encode/escape certain characters inside Markdown code runs.
			// The point is that in code, these characters are literals,
			// and lose their special Markdown meanings.
			
			// Encode all ampersands; HTML entities are not
			// entities within a Markdown code span.
			text = text.replace(/&/g,"&amp;");

			// Do the angle bracket song and dance:
			text = text.replace(/</g,"&lt;");
			text = text.replace(/>/g,"&gt;");

			// Now, escape characters that are magic in Markdown:
			text = escapeCharacters(text,"\*_{}[]\\",false);

			return text;
		}

		function _DoItalicsAndBold(text) {
			// '''''bold italic''''' = <strong><em>bold italic</em></strong>
			text = text.replace(/(\'\'\'\'\')(?=\S)([^\r]*?\S)\1/g,
				"<strong><em>$2</em></strong>");
				
			// '''bold''' = <strong>bold</strong>
			text = text.replace(/(\'\'\')(?=\S)([^\r]*?\S)\1/g,
				"<strong>$2</strong>");

			// ''italic'' = <em>italic</em>
			text = text.replace(/(\'\')(?=\S)([^\r]*?\S)\1/g,
				"<em>$2</em>");

			// +insert+ = <ins>insert</ins>
			text = text.replace(/(\+)(?=\S)([^\r]*?\S)\1/g,
				"<ins>$2</ins>");

			// ~~strike~~ = <del>strike</del>
			text = text.replace(/(\~T\~T)(?=\S)([^\r]*?\S)\1/g,
				"<del>$2</del>");

			// ??cite?? = <cite>cite</cite>
			text = text.replace(/(\?\?)(?=\S)([^\r]*?\S)\1/g,
				"<cite>$2</cite>");

			// __underline__ = <u>underline</u>
			text = text.replace(/(\_\_)(?=\S)([^\r]*?\S)\1/g,
				"<u>$2</u>");

			// ^superscript^ = <sup>superscript</sup>
			text = text.replace(/(\^)(?=\S)([^\r]*?\S)\1/g,
				"<sup>$2</sup>");

			// ,,subscript,, = <sub>subscript</sub>
			text = text.replace(/(\,\,)(?=\S)([^\r]*?\S)\1/g,
				"<sub>$2</sub>");

			return text;
		}

		function _DoBlockQuotes(text) {
			text = text.replace(/((^[ ]{2}.+\n(.+\n)*\n*)+)/gm,
				function(wholeMatch,m1) {
					var bq = m1;

					// hack around Konqueror 3.5.4 bug:
					// "----------bug".replace(/^-/g,"") == "bug"

					bq = bq.replace(/^[ ]{2}[ \t]?/gm,"~0"); // trim one level of quoting

					// clean up hack
					bq = bq.replace(/~0/g,"");

					bq = bq.replace(/^[ \t]+$/gm,""); // trim whitespace-only lines
					bq = _RunBlockGamut(bq); // recurse
					bq = _RunSpanGamut(bq);
					bq = bq.replace(/^\s+|\s+$/g, '');

					bq = bq.replace(/(^|\n)/g,"$1");
					// These leading spaces screw with <pre> content, so we need to fix that:
					bq = bq.replace(
							/(\s*<pre>[^\r]+?<\/pre>)/gm,
						function(wholeMatch,m1) {
							var pre = m1;
							// hack around Konqueror 3.5.4 bug:
							pre = pre.replace(/^  /mg,"~0");
							pre = pre.replace(/~0/g,"");
							return pre;
						});

					return hashBlock("<blockquote>\n" + bq + "\n</blockquote>");
				});
			return text;
		}

		function _FormParagraphs(text) {
			// Strip leading and trailing lines:
			text = text.replace(/^\n+/g,"");
			text = text.replace(/\n+$/g,"");

			var grafs = text.split(/\n{2,}/g);
			var grafsOut = new Array();

			// Wrap <p> tags.
			var end = grafs.length;
			for (var i=0; i<end; i++) {
				var str = grafs[i];

				// if this is an HTML marker, copy it
				if (str.search(/~K(\d+)K/g) >= 0) {
					grafsOut.push(str);
				}
				else if (str.search(/\S/) >= 0) {
					str = _RunSpanGamut(str);
					str = str.replace(/^([ \t]*)/g,"<p>");
					str += "</p>"
					grafsOut.push(str);
				}
			}

			// Unhashify HTML blocks
			end = grafsOut.length;
			for (var i=0; i<end; i++) {
				// if this is a marker for an html block...
				while (grafsOut[i].search(/~K(\d+)K/) >= 0) {
					var blockText = g_html_blocks[RegExp.$1];
					blockText = blockText.replace(/\$/g,"$$$$"); // Escape any dollar signs
					grafsOut[i] = grafsOut[i].replace(/~K\d+K/,blockText);
				}
			}

			return grafsOut.join("\n\n");
		}

		function _EncodeAmpsAndAngles(text) {
			// Smart processing for ampersands and angle brackets that need to be encoded.

			// Ampersand-encoding based entirely on Nat Irons's Amputator MT plugin:
			//   http://bumppo.net/projects/amputator/
			text = text.replace(/&(?!#?[xX]?(?:[0-9a-fA-F]+|\w+);)/g,"&amp;");

			// Encode naked <'s
			text = text.replace(/<(?![a-z\/?\$!])/gi,"&lt;");

			return text;
		}

		function _EncodeBackslashEscapes(text) {
			// The string, with after processing the following backslash
			// escape sequences.

			// The polite way to do this is with the new
			// escapeCharacters() function:
			//
			// 	text = escapeCharacters(text,"\\",true);
			// 	text = escapeCharacters(text,"`*_{}[]()>#+-.!",true);
			//
			// ...but we're sidestepping its use of the (slow) RegExp constructor
			// as an optimization for Firefox.  This function gets called a LOT.

			text = text.replace(/\\(\\)/g,escapeCharacters_callback);
			text = text.replace(/\\([`*_{}\[\]()>#+-.!])/g,escapeCharacters_callback);
			return text;
		}

		function _DoAutoLinks(text) {
			// URLs: http://something.com
			text = text.replace(/\s(ht|f)tps?:\/\/([^ \,\;\:\!\)\(\"\'\<\>\f\n\r\t\v])+/g, 
				function ($0,$1,$2) { 
					text = $0.substring(1,$0.length); 
					while (text.length>0 && text.charAt(text.length-1)=='.') {
						text = text.substring(0,text.length-1);
					}
					return " " + text.link(text); 
				}
			);

			// Email addresses: <address@domain.foo>
			text = text.replace(/(?:mailto:)?([-.\w]+\@[-a-z0-9]+(\.[-a-z0-9]+)*\.[a-z]+)/gi,
				function(wholeMatch,m1) {
					return _EncodeEmailAddress( _UnescapeSpecialChars(m1) );
				}
			);

			return text;
		}

		function _EncodeEmailAddress(addr) {
			//
			//  Input: an email address, e.g. "foo@example.com"
			//
			//  Output: the email address as a mailto link, with each character
			//	of the address encoded as either a decimal or hex entity, in
			//	the hopes of foiling most address harvesting spam bots. E.g.:
			//
			//	<a href="&#x6D;&#97;&#105;&#108;&#x74;&#111;:&#102;&#111;&#111;&#64;&#101;
			//	   x&#x61;&#109;&#x70;&#108;&#x65;&#x2E;&#99;&#111;&#109;">&#102;&#111;&#111;
			//	   &#64;&#101;x&#x61;&#109;&#x70;&#108;&#x65;&#x2E;&#99;&#111;&#109;</a>
			//
			//  Based on a filter by Matthew Wickline, posted to the BBEdit-Talk
			//  mailing list: <http://tinyurl.com/yu7ue>
			//

			// why can't javascript speak hex?
			function char2hex(ch) {
				var hexDigits = '0123456789ABCDEF';
				var dec = ch.charCodeAt(0);
				return(hexDigits.charAt(dec>>4) + hexDigits.charAt(dec&15));
			}

			var encode = [
				function(ch){return "&#"+ch.charCodeAt(0)+";";},
				function(ch){return "&#x"+char2hex(ch)+";";},
				function(ch){return ch;}
			];

			addr = "mailto:" + addr;

			addr = addr.replace(/./g, function(ch) {
				if (ch == "@") {
				   	// this *must* be encoded. I insist.
					ch = encode[Math.floor(Math.random()*2)](ch);
				} else if (ch !=":") {
					// leave ':' alone (to spot mailto: later)
					var r = Math.random();
					// roughly 10% raw, 45% hex, 45% dec
					ch =  (
							r > .9  ?	encode[2](ch)   :
							r > .45 ?	encode[1](ch)   :
										encode[0](ch)
						);
				}
				return ch;
			});

			addr = "<a href=\"" + addr + "\">" + addr + "</a>";
			addr = addr.replace(/">.+:/g,"\">"); // strip the mailto: from the visible part

			return addr;
		}

		function _UnescapeSpecialChars(text) {
			// Swap back in all the special characters we've hidden.
			
			text = text.replace(/~E(\d+)E/g,
				function(wholeMatch,m1) {
					var charCodeToReplace = parseInt(m1);
					return String.fromCharCode(charCodeToReplace);
				}
			);
			return text;
		}
		
		function _Outdent(text) {
			// Remove one level of line-leading tabs or spaces
			
			// hack around Konqueror 3.5.4 bug:
			// "----------bug".replace(/^-/g,"") == "bug"
			text = text.replace(/^(\t|[ ]{1,4})/gm,"~0");

			// clean up hack
			text = text.replace(/~0/g,"")

			return text;
		}

		function _Detab(text) {
			// expand first n-1 tabs
			text = text.replace(/\t(?=\t)/g,"    ");

			// replace the nth with two sentinels
			text = text.replace(/\t/g,"~A~B");

			// use the sentinel to anchor our regex so it doesn't explode
			text = text.replace(/~B(.+?)~A/g,
				function(wholeMatch,m1,m2) {
					var leadingText = m1;
					var numSpaces = 4 - leadingText.length % 4;

					// there *must* be a better way to do this:
					for (var i=0; i<numSpaces; i++) leadingText+=" ";

					return leadingText;
				}
			);

			// clean up sentinels
			text = text.replace(/~A/g,"    ");
			text = text.replace(/~B/g,"");

			return text;
		}

		function escapeCharacters(text, charsToEscape, afterBackslash) {
			// First we have to escape the escape characters so that
			// we can build a character class out of them
			var regexString = "([" + charsToEscape.replace(/([\[\]\\])/g,"\\$1") + "])";

			if (afterBackslash) {
				regexString = "\\\\" + regexString;
			}

			var regex = new RegExp(regexString,"g");
			text = text.replace(regex,escapeCharacters_callback);

			return text;
		}

		function escapeCharacters_callback(wholeMatch,m1) {
			var charCodeToEscape = m1.charCodeAt(0);
			return "~E"+charCodeToEscape+"E";
		}
		
		// Clear the global hashes. If we don't clear these, you get conflicts
		// from other articles when generating a page which contains more than
		// one article (e.g. an index page that shows the N most recent
		// articles):
		g_urls = new Array();
		g_titles = new Array();
		g_html_blocks = new Array();
		g_macros = new Array();
		
		// The order in which other subs are called here is essential. 
		// Link and image substitutions need to happen before
		// _EscapeSpecialCharsWithinTagAttributes(), so that any *'s or _'s in the <a>
		// and <img> tags get encoded.

		// Replace ~ with ~T
		// This lets us use tilde as an escape char to avoid md5 hashes
		// The choice of character is arbitray; anything that isn't
	    // magic in Wiki will work.
		text = text.replace(/~/g, "~T");

		// Replace $ with ~D
		// RegExp interprets $ as a special character
		// when it's in a replacement string
		text = text.replace(/\$/g, "~D");

		// Standardize line endings
		text = text.replace(/\r\n/g, "\n"); // DOS to Unix
		text = text.replace(/\r/g, "\n"); // Mac to Unix

		// Make sure text begins and ends with a couple of newlines:
		text = "\n\n" + text + "\n\n";

		// Convert all tabs to spaces.
		text = _Detab(text);

		// Strip any lines consisting only of spaces and tabs.
		// This makes subsequent regexen easier to write, because we can
		// match consecutive blank lines with /\n+/ instead of something
		// contorted like /[ \t]*\n+/ .
		text = text.replace(/^[ \t]+$/mg, "");

		text = _DoMath(text);

		text = _stripCodeBlocks(text);

		text = _DoHtmlCodeBlocks(text);

		text = _DoCodeBlocks(text);

		text = text.replace(/\[\[br\]\]/gi,"<br />");

		// Turn block-level HTML blocks into hash entries
		text = _HashHTMLBlocks(text);

		// Strip macros. This must be done before running the block gamut
		// Otherwise, the link processor would transform macro syntax.
		// Ex: [[Macro()]] -> [<a href="Macro()">Macro()</a>]
		text = _DoDivs(text);

		text = _DoColumns(text);

		text = _StripMacros(text);

		text = _RunBlockGamut(text);

		// Restore macros
		text = _UnStripMacros(text);

		text = _DoSpans(text);
		// Convert image and file macros
		//text = _DoImages(text);
		//text = _DoFiles(text);

		text = _UnescapeSpecialChars(text);

		// Restore dollar signs
		text = text.replace(/~D/g, "$$");

		// Restore tildes
		text = text.replace(/~T/g, "~");
		
		text = text.replace(/\n{3,}/g, '\n\n');

		return text;
	}
}

if (!jq) {
	var jq = $;
}

WYKIWYG.editor = function() {
	var c=[], 
		offset=-30;
	
	c['cut']=[1,'Cut','a','cut',1];
	c['copy']=[2,'Copy','a','copy',1];
	c['paste']=[3,'Paste','a','paste',1];
	c['bold']=[4,'Bold','a','bold'];
	c['italic']=[5,'Italic','a','italic'];
	c['underline']=[6,'Underline','a','underline'];
	c['strikethrough']=[7,'Strikethrough','a','strikethrough'];
	c['subscript']=[8,'Subscript','a','subscript'];
	c['superscript']=[9,'Superscript','a','superscript'];
	c['orderedlist']=[10,'Insert Ordered List','a','insertorderedlist'];
	c['unorderedlist']=[11,'Insert Unordered List','a','insertunorderedlist'];
	c['outdent']=[12,'Outdent','a','outdent'];
	c['indent']=[13,'Indent','a','indent'];
	c['leftalign']=[14,'Left Align','a','justifyleft'];
	c['centeralign']=[15,'Center Align','a','justifycenter'];
	c['rightalign']=[16,'Right Align','a','justifyright'];
	c['blockjustify']=[17,'Block Justify','a','justifyfull'];
	c['undo']=[18,'Undo','a','undo'];
	c['redo']=[19,'Redo','a','redo'];
	c['image']=[20,'Insert Image','i','insertimage','Enter Image URL:','http://'];
	c['hr']=[21,'Insert Horizontal Rule','a','inserthorizontalrule'];
	c['link']=[22,'Insert Hyperlink','i','createlink','Enter URL:','http://'];
	c['unlink']=[23,'Remove Hyperlink','a','unlink'];
	c['unformat']=[24,'Remove Formatting','a','removeformat'];
	c['print']=[25,'Print','a','print'];
	
	function edit(n, obj) {
		var $ = jq;
		
		this.n = n; 
		window[n] = this; 
		this.t = T$(obj.id);
		this.obj = obj; 
		this.xhtml = obj.xhtml;
		
		var p = document.createElement('div'), 
			w = document.createElement('div'), 
			h = document.createElement('div'),
			l = obj.controls.length, 
			i = 0;
			this.i = document.createElement('iframe'); 
		
		this.i.frameBorder = 0;
		this.i.width = '100%'; //(obj.width || (this.t.offsetWidth || '500') - 8); 
		//this.i.height = (obj.height || (this.t.offsetHeight || '250') - 8);
		if (obj.footer) {
			this.i.height = (obj.height || ((this.t.rows * parseInt($(this.t).css("font-size"))) || '250') - 8);
		} else {
			this.i.height = (obj.height || ((this.t.rows * parseInt($(this.t).css("font-size"))) || '250') + 50); // Add extra to account for footer height
		}
		this.ie = T$$$();
		h.className = obj.rowclass || 'wykiwyg-header';
		p.className = obj.cssclass || 'wykiwyg';
		p.style.width = '99%'; //this.i.width + 'px'; 
		p.appendChild(h);
		
		for (i; i < l; i++) {
			var id = obj.controls[i];
			switch (id)
			{
				case 'n':
					h = document.createElement('div'); 
					h.className = obj.rowclass || 'wykiwyg-header';
					p.appendChild(h);
				break;
				
				case '|':
					var d = document.createElement('div'); 
					d.className = obj.dividerclass || 'wykiwyg-divider';
					h.appendChild(d);
				break;
				
				case 'font':
					var sel = document.createElement('select'), 
						fonts = obj.fonts || ['Verdana','Arial','Georgia','Trebuchet MS'], 
						fl = fonts.length, 
						x = 0;
					sel.className = 'wykiwyg-font'; 
					sel.onchange = new Function(this.n+'.ddaction(this,"fontname")');
					sel.options[0] = new Option('Font','');
					for (x;x<fl;x++) {
						var font = fonts[x];
						sel.options[x+1] = new Option(font,font)
					}
					h.appendChild(sel);
				break;
				
				case 'size':
					var sel = document.createElement('select'), 
						sizes = obj.sizes || [1,2,3,4,5,6,7], 
						sl = sizes.length, 
						x = 0;
					sel.className = 'wykiwyg-size'; 
					sel.onchange = new Function(this.n+'.ddaction(this,"fontsize")');
					for (x; x < sl; x++) {
						var size = sizes[x];
						sel.options[x] = new Option(size,size);
					}
					h.appendChild(sel);
				break;
				
				case 'style':
					var sel = document.createElement('select'),
						styles = obj.styles || [['Style',''],['Paragraph','<p>'],['Header 1','<h1>'],['Header 2','<h2>'],['Header 3','<h3>'],['Header 4','<h4>'],['Header 5','<h5>'],['Header 6','<h6>']],
						sl = styles.length, 
						x = 0;
					sel.className = 'wykiwyg-style';
					sel.onchange = new Function(this.n+'.ddaction(this,"formatblock")');
					for (x; x < sl; x++) {
						var style = styles[x];
						sel.options[x] = new Option(style[0],style[1]);
					}
					h.appendChild(sel);
				break;
				
				default:
					if (c[id]) {
						var div = document.createElement('div'), 
							x = c[id], 
							func = x[2], 
							ex, 
							pos = x[0]*offset;
						div.className = obj.controlclass || 'wykiwyg-control';
						div.style.backgroundPosition = '0px '+pos+'px';
						div.title = x[1];
						ex = (func == 'a') ? '.action("'+x[3]+'",0,'+(x[4]||0)+')' : '.insert("'+x[4]+'","'+x[5]+'","'+x[3]+'")';
						div.onclick = new Function(this.n+(id == 'print' ? '.print()' : ex));
						div.onmouseover = new Function(this.n+'.hover(this,'+pos+',1)');
						div.onmouseout = new Function(this.n+'.hover(this,'+pos+',0)');
						h.appendChild(div);
						if (this.ie) {
							div.unselectable = 'on';
						}
					}
				break;
			}
		}
		this.t.parentNode.insertBefore(p, this.t); 
		this.t.style.width = '100%'; //this.i.width+'px';
		w.appendChild(this.t); 
		w.appendChild(this.i); 
		p.appendChild(w); 
		this.t.style.display = 'none';
		if (obj.footer) {
			var f = document.createElement('div'); 
			f.className = obj.footerclass || 'wykiwyg-footer';
			if (obj.toggle) {
				var to = obj.toggle, 
					ts = document.createElement('div');
				ts.className = to.cssclass || 'wykiwyg-toggle';
				ts.innerHTML = obj.toggletext || 'switch to source';
				ts.onclick = new Function(this.n+'.toggle(0,this);return false');
				f.appendChild(ts);
			}
			if (obj.resize) {
				var ro = obj.resize, 
					rs = document.createElement('div'); 
				rs.className = ro.cssclass || 'wykiwyg-resize';
				rs.onmousedown = new Function('event',this.n+'.resize(event);return false');
				rs.onselectstart = function(){
					return false;
				}
				f.appendChild(rs);
			}
			p.appendChild(f);
		}
		this.e = this.i.contentWindow.document;
		this.e.open();
		// Create the iframe document
		var m = '<!DOCTYPE html><html><head><meta charset="UTF-8">', 
			bodyid = obj.bodyid ? ' id="'+obj.bodyid+'"' : ' id="wykiwyg-editor"';
		if (obj.cssfile) {
			m += '<link rel="stylesheet" href="'+obj.cssfile+'" />';
		}
		if (obj.css) {
			m += '<style type="text/css">'+obj.css+'</style>';
		}
		m += '</head><body'+bodyid+'>';
		if (obj.content) {
			m += obj.content;
		} else {
			var converter = new WYKIWYG.converter();
			m += converter.makeHtml(this.t.value);
		}
		m += '</body></html>';
		this.e.write(m);
		this.e.close();
		this.e.designMode = 'on';
		this.d = 1;
		if (this.xhtml) {
			try {
				this.e.execCommand("styleWithCSS", 0, 0);
			} catch(e) {
				try{
					this.e.execCommand("useCSS", 0, 1);
				} catch(e) {}
			}
		}
		var wwe = this;
		$(this.t).closest('form').on('submit', function(){
			var converter = new WYKIWYG.converter();
			if (wwe.d) {
				wwe.t.value = wwe.makeWiki();
				wwe.e.body.innerHTML = '';
			}
		});
	};
	edit.prototype.print = function() {
		this.i.contentWindow.print();
	},
	edit.prototype.hover = function(div, pos, dir) {
		div.style.backgroundPosition = (dir ? '34px ' : '0px ')+(pos)+'px';
	},
	edit.prototype.ddaction = function(dd,a){
		var i = dd.selectedIndex, 
			v = dd.options[i].value;
		this.action(a,v);
	},
	edit.prototype.action = function(cmd, val, ie) {
		if (ie && !this.ie) {
			alert('Your browser does not support this function.');
		} else {
			this.e.execCommand(cmd, 0, val || null);
		}
	},
	edit.prototype.insert = function(pro, msg, cmd) {
		var val = prompt(pro,msg);
		if (val != null && val != '') {
			this.e.execCommand(cmd, 0, val);
		}
	},
	edit.prototype.setfont = function() {
		execCommand('formatblock', 0, hType);
	},
	edit.prototype.resize = function(e) {
		if (this.mv) {
			this.freeze();
		}
		this.i.bcs = WYKIWYG.cursor.top(e);
		this.mv = new Function('event',this.n+'.move(event)');
		this.sr = new Function(this.n+'.freeze()');
		if (this.ie) {
			document.attachEvent('onmousemove',this.mv); 
			document.attachEvent('onmouseup',this.sr);
		} else {
			document.addEventListener('mousemove',this.mv,1); 
			document.addEventListener('mouseup',this.sr,1);
		}
	},
	edit.prototype.move = function(e) {
		var pos = WYKIWYG.cursor.top(e);
		this.i.height = parseInt(this.i.height)+pos-this.i.bcs;
		this.i.bcs = pos;
	},
	edit.prototype.freeze = function() {
		if (this.ie) {
			document.detachEvent('onmousemove', this.mv); 
			document.detachEvent('onmouseup', this.sr);
		} else {
			document.removeEventListener('mousemove', this.mv, 1); 
			document.removeEventListener('mouseup', this.sr, 1);
		}
	},
	edit.prototype.makeWiki = function() {
		var converter = new WYKIWYG.converter(),
			v = this.e.body.innerHTML;
		if (this.xhtml) {
			v = v.replace(/<span class="apple-style-span">(.*)<\/span>/gi,'$1');
			v = v.replace(/ class="apple-style-span"/gi,'');
			v = v.replace(/<span style="">/gi,'');
			v = v.replace(/<br>/gi,'<br />');
			v = v.replace(/<br ?\/?>$/gi,'');
			v = v.replace(/^<br ?\/?>/gi,'');
			v = v.replace(/(<img [^>]+[^\/])>/gi,'$1 />');
			v = v.replace(/<b\b[^>]*>(.*?)<\/b[^>]*>/gi,'<strong>$1</strong>');
			v = v.replace(/<i\b[^>]*>(.*?)<\/i[^>]*>/gi,'<em>$1</em>');
			v = v.replace(/<span style="?text-decoration: underline;?"?>(.*?)<\/span>/gi,'<u>$1</u>');
			v = v.replace(/<(b|strong|em|i|u) style="font-weight: normal;?">(.*)<\/(b|strong|em|i|u)>/gi,'$2');
			v = v.replace(/<(b|strong|em|i|u) style="(.*)">(.*)<\/(b|strong|em|i|u)>/gi,'<span style="$2"><$4>$3</$4></span>');
			v = v.replace(/<span style="?vertical-align: super;?"?>(.*)<\/span>/gi,'<sup>$1</sup>');
			v = v.replace(/<span style="?vertical-align: sub;?"?>(.*)<\/span>/gi,'<sub>$1</sub>');
			v = v.replace(/<span style="font-weight: normal;?">(.*)<\/span>/gi,'$1');
			v = v.replace(/<span style="font-weight: bold;?">(.*)<\/span>/gi,'<strong>$1</strong>');
			v = v.replace(/<span style="font-style: italic;?">(.*)<\/span>/gi,'<em>$1</em>');
			v = v.replace(/<span style="font-weight: bold;?">(.*)<\/span>|<b\b[^>]*>(.*?)<\/b[^>]*>/gi,'<strong>$1</strong>');
			v = v.replace(/\n{2,}<br ?\/?>/gi,'\n\n');

			// Strip unwanted spans -- these can occur when pasting from other applications
			v = v.replace(/<span\b[^>]*>([\s\S]*?)<\/span>/gi,'$1');

			// Most of this is to get Firefox to behave
			v = v.replace(/<div style="margin-left: 40px;?">(.*)<\/div>/gi,'<blockquote>$1</blockquote>\n\n');
			v = v.replace(/<br ?\/?><\/(blockquote|p|div|li|dt|dd|td|h[1-6])>/gi,'</$1>');
			v = v.replace(/<\/(blockquote|p|div|li|dt|dd|td|h[1-6])><br ?\/?>/gi,'</$1>\n');
			v = v.replace(/<\/div><(blockquote|p|div|li|dt|dd|td|h[1-6])(\b[^>]*)>/gi,'</div>\n<$1$2>');
			v = v.replace(/<br ?\/?><br ?\/?>/gi,'\n\n');
			
		}
		return converter.makeWiki(v);
	},
	edit.prototype.makeHtml = function() {
		var converter = new WYKIWYG.converter(),
			v = this.t.value;
		return converter.makeHtml(v);
	},
	edit.prototype.toggle = function(post, div) {
		var converter = new WYKIWYG.converter();
		if (!this.d) {
			var v = converter.makeHtml(this.t.value);
			if (div) {
				div.innerHTML = this.obj.toggletext || 'switch to source';
			}
			this.e.body.innerHTML = v;
			this.t.style.display = 'none'; 
			this.i.style.display = 'block'; 
			this.d = 1;
		} else {
			if (div) {
				div.innerHTML = this.obj.toggletext || 'switch to wysiwyg';
			}
			this.t.value = this.makeWiki(v);
			if (!post) {
				this.t.style.height = this.i.height+'px';
				this.i.style.display = 'none';
				this.t.style.display = 'block';
				this.t.focus();
				this.d = 0;
			}
		}
	},
	edit.prototype.post = function() {
		if (this.d) {
			this.toggle(1);
		}
	};
	return { edit:edit }
}();

WYKIWYG.cursor = function() {
	return {
		top: function(e) {
			return T$$$() ? window.event.clientY+document.documentElement.scrollTop+document.body.scrollTop : e.clientY+window.scrollY;
		}
	}
}();

var wykiwygs = [];

function initWykiwyg() {
	//console.log('init wykiwyg');

	jQuery('.wiki-toolbar-content').each(function(i, textarea) {
		var id = jQuery(textarea).attr('id');

		for (var i = 0; i < wykiwygs.length; i++) 
		{
			if (wykiwygs[i].obj.id == id) {
				return;
			}
		}

		var controls = [
			'bold','italic','underline','strikethrough','|',
			'subscript','superscript','|',
			'orderedlist','unorderedlist','|',
			'outdent','indent','|',
			'unformat','|',
			'style','|',
			'hr','link','unlink'
		];
		if (jQuery(this).hasClass('minimal')) {
			controls = [
				'bold','italic','underline','strikethrough','|',
				'subscript','superscript','|',
				'unformat','|',
				'link','unlink'
			];
		}

		var footer = true;
		if (jQuery(this).hasClass('no-footer')) {
			footer = false;
		}

		var links = jQuery(jQuery(textarea).parent()).find('a.popup');
		links.each(function(i, link) {
			jQuery(link).attr('href', jQuery(link).attr('href') + '?tmpl=component');
		});

		var edtr = new WYKIWYG.editor.edit('editor' + id,{
			id: id,
			controls: controls,
			footer: footer,
			toggle: true,
			resize: true,
			xhtml: true,
			cssfile: '/plugins/hubzero/wikieditorwykiwyg/wikieditorwykiwyg.css'
		});

		wykiwygs.push(edtr);
	});
};

// Init editor
jQuery(document).ready(function($){
	initWykiwyg();
});
jQuery(document).on('ajaxLoad', initWykiwyg);
