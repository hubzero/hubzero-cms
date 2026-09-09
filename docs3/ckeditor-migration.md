# CKEditor in Hubzero CMS: Current Use and Migration Plan

## Part 1: Current State of CKEditor

### Overview

Hubzero CMS ships **two** CKEditor editor plugins:

| | CKEditor 4 | CKEditor 5 |
|---|---|---|
| **Path** | `core/plugins/editors/ckeditor/` | `core/plugins/editors/ckeditor5/` |
| **Version** | 4.22.1 (Full) | ~10.x (2018 pre-stable build) |
| **JS size** | 758 KB (minified) | 392 KB (minified + source map) |
| **Status** | Default editor, fully configured | Skeleton — minimal, mostly stub methods |
| **Custom plugins** | 5 Hubzero-specific | None |
| **Skins** | 6 (moono, moono-dark, moono-lisa, moonocolor, office2013, bootstrapck) | None (CSS-in-JS) |
| **Languages** | 70+ standalone files | English only (bundled) |
| **EOL** | CKEditor 4 EOL was June 2023 | This build is far too old to use |

**CKEditor 4 is the system default** — set in `core/bootstrap/Install/config/app.php` line 88.

The CKEditor 5 plugin is essentially a proof-of-concept from 2018. Its `onGetContent()`, `onSetContent()`, and `onGetInsertMethod()` all return empty strings. It ships no custom plugins and has no feature parity with the CKEditor 4 plugin.

---

### 1. Editor Infrastructure (Generic)

The editor system is abstracted behind a plugin interface. Components should never reference "ckeditor" directly — they go through:

| File | Role |
|---|---|
| `core/libraries/Hubzero/Html/Editor.php` | Wrapper/facade — loads editor plugin by name, delegates `display()`, `save()`, `getContent()`, `setContent()`, `getButtons()` |
| `core/bootstrap/Site/Providers/EditorServiceProvider.php` | Registers `App::get('editor')` for site; resolves user pref → global config → `'none'` fallback |
| `core/bootstrap/Administrator/Providers/EditorServiceProvider.php` | Same for admin |
| `core/libraries/Hubzero/Form/Fields/Editor.php` | `<field type="editor">` form field |
| `core/libraries/Hubzero/View/Helper/Editor.php` | View helper for templates |

**Most components use the generic interface correctly:**
```php
$editor = App::get('editor');
echo $editor->display('fieldname', $content, '100%', '400');
```

### 2. CKEditor 4 Plugin (`core/plugins/editors/ckeditor/`)

#### 2.1 Main Plugin File — `ckeditor.php`

- **Namespace:** `Plugins\Editors\Ckeditor`
- **`onInit()`** — loads `ckeditor.js` and jQuery adapter
- **`onDisplay()`** — renders `<textarea>` with JSON config in a `<script>` tag
- **`buildConfig()`** — 280-line method assembling toolbar, plugins, protected sources, spell check, file browser URLs, templates, CodeMirror source view, word count, mentions

#### 2.2 Configuration XML — `ckeditor.xml`

Exposes admin-configurable parameters:
- Skin selection
- Startup mode (WYSIWYG vs source)
- Color button, font size toggles
- Spell check (SCAYT) settings
- File browser dimensions
- Source view button toggle

#### 2.3 Custom Hubzero Plugins (CKEditor 4 plugin API)

All in `core/plugins/editors/ckeditor/assets/plugins/`:

| Plugin | Purpose | Complexity |
|---|---|---|
| **hubzeroautogrow** | Auto-resizes editor height as content grows. Handles both WYSIWYG iframe and CodeMirror source mode. Config: min/max height. | Medium — event listeners on `key`, `mode`, `maximize` |
| **hubzeroequation** | Insert LaTeX equations rendered as images. Dialog with live preview via AJAX call to `/api/resources/renderlatex`. Stores equation in `data-equation` attribute. Context menu for editing. | High — dialog, AJAX, context menu |
| **hubzerogrid** | Insert responsive grid layouts (1-6 columns). Generates `<div class="grid">` with `col span*` classes. | Medium — dialog with dropdown |
| **hubzerohighlight** | Visually highlights `[[macros]]`, `<group:include>` tags, and `{xhub:...}` tags with colored `<mark>` overlays in the editor. Strips marks on form submission so raw content is preserved. | Medium — regex replacement, CSS, event-driven |
| **hubzeromacro** | Opens iframe to `/help/content/formathtml/macros` as a reference popup. Read-only (no insertion). | Low — iframe dialog |

