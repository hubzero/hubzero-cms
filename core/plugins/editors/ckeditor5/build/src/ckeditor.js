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
	Plugin, ButtonView, View, Dialog,
	Essentials, Paragraph, Heading,
	Bold, Italic, Underline, Strikethrough, Subscript, Superscript, Code, RemoveFormat,
	FontColor, FontBackgroundColor, FontSize,
	Link, LinkImage,
	List, ListProperties, BlockQuote, HorizontalLine, Alignment,
	Indent, IndentBlock, Autoformat, PasteFromOffice,
	Table, TableToolbar, TableProperties, TableCellProperties, TableCaption, TableColumnResize,
	Image, ImageToolbar, ImageCaption, ImageStyle, ImageResize, ImageInsert, ImageUpload,
	MediaEmbed, SourceEditing, GeneralHtmlSupport,
	FindAndReplace, SpecialCharacters, SpecialCharactersEssentials, PageBreak, HtmlEmbed
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

// Markup CKEditor 5 must not be allowed to parse.
//
// A PHP block becomes a nameless comment node and crashes conversion outright,
// so any page containing one cannot be opened at all. A self-closing namespaced
// tag such as <group:include ... /> stays open instead, because HTML5 ignores
// the trailing slash on an unknown element, and swallows the block after it.
//
// CKEditor 4 handled both with config.protectedSource. There is no equivalent
// here, so swap each match for a placeholder element before the data processor
// sees it and put the original back on the way out. Everything else CKEditor 4
// protected (image maps, {xhub:} macros) round-trips correctly on its own now
// that the html support list is permissive, and is left alone.
var PROTECTED_ALWAYS = [
	/<\?[\s\S]*?\?>/g,
	/<([a-z][\w-]*:[\w-]+)([^>]*?)\/>/gi
];

var PROTECTED_SCRIPT = /<script[^>]*>[\s\S]*?<\/script>/gi;

var PLACEHOLDER = /<hz-protected data-hz-source="(\d+)"><\/hz-protected>/g;

function makeProtectedSourcePlugin(patterns) {
	return function (editor) {
		installProtectedSource(editor, patterns);
	};
}

function installProtectedSource(editor, patterns) {
	if (!patterns.length) {
		return;
	}

	var store = [];
	var processor = editor.data.processor;
	var toView = processor.toView.bind(processor);
	var toData = processor.toData.bind(processor);

	processor.toView = function (data) {
		patterns.forEach(function (pattern) {
			data = data.replace(pattern, function (match) {
				store.push(match);
				return '<hz-protected data-hz-source="' + (store.length - 1) + '"></hz-protected>';
			});
		});

		return toView(data);
	};

	processor.toData = function (fragment) {
		return toData(fragment).replace(PLACEHOLDER, function (match, index) {
			return (store[index] !== undefined) ? store[index] : '';
		});
	};
}

// A dialog body built from plain DOM. The CKEditor 5 template system is not a
// good fit for the small ad-hoc forms these plugins need, so give each one a
// container and let it fill it in.
class HubDialogView extends View {
	constructor(locale, build) {
		super(locale);
		this._build = build;
		this.setTemplate({ tag: 'div', attributes: { class: ['ck', 'ck-reset_all-excluded', 'hz-dialog'] } });
	}

	render() {
		super.render();
		this._build(this.element);
	}
}

// Insert a fragment of HTML at the selection. Goes through the data processor,
// so protected source and the html support list apply as they do on load.
function insertHtml(editor, html) {
	editor.model.insertContent(editor.data.toModel(editor.data.processor.toView(html)));
}

// Register a toolbar button that runs a callback.
function addButton(editor, name, label, icon, onExecute) {
	editor.ui.componentFactory.add(name, locale => {
		const view = new ButtonView(locale);
		view.set({ label: label, icon: icon, tooltip: true });
		view.on('execute', onExecute);
		return view;
	});
}

