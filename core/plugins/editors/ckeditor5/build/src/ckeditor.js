// Modern CKEditor 5 build for the HUBzero `ckeditor5` editor plugin.
//
// Exposes window.HubEditor.create(el, opts) — a ClassicEditor with image
// upload, media embed (YouTube/Vimeo), tables and source editing. It injects
// its own CSS (so the plugin only loads this one script), syncs the editor
// HTML back to the source <textarea> on form submit, and — when opts.uploadUrl
// is given — enables image upload through a custom adapter that POSTs the file
// (plus opts.tokenField as a form field, which HubZero's checkToken reads from
// the body) and inserts the returned URL.
//
// When opts.fileBrowser.url is given it also adds a "Browse server" toolbar
// button that opens the HubZero file browser and inserts the chosen file,
// through the HUB.Editor registry.

import editorCss from 'ckeditor5/ckeditor5.css';
import {
	ClassicEditor,
	Plugin, ButtonView,
	Essentials, Paragraph, Heading,
	Bold, Italic, Underline, Strikethrough, Code, RemoveFormat,
	Link, LinkImage,
	List, BlockQuote, HorizontalLine, Alignment, Indent, Autoformat, PasteFromOffice,
	Table, TableToolbar,
	Image, ImageToolbar, ImageCaption, ImageStyle, ImageResize, ImageInsert, ImageUpload,
	MediaEmbed, SourceEditing, GeneralHtmlSupport
} from 'ckeditor5';

// Inject the editor stylesheet once.
(function () {
	if (typeof document === 'undefined' || document.getElementById('ckeditor5-styles')) {
		return;
	}
	var s = document.createElement('style');
	s.id = 'ckeditor5-styles';
	s.textContent = editorCss;
	document.head.appendChild(s);
})();

class HubUploadAdapter {
	constructor(loader, url, tokenField) {
		this.loader = loader;
		this.url = url;
		this.tokenField = tokenField;
	}
	upload() {
		return this.loader.file.then(file => new Promise((resolve, reject) => {
			var data = new FormData();
			data.append('upload', file);
			if (this.tokenField) {
				data.append(this.tokenField, '1');
			}
			fetch(this.url, { method: 'POST', body: data, credentials: 'same-origin' })
				.then(r => r.json())
				.then(j => {
					if (j && j.url) {
						resolve({ default: j.url });
					} else {
						reject((j && j.error && j.error.message) || 'Upload failed.');
					}
				})
				.catch(() => reject('Upload failed.'));
		}));
	}
	abort() {}
}

// Toolbar icon for the file browser button — a folder.
const BROWSE_ICON = '<svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">' +
	'<path d="M2 4.5A1.5 1.5 0 0 1 3.5 3h3.4a1.5 1.5 0 0 1 1.2.6l.9 1.2h6.5A1.5 1.5 0 0 1 17 6.3v9.2a1.5 1.5 0 0 1-1.5 1.5h-12A1.5 1.5 0 0 1 2 15.5v-11Zm1.5 0v11h12V6.3H8.6a1.5 1.5 0 0 1-1.2-.6l-.9-1.2H3.5Z"/>' +
	'</svg>';