#### 2.4 Bundled Third-Party Plugins (50 total)

Notable ones actively used:
- `codemirror` — source view
- `wordcount` — character/word counting
- `mentions` — @-mention autocomplete
- `tableresize`, `tabletools`, `tableselection` — rich table editing
- `scayt` — spell check as you type
- `iframedialog` — used by equation plugin
- `templates` — content templates (Hubzero-specific `hub.js`)
- `pastefromword`, `pastefromgdocs`, `pastefromlibreoffice` — paste cleanup

#### 2.5 Protected Source Patterns

CKEditor 4 is configured to preserve Hubzero-specific markup that would otherwise be stripped:
```javascript
protectedSource: [
    /<group:include([^>]*)\/>/g,      // Group include tags
    /{xhub:([^}]*)}/gi,               // XHub macro tags
    /<map[^>]*>(.|\n)*<\/map>/ig,     // Image maps
    /<area([^>]*)\/?>/ig              // Image map areas
]
```

And optionally:
- `<script>` tags (for super group pages)
- `<?php ?>` tags (for super group pages)

#### 2.6 Skins

Default skin: `moono`. Six skins shipped — 177 CSS files total including browser-specific variants (IE, gecko, opera).

#### 2.7 Template CSS Overrides

Three templates override CKEditor 4 content styles:
- `core/templates/kameleon/html/plg_editors_ckeditor/ckeditor.css` (2 KB)
- `core/templates/kimera/html/plg_editors_ckeditor/ckeditor.css` (1.7 KB)
- `core/templates/hzadmin/html/plg_editors_ckeditor/ckeditor.css` (2 KB)

Legacy LESS files:
- `core/templates/lucent/less/template/_layout/_ckeditor.less`
- `core/templates/lucent/less/template/layout/_ckeditor.less`

### 3. CKEditor 5 Plugin (`core/plugins/editors/ckeditor5/`)

**This is not production-ready.** Key issues:
- 2018-era build (CKEditor 5 v10/v11 — current is v44+)
- `onGetContent()`, `onSetContent()`, `onGetInsertMethod()` return empty strings
- No custom plugins ported
- No protected source support
- Empty config object: `{ }`
- Hardcoded `.ck-content { height: Xem }` with known overwrite bug (noted in comments)
- No file browser integration
- No spell check
- English only

### 4. JavaScript Files That Directly Use the CKEditor 4 API

These files call the `CKEDITOR` global directly — they will break if CKEditor 4 is removed without adaptation:

| File | API Used | Context |
|---|---|---|
| `core/plugins/projects/publications/assets/js/curation.js` (lines 622-689) | `CKEDITOR.instances[id].getData()` | Polls editor content every 1s to track completion status of publication fields |
| `core/plugins/courses/outline/assets/js/tool.js` (lines 117-119) | `CKEDITOR.instances[i].updateElement()` | Syncs all CKEditor instances to their textareas before form submission |
| `core/plugins/courses/outline/assets/js/wiki.js` (lines 76-78) | `CKEDITOR.instances[i].updateElement()` | Same sync pattern before form submission |
| `core/components/com_groups/site/assets/js/groups.medialist.js` (lines 303-310) | `CKEDITOR.tools.callFunction()` | File browser callback to insert selected files into editor |

### 5. PHP Files with CKEditor-Specific References

