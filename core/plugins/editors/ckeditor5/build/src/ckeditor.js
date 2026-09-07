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
	Plugin, ButtonView, View, Dialog, DomEventObserver,
	Essentials, Paragraph, Heading,
	Bold, Italic, Underline, Strikethrough, Subscript, Superscript, Code, RemoveFormat,
	FontColor, FontBackgroundColor, FontSize,
	Link, LinkImage,
	List, ListProperties, BlockQuote, HorizontalLine, Alignment,
	Indent, IndentBlock, Autoformat, PasteFromOffice,
	Table, TableToolbar, TableProperties, TableCellProperties, TableCaption, TableColumnResize,
	Image, ImageToolbar, ImageCaption, ImageStyle, ImageResize, ImageInsert, ImageUpload,
	MediaEmbed, SourceEditing, GeneralHtmlSupport,
	FindAndReplace, SpecialCharacters, SpecialCharactersEssentials, PageBreak, HtmlEmbed,
	Mention
} from 'ckeditor5';

// Inject the editor stylesheet once.
(function () {
	if (typeof document === 'undefined' || document.getElementById('ckeditor5-styles')) {
		return;
	}
	var s = document.createElement('style');
	s.id = 'ckeditor5-styles';
	s.textContent = editorCss + [
		// Author aids. Both only ever appear in the editing view.
		'.ck-content .hz-highlight{border-radius:2px;padding:0 1px;}',
		'.ck-content .hz-highlight-macro{background:#fff3c4;box-shadow:0 0 0 1px #e0b000;}',
		'.ck-content .hz-highlight-xhub{background:#dcecff;box-shadow:0 0 0 1px #7fb2e5;}',
		// Protected markup is swapped for a placeholder before the editor sees
		// it, and the editing view renders an unknown element as a span marked
		// data-ck-unsafe-element, so without this it is invisible and empty.
		'.ck-content hz-protected,.ck-content [data-ck-unsafe-element="hz-protected"]',
		// CKEditor hides unsafe elements outright; show this one, since its whole
		// purpose is to tell the author that something is there
		'{display:inline-block!important;padding:0 6px;background:#eee;border:1px dashed #999;',
		'border-radius:3px;font:0.85em monospace;color:#444;}',
		'.ck-content hz-protected::before,',
		'.ck-content [data-ck-unsafe-element="hz-protected"]::before{content:"protected markup";}'
	].join('');
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

const EQUATION_ICON = '<svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M3 4h6.2l2.3 5.1L14 4h3l-3.6 6.6L17 17h-3.1l-2.5-5.4L8.7 17H6l3.9-6.7L7.6 6H3V4Z"/></svg>';

// A LaTeX expression rendered server-side to an image. The markup matches the
// CKEditor 4 plugin exactly: the expression is kept on data-equation so the
// image can be edited again later, rather than reverse engineered from the URL.
const EQUATION_CLASS = 'hubzeroequation-result';

function equationHtml(expression, src) {
	return '<img class="' + EQUATION_CLASS + '"'
		+ ' data-equation="' + expression.replace(/"/g, '&quot;') + '"'
		+ ' alt="Equation: ' + expression.replace(/"/g, '&quot;') + '"'
		+ ' src="' + src + '">';
}

// The view document does not watch for double clicks out of the box.
class DoubleClickObserver extends DomEventObserver {
	constructor(view) {
		super(view);
		this.domEventType = 'dblclick';
	}

	onDomEvent(domEvent) {
		this.fire(domEvent.type, domEvent);
	}
}

class HubzeroEquation extends Plugin {
	static get requires() { return [Dialog]; }
	static get pluginName() { return 'HubzeroEquation'; }

	init() {
		const editor = this.editor;
		const url = editor.config.get('hubzero.latexUrl') || '/api/resources/renderlatex';

		addButton(editor, 'hubzeroEquation', 'Equation', EQUATION_ICON, () => this._open(url));

		// Double-clicking a rendered equation reopens it for editing, as it did
		// in CKEditor 4. The class and data-equation are not rendered onto the
		// <img> in the editing view: general html support keeps them on the
		// model and the widget wrapper carries them, so search upwards rather
		// than testing the click target itself.
		editor.editing.view.addObserver(DoubleClickObserver);

		editor.editing.view.document.on('dblclick', (evt, data) => {
			const target = data.domTarget;
			const holder = (target && target.closest) ? target.closest('.' + EQUATION_CLASS) : null;

			if (!holder) {
				return;
			}

			const image = (holder.tagName === 'IMG') ? holder : holder.querySelector('img');

			this._open(
				url,
				holder.getAttribute('data-equation') || '',
				image ? image.getAttribute('src') : ''
			);

			data.preventDefault();
			evt.stop();
		});
	}

	_open(url, expression, src) {
		const editor = this.editor;
		const dialog = editor.plugins.get('Dialog');
		let field, preview, status, timer, last;

		const render = () => {
			const value = field.value.trim();

			if (value === last) {
				return;
			}
			last = value;

			if (!value) {
				preview.removeAttribute('src');
				status.textContent = '';
				return;
			}

			status.textContent = 'Rendering…';

			fetch(url + '?expression=' + encodeURIComponent(value), { credentials: 'same-origin' })
				.then(r => r.json())
				.then(json => {
					if (json && json.error) {
						status.textContent = String(json.error);
						return;
					}
					if (json && json.img) {
						preview.src = json.img;
						status.textContent = '';
					}
				})
				.catch(() => { status.textContent = 'Could not render the expression.'; });
		};

		dialog.show({
			id: 'hubzeroEquation',
			title: expression ? 'Edit equation' : 'Insert equation',
			content: new HubDialogView(editor.locale, el => {
				el.style.cssText = 'padding:12px;min-width:420px;';

				const label = document.createElement('label');
				label.textContent = 'LaTeX expression';
				label.style.cssText = 'display:block;margin-bottom:4px;';

				field = document.createElement('textarea');
				field.rows = 4;
				field.value = expression || '';
				field.style.cssText = 'width:100%;box-sizing:border-box;font-family:monospace;';

				status = document.createElement('div');
				status.style.cssText = 'font-size:12px;min-height:1.2em;margin:6px 0;';

				preview = document.createElement('img');
				preview.alt = 'Preview';
				preview.style.cssText = 'max-width:100%;display:block;';
				if (src) {
					preview.src = src;
				}

				field.addEventListener('keyup', () => {
					clearTimeout(timer);
					timer = setTimeout(render, 600);
				});

				el.appendChild(label);
				el.appendChild(field);
				el.appendChild(status);
				el.appendChild(preview);
			}),
			onHide: () => { clearTimeout(timer); },
			actionButtons: [
				{ label: 'Cancel', withText: true, onExecute: () => dialog.hide() },
				{
					label: expression ? 'Update' : 'Insert',
					withText: true,
					class: 'ck-button-action',
					onExecute: () => {
						const value = field.value.trim();
						const source = preview.getAttribute('src');

						if (!value || !source) {
							status.textContent = 'Enter an expression and wait for the preview.';
							return;
						}

						dialog.hide();
						editor.model.change(() => insertHtml(editor, equationHtml(value, source)));
						editor.editing.view.focus();
					}
				}
			]
		});
	}
}

// Author aids: mark up the things that are not literal text so they are
// visible while editing.
//
// CKEditor 4 did this by rewriting the document — stripping <mark> tags out of
// the data, adding new ones, and calling setData again on every blur. That put
// the content itself at risk for a purely visual effect. Markers do the same
// job in the editing view only and never reach the data.
const HIGHLIGHT_PATTERNS = [
	{ name: 'macro', pattern: /\[\[[^\]]*\]\]/g },
	{ name: 'xhub', pattern: /\{xhub:[^}]*\}/g }
];

const HIGHLIGHT_PREFIX = 'hzHighlight';

class HubzeroHighlight extends Plugin {
	static get pluginName() { return 'HubzeroHighlight'; }

	init() {
		const editor = this.editor;

		editor.conversion.for('editingDowncast').markerToHighlight({
			model: HIGHLIGHT_PREFIX,
			view: ({ markerName }) => {
				const type = markerName.split(':')[1];
				return { classes: ['hz-highlight', 'hz-highlight-' + type] };
			}
		});

		this._refreshing = false;
		editor.model.document.on('change:data', () => this._refresh());
	}

	afterInit() {
		this._refresh();
	}

	_refresh() {
		if (this._refreshing) {
			return;
		}
		this._refreshing = true;

		const model = this.editor.model;

		// Not undoable and not affecting data: these markers are decoration, and
		// must not put the document into a modified state or land in the undo
		// stack, which is exactly what the CKEditor 4 version got wrong.
		model.enqueueChange({ isUndoable: false }, writer => {
			for (const marker of Array.from(model.markers)) {
				if (marker.name.indexOf(HIGHLIGHT_PREFIX + ':') === 0) {
					writer.removeMarker(marker.name);
				}
			}

			let index = 0;

			for (const rootName of model.document.getRootNames()) {
				const root = model.document.getRoot(rootName);

				for (const item of model.createRangeIn(root).getItems()) {
					if (!item.is('$text') && !item.is('$textProxy')) {
						continue;
					}

					for (const entry of HIGHLIGHT_PATTERNS) {
						entry.pattern.lastIndex = 0;

						let match;
						while ((match = entry.pattern.exec(item.data)) !== null) {
							const from = item.startOffset + match.index;

							writer.addMarker(HIGHLIGHT_PREFIX + ':' + entry.name + ':' + (index++), {
								range: writer.createRange(
									writer.createPositionAt(item.parent, from),
									writer.createPositionAt(item.parent, from + match[0].length)
								),
								usingOperation: false,
								affectsData: false
							});
						}
					}
				}
			}
		});

		this._refreshing = false;
	}
}

// Mentions.
//
// Callers configure these the way the CKEditor 4 plugin wants them: a feed URL
// carrying {encodedQuery}, and itemTemplate/outputTemplate strings. CKEditor 5
// wants a feed function, an itemRenderer returning an element, and a downcast
// for the output markup, so translate rather than ask every caller to change.
function fillTemplate(template, values) {
	return template.replace(/\{(\w+)\}/g, (match, key) => {
		return (values[key] === undefined || values[key] === null) ? '' : String(values[key]);
	});
}

function makeMentionFeed(config) {
	const marker = config.marker || '@';

	return {
		marker: marker,
		minimumCharacters: (config.minChars === undefined) ? 0 : Number(config.minChars),

		feed: query => {
			const url = config.feed
				.replace(/\{encodedQuery\}/g, encodeURIComponent(query))
				.replace(/\{query\}/g, query);

			return fetch(url, { credentials: 'same-origin', headers: { Accept: 'application/json' } })
				.then(response => response.ok ? response.json() : [])
				.then(rows => (Array.isArray(rows) ? rows : []).map(row => ({
					// CKEditor requires the id to start with the marker; the
					// original row is kept alongside for the templates
					id: marker + row.username,
					userId: row.id,
					username: row.username,
					name: row.name,
					picture: row.picture
				})))
				.catch(() => []);
		},

		itemRenderer: item => {
			const holder = document.createElement('div');
			holder.innerHTML = fillTemplate(config.itemTemplate || '<span>{username}</span>', {
				id: item.userId,
				username: item.username,
				name: item.name,
				picture: item.picture
			});

			return holder.firstElementChild || document.createTextNode(item.username);
		}
	};
}

// Render a mention as the link the CKEditor 4 output template produced, rather
// than CKEditor 5's default <span class="mention">.
function makeMentionOutputPlugin(template) {
	return function (editor) {
		editor.conversion.for('dataDowncast').attributeToElement({
			model: 'mention',
			view: (value, { writer }) => {
				if (!value) {
					return;
				}

				const holder = document.createElement('div');
				holder.innerHTML = fillTemplate(template, {
					id: value.userId,
					username: value.username,
					name: value.name
				});

				const element = holder.firstElementChild;
				if (!element) {
					return;
				}

				const attributes = {};
				Array.from(element.attributes).forEach(attribute => {
					attributes[attribute.name] = attribute.value;
				});

				return writer.createAttributeElement(element.tagName.toLowerCase(), attributes, { priority: 20 });
			},
			converterPriority: 'high'
		});
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
		extra.push(HubzeroMacro, HubzeroGrid, HubzeroEquation, HubzeroHighlight);

		var mentionConfig = null;
		if (opts.mentions && opts.mentions.length) {
			plugins.push(Mention);
			mentionConfig = { feeds: opts.mentions.map(makeMentionFeed) };

			var outputTemplate = opts.mentions[0].outputTemplate;
			if (outputTemplate) {
				extra.push(makeMentionOutputPlugin(outputTemplate));
			}
		}

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
				'hubzeroGrid', 'hubzeroEquation', 'hubzeroMacro', '|',
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
			mention: mentionConfig || {},
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