// Files the editor should insert as an image rather than as a link.
const IMAGE_EXTENSIONS = /\.(jpe?g|png|gif|svg|webp|bmp|ico)(\?|#|$)/i;

// A toolbar button that opens the HubZero file browser in a popup and inserts
// whatever the user picks. The browser is a separate window that reports back
// through HUB.Editor.insertFile(callbackId, file, done), so the plugin claims a
// callback id up front and hands it to the popup on the query string.
function makeFileBrowserPlugin(cfg) {
	return class HubzeroFileBrowser extends Plugin {
		static get pluginName() {
			return 'HubzeroFileBrowser';
		}

		init() {
			const editor = this.editor;

			this._callbackId = null;

			// Without the registry there is nothing for the file browser to
			// report back to, so leave the button out rather than offering one
			// that silently does nothing.
			if (!window.HUB || !window.HUB.Editor) {
				return;
			}

			this._callbackId = window.HUB.Editor.registerFileBrowserCallback((file, done) => {
				this._insert(file);
				if (typeof done == 'function') {
					done();
				}
			});

			editor.ui.componentFactory.add('hubzeroFileBrowser', locale => {
				const view = new ButtonView(locale);

				view.set({
					label: 'Browse server',
					icon: BROWSE_ICON,
					tooltip: true
				});

				view.on('execute', () => this._open());

				return view;
			});
		}

		destroy() {
			if (this._callbackId !== null && window.HUB && window.HUB.Editor) {
				window.HUB.Editor.unregisterFileBrowserCallback(this._callbackId);
			}

			return super.destroy();
		}

		// Open the browser, telling it which editor asked and where to report back
		_open() {
			const el = this.editor.sourceElement;
			const id = (el && el.id) ? el.id : '';
			const sep = (cfg.url.indexOf('?') === -1) ? '?' : '&';
			const url = cfg.url + sep
				+ 'editor=' + encodeURIComponent(id)
				+ '&editorFuncNum=' + encodeURIComponent(this._callbackId);

			window.open(
				url,
				'hubzerofilebrowser',
				'width=' + (cfg.width || 1200) + ',height=' + (cfg.height || 600) + ',resizable=yes,scrollbars=yes'
			);
		}

		// Images become image elements, anything else a link to the file
		_insert(file) {
			const editor = this.editor;

			if (IMAGE_EXTENSIONS.test(file) && editor.commands.get('insertImage')) {
				editor.execute('insertImage', { source: file });
				return;
			}

			const label = file.split('?')[0].split('/').pop() || file;

			editor.model.change(writer => {
				editor.model.insertContent(writer.createText(label, { linkHref: file }));
			});
		}
	};
}

function makeUploadPlugin(url, tokenField) {
	return function (editor) {
		editor.plugins.get('FileRepository').createUploadAdapter =
			loader => new HubUploadAdapter(loader, url, tokenField);
	};
}

window.HubEditor = {
	create: function (el, opts) {
		opts = opts || {};

		var plugins = [
			Essentials, Paragraph, Heading,
			Bold, Italic, Underline, Strikethrough, Code, RemoveFormat,
			Link, LinkImage,
			List, BlockQuote, HorizontalLine, Alignment, Indent, Autoformat, PasteFromOffice,
			Table, TableToolbar,
			Image, ImageToolbar, ImageCaption, ImageStyle, ImageResize, ImageInsert, ImageUpload,
			MediaEmbed, SourceEditing, GeneralHtmlSupport
		];

		var extra = [];
		if (opts.uploadUrl) {
			extra.push(makeUploadPlugin(opts.uploadUrl, opts.tokenField));
		}

		// The browse button only exists when a caller supplied a browse URL
		var toolbar = [
			'heading', '|',
			'bold', 'italic', 'underline', 'strikethrough', 'code', 'removeFormat', '|',
			'link', 'bulletedList', 'numberedList', 'blockQuote', 'horizontalLine', 'alignment', '|',
			'insertImage', 'mediaEmbed', 'insertTable', '|',
			'sourceEditing', '|', 'undo', 'redo'
		];

		if (opts.fileBrowser && opts.fileBrowser.url) {
			extra.push(makeFileBrowserPlugin(opts.fileBrowser));
			toolbar.splice(toolbar.indexOf('insertTable') + 1, 0, 'hubzeroFileBrowser');
		}

		return ClassicEditor.create(el, {
			licenseKey: 'GPL',
			plugins: plugins,
			extraPlugins: extra,
			toolbar: toolbar,
			image: {
				toolbar: [
					'imageStyle:inline', 'imageStyle:block', 'imageStyle:side', '|',
					'toggleImageCaption', 'imageTextAlternative', '|', 'resizeImage'
				]
			},
			table: { contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'] },
			mediaEmbed: { previewsInData: true },
			htmlSupport: {
				// Allow every element, attribute, class and style, which is what
				// the CKEditor 4 plugin did with extraAllowedContent '*(*)[*]{*}'.
				// Anything narrower is not a display quirk: the editor writes out
				// what it kept, so markup it refuses to load is deleted from the
				// page on the next save. Existing group pages carry custom tags
				// such as <group:include/> and image maps that no fixed list would
				// anticipate. Filtering stays a server-side concern, as before.
				allow: [
					{ name: /.*/, attributes: true, classes: true, styles: true }
				]
			}
		}).then(function (editor) {
			// Sync editor HTML back to the source textarea on form submit, so
			// existing HubZero forms post the content without per-view glue.
			var form = (el && el.closest) ? el.closest('form') : null;
			if (form) {
				form.addEventListener('submit', function () { el.value = editor.getData(); });
			}
			return editor;
		});
	}
};
