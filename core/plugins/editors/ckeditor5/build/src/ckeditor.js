// Modern CKEditor 5 build for the HUBzero `ckeditor5` editor plugin.
//
// Exposes window.HubEditor.create(el, opts) — a ClassicEditor with image
// upload, media embed (YouTube/Vimeo), tables and source editing. It injects
// its own CSS (so the plugin only loads this one script), syncs the editor
// HTML back to the source <textarea> on form submit, and — when opts.uploadUrl
// is given — enables image upload through a custom adapter that POSTs the file
// (plus opts.tokenField as a form field, which HubZero's checkToken reads from
// the body) and inserts the returned URL.

import editorCss from 'ckeditor5/ckeditor5.css';
import {
	ClassicEditor,
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

		return ClassicEditor.create(el, {
			licenseKey: 'GPL',
			plugins: plugins,
			extraPlugins: extra,
			toolbar: [
				'heading', '|',
				'bold', 'italic', 'underline', 'strikethrough', 'code', 'removeFormat', '|',
				'link', 'bulletedList', 'numberedList', 'blockQuote', 'horizontalLine', 'alignment', '|',
				'insertImage', 'mediaEmbed', 'insertTable', '|',
				'sourceEditing', '|', 'undo', 'redo'
			],
			image: {
				toolbar: [
					'imageStyle:inline', 'imageStyle:block', 'imageStyle:side', '|',
					'toggleImageCaption', 'imageTextAlternative', '|', 'resizeImage'
				]
			},
			table: { contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'] },
			mediaEmbed: { previewsInData: true },
			htmlSupport: {
				// Preserve layout containers, figures and media so custom content
				// (e.g. multi-column layouts) round-trips instead of being flattened.
				allow: [
					{ name: /^(div|section|article|aside|figure|figcaption|span)$/, classes: true, styles: true, attributes: true },
					{ name: 'video', attributes: true, classes: true, styles: true },
					{ name: 'source', attributes: true },
					{ name: 'iframe', attributes: true, classes: true, styles: true }
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