const MACRO_ICON = '<svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M4 3h12v2H4V3Zm0 4h12v2H4V7Zm0 4h8v2H4v-2Zm0 4h8v2H4v-2Zm10.5-1.2 2.2-2.2-1.1-1.1-2.2 2.2-2.2-2.2-1.1 1.1 2.2 2.2-2.2 2.2 1.1 1.1 2.2-2.2 2.2 2.2 1.1-1.1-2.2-2.2Z"/></svg>';
const GRID_ICON  = '<svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M2 3h5v14H2V3Zm6.5 0h3v14h-3V3ZM13 3h5v14h-5V3Zm1.5 1.5v11h2v-11h-2ZM3.5 4.5v11h2v-11h-2Zm6.5 0v11h1v-11h-1Z"/></svg>';

// Column layouts, matching the CKEditor 4 grid dialog.
const GRID_LAYOUTS = [
	{ value: 'one',   label: 'One',   cols: 1, cls: 'col span12' },
	{ value: 'two',   label: 'Two',   cols: 2, cls: 'col span6' },
	{ value: 'three', label: 'Three', cols: 3, cls: 'col span4' },
	{ value: 'four',  label: 'Four',  cols: 4, cls: 'col span3' },
	{ value: 'five',  label: 'Five',  cols: 5, cls: 'col five columns' },
	{ value: 'six',   label: 'Six',   cols: 6, cls: 'col span2' }
];

function buildGrid(value, placeholders) {
	const layout = GRID_LAYOUTS.find(l => l.value === value) || GRID_LAYOUTS[0];
	let html = '<div class="grid">';

	for (let i = 0; i < layout.cols; i++) {
		const cls = layout.cls + ((i + 1 === layout.cols) ? ' omega' : '');
		html += '<div class="' + cls + '">' + (placeholders ? 'Column ' + (i + 1) : '') + '</div>';
	}

	return html + '</div>';
}

// A reference list of the macros available on this hub, shown in a dialog.
// It inserts nothing; the CKEditor 4 version was a help panel too.
class HubzeroMacro extends Plugin {
	static get requires() { return [Dialog]; }
	static get pluginName() { return 'HubzeroMacro'; }

	init() {
		const editor = this.editor;
		const url = (editor.config.get('hubzero.macroUrl')) || '/help/content/formathtml/macros';

		addButton(editor, 'hubzeroMacro', 'Macros', MACRO_ICON, () => {
			const dialog = editor.plugins.get('Dialog');

			dialog.show({
				id: 'hubzeroMacro',
				title: 'Macros',
				content: new HubDialogView(editor.locale, el => {
					const frame = document.createElement('iframe');
					frame.src = url;
					frame.title = 'Macros';
					frame.style.cssText = 'width:760px;height:460px;border:0;display:block;';
					el.appendChild(frame);
				}),
				actionButtons: [
					{ label: 'Close', withText: true, onExecute: () => dialog.hide() }
				]
			});
		});
	}
}

// Insert a column layout.
class HubzeroGrid extends Plugin {
	static get requires() { return [Dialog]; }
	static get pluginName() { return 'HubzeroGrid'; }