| File | Issue |
|---|---|
| `core/components/com_groups/site/views/modules/tmpl/edit.php:126` | **Hardcodes** `new \Hubzero\Html\Editor('ckeditor')` instead of using `App::get('editor')` |
| `core/components/com_groups/site/views/pages/tmpl/edit.php:187` | Uses `App::get('editor')` but has commented-out `new \Hubzero\Html\Editor('ckeditor')` fallback |
| `core/components/com_groups/site/controllers/media.php` (lines 921-934) | Handles `CKEditor` and `CKEditorFuncNum` GET parameters for file browser callback |
| `core/components/com_groups/site/views/media/tmpl/filebrowser.php` | Extracts and passes CKEditor query params to iframe |
| `core/components/com_groups/site/views/media/tmpl/filelist.php` | Renders "Insert File" button using CKEditor callback, embeds `ckeditorInsertFile()` JS function |
| `core/plugins/groups/files/views/files/tmpl/filebrowser.php` | Extracts CKEditor query params for file browser integration |

### 6. Components Using the Editor (via Generic Interface)

These use `App::get('editor')->display()` and will work with any editor:

- `com_groups` — page editing, module editing
- `com_events` — event descriptions
- `com_resources` — textarea element type
- `com_publications` — editor element type
- `com_content` — article editing (admin)
- `com_categories` — category descriptions (admin)
- `com_feedback` — quote editing (admin)
- `com_forum` — thread/post editing
- `com_kb` — knowledge base articles
- Groups collections plugin
- Members collections plugin

### 7. Other Editor Plugins Available

| Plugin | Path | Notes |
|---|---|---|
| `none` | `core/plugins/editors/none/` | Plain textarea fallback |
| `codemirror` | `core/plugins/editors/codemirror/` | Code-only editor |
| `tinymce` | `core/plugins/editors/tinymce/` | Alternative WYSIWYG |
| `pagedown` | `core/plugins/editors/pagedown/` | Markdown editor |
| `wikitoolbar` | `core/plugins/editors/wikitoolbar/` | Wiki format toolbar |
| `wikiwyg` | `core/plugins/editors/wikiwyg/` | Wiki WYSIWYG |

### 8. Editors-XTD Plugins (Extension Buttons)

These provide additional buttons below the editor (article link, image insert, page break, read more). They work through the generic `Editor::getButtons()` interface and are editor-agnostic.

| Plugin | Path | Function |
|---|---|---|
| `article` | `core/plugins/editors-xtd/article/` | Insert article link via modal |
| `image` | `core/plugins/editors-xtd/image/` | Insert image via media browser modal |
| `pagebreak` | `core/plugins/editors-xtd/pagebreak/` | Insert page break |
| `readmore` | `core/plugins/editors-xtd/readmore/` | Insert read-more divider |

---

## Part 2: Migration Plan — CKEditor 4 to CKEditor 5

### Goals

1. Replace the EOL CKEditor 4 with a current CKEditor 5 build
2. Port all 5 custom Hubzero plugins to CKEditor 5's plugin architecture
3. Maintain the generic editor interface so the swap is transparent to components
4. Update all direct `CKEDITOR` API references in JavaScript
5. Remove CKEditor 4 assets entirely

### Licensing Note

CKEditor 5 offers both open-source (GPL) and commercial licenses. As of 2024, CKEditor 5 v42+ uses a "new installation methods" model with npm packages. Evaluate licensing requirements before proceeding. The open-source GPL license is suitable for Hubzero if the CMS remains open-source.

### Phase 1: Build a New CKEditor 5 Distribution

**Objective:** Create a custom CKEditor 5 build with the features matching CKEditor 4 usage.

