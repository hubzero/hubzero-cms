tinyMCEPopup.requireLangPack();

var PasteDialog = {
	init : function() {
		var ed = tinyMCEPopup.editor,  el = document.getElementById('container'), title = document.getElementById('title'), ifr, doc, css, cssHTML = '';

		var cmd = tinyMCEPopup.getWindowArg('cmd', 'mcePaste');
		
		if (cmd == 'mcePaste') {
			// Set title
			document.title = ed.getLang('paste.paste_desc');
			title.innerHTML = ed.getLang('paste.paste_desc');
			
			// Create iframe
			el.innerHTML = '<iframe id="content" src="javascript:\'\';" frameBorder="0" style="border: 1px solid gray"></iframe>';
			ifr = document.getElementById('content');
			doc = ifr.contentWindow.document;
	
			// Force absolute CSS urls
			css = tinymce.explode(ed.settings.content_css) || [];
			css = css.concat(ed.baseURI.toAbsolute("themes/" + ed.settings.theme + "/skins/" + ed.settings.skin + "/content.css"));
			css = css.concat(ed.baseURI.toAbsolute("plugins/paste/css/blank.css"));
			tinymce.each(css, function(u) {
				cssHTML += '<link href="' + ed.documentBaseURI.toAbsolute('' + u) + '" rel="stylesheet" type="text/css" />';
			});
	
			// Write content into iframe
			doc.open();
			doc.write('<html><head>' + cssHTML + '</head><body class="mceContentBody" spellcheck="false"></body></html>');
			doc.close();
	
			doc.designMode = 'on';
			
			window.setTimeout(function() {
				ifr.contentWindow.focus();
			}, 10);
		} else {
			document.title = ed.getLang('paste.paste_text_desc');
			title.innerHTML = ed.getLang('paste.paste_text_desc');
			el.innerHTML = '<textarea id="content" name="content" rows="15" cols="100" dir="ltr" wrap="soft" class="mceFocus"></textarea>';		}
		this.resize();
	},

	insert : function() {
		var h, wc, c = document.getElementById('content');
		if(c.nodeName == 'TEXTAREA') {
			h = c.value;
			
			lines = h.split(/\r?\n/);
			if (lines.length > 1) {
				h = '';
				tinymce.each(lines, function(row){
					if (tinyMCEPopup.editor.getParam('force_p_newlines')) {
						h += '<p>' + row + '</p>';
					}
					else {
						h += row + '<br />';
					}
				});
			}
			
			wc = false;
		} else {
			h = c.contentWindow.document.body.innerHTML;
			wc = true;
		}

		tinyMCEPopup.editor.execCommand('mceInsertClipboardContent', false, {content : h, wordContent : wc});
		tinyMCEPopup.close();
	},

	resize : function() {
		var vp = tinyMCEPopup.dom.getViewPort(window), el;

		el = document.getElementById('content');

		if (el) {
			el.style.width  = (vp.w - 20) + 'px';
			el.style.height = (vp.h - 90) + 'px';
		}
	}
};
tinyMCEPopup.onInit.add(PasteDialog.init, PasteDialog);