	init() {
		const editor = this.editor;

		addButton(editor, 'hubzeroGrid', 'Grid', GRID_ICON, () => {
			const dialog = editor.plugins.get('Dialog');
			let select, check;

			dialog.show({
				id: 'hubzeroGrid',
				title: 'Grid creator',
				content: new HubDialogView(editor.locale, el => {
					el.style.cssText = 'padding:12px;min-width:280px;';

					const row = document.createElement('div');
					row.style.cssText = 'margin-bottom:10px;';
					const label = document.createElement('label');
					label.textContent = 'Number of columns: ';
					select = document.createElement('select');
					GRID_LAYOUTS.forEach(l => {
						const o = document.createElement('option');
						o.value = l.value;
						o.textContent = l.label;
						select.appendChild(o);
					});
					label.appendChild(select);
					row.appendChild(label);

					const row2 = document.createElement('div');
					const label2 = document.createElement('label');
					check = document.createElement('input');
					check.type = 'checkbox';
					label2.appendChild(check);
					label2.appendChild(document.createTextNode(' Include placeholders'));
					row2.appendChild(label2);

					el.appendChild(row);
					el.appendChild(row2);
				}),
				actionButtons: [
					{ label: 'Cancel', withText: true, onExecute: () => dialog.hide() },
					{
						label: 'Insert',
						withText: true,
						class: 'ck-button-action',
						onExecute: () => {
							const html = buildGrid(select.value, check.checked);
							dialog.hide();
							editor.model.change(() => insertHtml(editor, html));
							editor.editing.view.focus();
						}
					}
				]
			});
		});
	}
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
			Bold, Italic, Underline, Strikethrough, Subscript, Superscript, Code, RemoveFormat,
			FontColor, FontBackgroundColor, FontSize,
			Link, LinkImage,
			List, ListProperties, BlockQuote, HorizontalLine, Alignment,
			Indent, IndentBlock, Autoformat, PasteFromOffice,
			Table, TableToolbar, TableProperties, TableCellProperties, TableCaption, TableColumnResize,
			Image, ImageToolbar, ImageCaption, ImageStyle, ImageResize, ImageInsert, ImageUpload,
			MediaEmbed, SourceEditing, GeneralHtmlSupport,
			FindAndReplace, SpecialCharacters, SpecialCharactersEssentials, PageBreak, HtmlEmbed
		];

		var extra = [];
		if (opts.uploadUrl) {
			extra.push(makeUploadPlugin(opts.uploadUrl, opts.tokenField));
		}

		// Registered as a plugin so it is in place before the editor parses the
		// element's initial content, which is where a PHP block would otherwise
		// crash conversion before create() ever resolves
		var protectedPatterns = PROTECTED_ALWAYS.slice();
		if (opts.allowScriptTags) {
			protectedPatterns.push(PROTECTED_SCRIPT);
		}
		extra.push(makeProtectedSourcePlugin(protectedPatterns));

		// The Hubzero authoring tools. Macro is a reference panel; the rest
		// insert or annotate content.
		extra.push(HubzeroMacro, HubzeroGrid);

		// The cut-down toolbar mirrors the CKEditor 4 plugin's 'minimal' class,
		// which the forum and other short-form fields ask for
		var toolbar = opts.minimal
			? [
				'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript', '|',
				'link', '|',
				'bulletedList', 'numberedList'
			]
			: [
				'heading', '|',
				'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript',
				'code', 'removeFormat', '|',
				'fontSize', 'fontColor', 'fontBackgroundColor', '|',
				'link', 'bulletedList', 'numberedList', 'blockQuote', 'horizontalLine', 'alignment',
				'outdent', 'indent', '|',
				'insertImage', 'mediaEmbed', 'insertTable', 'specialCharacters', 'pageBreak', 'htmlEmbed', '|',
				'hubzeroGrid', 'hubzeroMacro', '|',
				'findAndReplace'
			];

		// Images are insertable in minimal mode only when asked for
		if (opts.minimal && opts.images) {
			toolbar.push('|', 'insertImage');
		}

		// As in CKEditor 4, the macro reference is offered in minimal mode only
		// when the caller asks for it
		if (opts.minimal && opts.macros) {
			toolbar.push('|', 'hubzeroMacro');
		}

		if (opts.fileBrowser && opts.fileBrowser.url) {
			extra.push(makeFileBrowserPlugin(opts.fileBrowser));

			var after = toolbar.indexOf('insertTable');
			if (after === -1) {
				toolbar.push('hubzeroFileBrowser');
			} else {
				toolbar.splice(after + 1, 0, 'hubzeroFileBrowser');
			}
		}

		// The source button is on unless a caller turned it off
		if (opts.sourceViewButton !== false) {
			toolbar.push('|', 'sourceEditing');
		}

		toolbar.push('|', 'undo', 'redo');

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
			table: {
				contentToolbar: [
					'tableColumn', 'tableRow', 'mergeTableCells',
					'tableProperties', 'tableCellProperties', 'toggleTableCaption'
				]
			},
			list: { properties: { styles: true, startIndex: true, reversed: true } },
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
			if (opts.minHeight) {
				editor.editing.view.change(function (writer) {
					writer.setStyle('min-height', opts.minHeight, editor.editing.view.document.getRoot());
				});
			}

			if (opts.startInSource && editor.plugins.has('SourceEditing')) {
				editor.plugins.get('SourceEditing').isSourceEditingMode = true;
			}

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