1. **Set up a CKEditor 5 custom build** using the [CKEditor 5 online builder](https://ckeditor.com/ckeditor-5/builder/) or npm packages
2. **Include these core plugins** (matching current CKEditor 4 toolbar):
   - Essentials (undo, redo, clipboard, enter)
   - Bold, Italic, Underline, Strikethrough, Subscript, Superscript
   - Heading / Format dropdown
   - Link
   - Image (insert, upload, resize)
   - Table (with resize, toolbars)
   - List (numbered, bulleted)
   - BlockQuote
   - HorizontalLine
   - SpecialCharacters
   - FindAndReplace
   - SourceEditing (replaces Source + CodeMirror)
   - HtmlEmbed or GeneralHtmlSupport (for raw HTML)
   - PageBreak
   - Alignment (justify left/center/right/block)
   - Iframe (via HtmlEmbed or GeneralHtmlSupport)
   - Mention (replaces mentions plugin)
   - WordCount
   - AutoGrow / editor min-height via CSS
   - PasteFromOffice
   - Font (color, background color, size — optional toggles)
3. **Enable GeneralHtmlSupport (GHS)** — critical for preserving Hubzero-specific tags:
   - `<group:include>` tags
   - `{xhub:...}` macro syntax
   - `<map>` / `<area>` tags
   - `<mark>` tags (used by highlight plugin)
4. **Output:** Single `ckeditor5.js` bundle + CSS, placed in `core/plugins/editors/ckeditor5/assets/`

### Phase 2: Port Custom Hubzero Plugins to CKEditor 5

CKEditor 5 uses a completely different plugin architecture (ES6 classes extending `Plugin`, using the model-view-controller pattern). Each Hubzero plugin needs to be rewritten.

#### 2.1 hubzeroautogrow → CSS-based

CKEditor 5 handles auto-grow natively — the classic editor grows with content by default. Configure via:
```css
.ck-editor__editable_inline {
    min-height: 200px;
    max-height: 1000px;
}
```
**No custom plugin needed.** Remove hubzeroautogrow entirely.

#### 2.2 hubzeroequation → Custom CKEditor 5 Plugin

- Create as an ES6 `Plugin` class
- Register a toolbar button via `ButtonView`
- Open a balloon or dialog for LaTeX input
- Call `/api/resources/renderlatex` for preview (same AJAX endpoint)
- Insert as a `<figure class="equation">` or inline `<img>` with `data-equation` attribute
- Register a model element and upcast/downcast converters
- Register double-click handler for editing

**Estimated complexity:** High. Consider using the CKEditor 5 `Widget` API for proper selection/editing behavior.

#### 2.3 hubzerogrid → Custom CKEditor 5 Plugin

- Register toolbar button
- Balloon/dropdown UI to select column count
- Insert HTML structure using `editor.model` API
- Use GHS to preserve the `<div class="grid">` / `<div class="col span*">` structure
- Consider making grid divs `Widget` elements for better editing UX

**Estimated complexity:** Medium.

#### 2.4 hubzerohighlight → GHS + CSS

CKEditor 5's General HTML Support can be configured to allow `<mark>` tags. The visual highlighting of macros and special tags can be achieved via:

- **Option A:** CSS-only in the editor content styles — style `[[...]]` text patterns via a CKEditor 5 plugin that adds marker decorations in the editing view
- **Option B:** Post-processing plugin that applies decorations to matching text patterns in the view layer (not the model), similar to how spell-check underlining works

The current approach of regex-replacing content with `<mark>` tags and stripping on submit is fragile. CKEditor 5's decoration API is the proper replacement.

**Estimated complexity:** Medium-High.

#### 2.5 hubzeromacro → Simple Plugin (iframe popup)

- Register toolbar button
- Open an iframe/modal to `/help/content/formathtml/macros`
- Read-only reference — no model interaction needed

**Estimated complexity:** Low.

### Phase 3: Update the CKEditor 5 PHP Plugin

Rewrite `core/plugins/editors/ckeditor5/ckeditor5.php` to match CKEditor 4's feature set:

1. **`onInit()`** — load the new CKEditor 5 JS bundle and CSS
2. **`onDisplay()`** — render textarea + initialization script with full config:
   - Toolbar configuration
   - GHS rules for Hubzero-specific HTML
   - File browser integration (CKEditor 5 uses `ckfinder` adapter or custom upload adapters)
   - Mention configuration
   - Word count configuration
   - Source editing toggle
   - Skin/theme from admin config
3. **`onGetContent()`** — return JS: `editor.getData()`
4. **`onSetContent()`** — return JS: `editor.setData(html)`
5. **`onSave()`** — CKEditor 5 auto-syncs to the source element; may need explicit `updateSourceElement()` call
6. **`buildConfig()`** — translate all CKEditor 4 config options to CKEditor 5 equivalents

#### Config Mapping

| CKEditor 4 | CKEditor 5 Equivalent |
|---|---|
| `config.toolbar` (array of arrays) | `toolbar.items` (flat array with `'|'` separators) |
| `config.extraPlugins` | Plugins included in build, enabled via config |
| `config.protectedSource` | `htmlSupport.allow` rules in GHS |
| `config.extraAllowedContent` | `htmlSupport.allow` with element/attribute/class/style patterns |
| `config.allowedContent = true` | Not needed — GHS with permissive config |
| `config.contentsCss` | Import CSS in build or use `style` config |
| `config.height` | CSS on `.ck-editor__editable` |
| `config.skin` | CKEditor 5 theming via CSS custom properties |
| `config.startupMode` | No direct equivalent — SourceEditing plugin toggles |
| `config.filebrowserBrowseUrl` | Custom upload adapter or CKFinder integration |
| `config.scayt_*` | CKEditor 5 uses WProofreader (commercial) or browser spell check |
| `config.codemirror` | SourceEditing plugin (built-in, no CodeMirror needed) |
| `config.wordcount.*` | `wordCount` plugin config |
| `config.mentions` | `mention` plugin config |
| `config.templates` | Content templates via plugin |
| `config.bodyClass` | CSS class on `.ck-editor__editable` |

### Phase 4: Update Direct CKEditor API References

#### 4.1 `curation.js` (Publications)

**Current:** `CKEDITOR.instances[id].getData()`

**Migration:**
```javascript
// CKEditor 5 stores editor instances differently
// Option A: Use a global registry
const editorInstance = window.hubEditors[editorId];
const data = editorInstance ? editorInstance.getData() : '';

// Option B: Query the DOM element
const element = document.querySelector('#' + editorId);
const editorInstance = element?.ckeditorInstance;
const data = editorInstance ? editorInstance.getData() : element?.value || '';
```

CKEditor 5 exposes `element.ckeditorInstance` on the source element when using `ClassicEditor.create()`. This is the cleanest migration path.

#### 4.2 `tool.js` and `wiki.js` (Courses)

**Current:** Loop through `CKEDITOR.instances` calling `updateElement()`

**Migration:**
CKEditor 5's `ClassicEditor` automatically updates the source textarea via `updateSourceElement()`. This may no longer be needed. If explicit sync is required:
```javascript
document.querySelectorAll('.ck-editor__editable').forEach(el => {
    if (el.ckeditorInstance) {
        el.ckeditorInstance.updateSourceElement();
    }
});
```

#### 4.3 `groups.medialist.js` (Groups)

**Current:** `w.opener.CKEDITOR.tools.callFunction(funcNum, file)`

**Migration:**
CKEditor 5 does not use `tools.callFunction()`. File browser integration uses an upload adapter pattern. Replace with:
- A custom upload adapter that opens the Hubzero media browser
- Or `postMessage`-based communication between the browser popup and the editor

This is the most significant JS migration point.

#### 4.4 File Browser Templates

The `filebrowser.php`, `filelist.php` templates, and `media.php` controller all pass `CKEditor` and `CKEditorFuncNum` parameters. These are CKEditor 4-specific.

**Migration approach:**
- Replace with a generic callback mechanism (e.g., `postMessage` to parent window)
- The file browser becomes editor-agnostic
- The CKEditor 5 plugin registers a `postMessage` listener that calls `editor.execute('insertImage', { source: url })`

### Phase 5: Update Template Overrides

1. **Remove** CKEditor 4 content CSS overrides:
   - `core/templates/kameleon/html/plg_editors_ckeditor/ckeditor.css`
   - `core/templates/kimera/html/plg_editors_ckeditor/ckeditor.css`
   - `core/templates/hzadmin/html/plg_editors_ckeditor/ckeditor.css`
2. **Create** CKEditor 5 content CSS overrides if needed:
   - `core/templates/*/html/plg_editors_ckeditor5/ckeditor5.css`
   - CKEditor 5 theming uses CSS custom properties — much simpler to customize
3. **Remove** legacy LESS files:
   - `core/templates/lucent/less/template/_layout/_ckeditor.less`
   - `core/templates/lucent/less/template/layout/_ckeditor.less`

### Phase 6: Fix Hardcoded References

1. **`com_groups/site/views/modules/tmpl/edit.php`** — change `new \Hubzero\Html\Editor('ckeditor')` to `App::get('editor')`
2. **`com_groups/site/views/pages/tmpl/edit.php`** — remove commented-out ckeditor fallback

### Phase 7: Remove CKEditor 4

1. Delete `core/plugins/editors/ckeditor/` entirely (plugin file, assets, migrations)
2. Update default editor config in `core/bootstrap/Install/config/app.php` from `'ckeditor'` to `'ckeditor5'`
3. Create a database migration to:
   - Update the `extensions` table to disable the ckeditor plugin
   - Enable the ckeditor5 plugin
   - Update global config editor setting
   - Update any user params that reference `'ckeditor'` to `'ckeditor5'`
4. Update help documentation in `com_groups`

### Phase 8: Testing Checklist

- [ ] Editor loads and renders on all component edit pages
- [ ] Content round-trips correctly (save and reload preserves all HTML)
- [ ] `[[macro]]` syntax preserved through editor
- [ ] `{xhub:...}` tags preserved through editor
- [ ] `<group:include>` tags preserved through editor
- [ ] LaTeX equation insertion and editing works
- [ ] Grid layout insertion works
- [ ] Macro reference popup works
- [ ] File browser integration works (groups media)
- [ ] Image insertion via file browser works
- [ ] Paste from Word/Google Docs preserves formatting
- [ ] Source editing view works
- [ ] Minimal toolbar mode works
- [ ] Word count / character count works
- [ ] Mention (@) autocomplete works
- [ ] Spell check works (browser native)
- [ ] Content CSS from templates applies inside editor
- [ ] PHP/script tags preserved for super group pages
- [ ] Admin editor works (with XTD buttons)
- [ ] Publications curation field completion tracking works
- [ ] Courses outline tool/wiki form submission syncs content
- [ ] Groups module editing works

### Suggested Implementation Order

1. **Phase 1** — Build CKEditor 5 distribution (1-2 days)
2. **Phase 2.1** — Verify auto-grow works natively (quick win)
3. **Phase 2.5** — Port hubzeromacro (simplest plugin, validates plugin architecture) (0.5 day)
4. **Phase 3** — Rewrite PHP plugin with full config (2-3 days)
5. **Phase 2.3** — Port hubzerogrid (1-2 days)
6. **Phase 2.2** — Port hubzeroequation (2-3 days)
7. **Phase 2.4** — Port hubzerohighlight (2-3 days)
8. **Phase 4** — Update JS API references (1-2 days)
9. **Phase 5** — Update template overrides (0.5 day)
10. **Phase 6** — Fix hardcoded references (0.5 day)
11. **Phase 7** — Remove CKEditor 4, migration (0.5 day)
12. **Phase 8** — Integration testing (2-3 days)

### Risk Areas

| Risk | Mitigation |
|---|---|
| **Protected source / GHS gaps** — Hubzero-specific tags getting stripped | Extensive GHS configuration; integration tests with real content containing macros |
| **File browser integration** — CKEditor 5 has no `tools.callFunction()` | Build a `postMessage`-based bridge; make file browser editor-agnostic |
| **Super group PHP/script tags** — complex protected source patterns | Test with real super group page content; may need custom GHS rules or raw HTML handling |
| **Content migration** — existing content may have CKEditor 4 artifacts | CKEditor 5 handles most standard HTML; test with production content samples |
| **User preference migration** — users with `editor=ckeditor` in their profile | Database migration to update user params |
| **Spell check** — SCAYT is CKEditor 4 only | Browser native spell check is the default in CKEditor 5; WProofreader available commercially |
