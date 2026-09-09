<!--
status: generated
source: Event::trigger('editors.*') call sites and core/plugins/editors/
-->

# Editors events

Events in the `editors` group. A plugin in `core/plugins/editors/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `editors.onDisplay`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_editors_ckeditor` — [`onDisplay($name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null, $params = array()`](../../../core/plugins/editors/ckeditor/ckeditor.php)
- `plg_editors_ckeditor5` — [`onDisplay($name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null, $params = array()`](../../../core/plugins/editors/ckeditor5/ckeditor5.php)
- `plg_editors_codemirror` — [`onDisplay($name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null, $params = array()`](../../../core/plugins/editors/codemirror/codemirror.php)
- `plg_editors_none` — [`onDisplay($name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null, $params = array()`](../../../core/plugins/editors/none/none.php)
- `plg_editors_pagedown` — [`onDisplay($name, $content, $width, $height, $columns, $rows, $buttons = true, $id = null, $asset = null, $author = null, $params = array()`](../../../core/plugins/editors/pagedown/pagedown.php)
- `plg_editors_tinymce` — [`onDisplay($name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null)`](../../../core/plugins/editors/tinymce/tinymce.php)
- `plg_editors_wikitoolbar` — [`onDisplay($name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null, $params = array()`](../../../core/plugins/editors/wikitoolbar/wikitoolbar.php)
- `plg_editors_wikiwyg` — [`onDisplay($name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null, $params = array()`](../../../core/plugins/editors/wikiwyg/wikiwyg.php)

## `editors.onGetContent`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_editors_ckeditor` — [`onGetContent($id)`](../../../core/plugins/editors/ckeditor/ckeditor.php)
- `plg_editors_ckeditor5` — [`onGetContent($id)`](../../../core/plugins/editors/ckeditor5/ckeditor5.php)
- `plg_editors_codemirror` — [`onGetContent($id)`](../../../core/plugins/editors/codemirror/codemirror.php)
- `plg_editors_none` — [`onGetContent($id)`](../../../core/plugins/editors/none/none.php)
- `plg_editors_pagedown` — [`onGetContent()`](../../../core/plugins/editors/pagedown/pagedown.php)
- `plg_editors_tinymce` — [`onGetContent($editor)`](../../../core/plugins/editors/tinymce/tinymce.php)

## `editors.onGetInsertMethod`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_editors_ckeditor` — [`onGetInsertMethod($id)`](../../../core/plugins/editors/ckeditor/ckeditor.php)
- `plg_editors_ckeditor5` — [`onGetInsertMethod($id)`](../../../core/plugins/editors/ckeditor5/ckeditor5.php)
- `plg_editors_codemirror` — [`onGetInsertMethod()`](../../../core/plugins/editors/codemirror/codemirror.php)
- `plg_editors_none` — [`onGetInsertMethod($id)`](../../../core/plugins/editors/none/none.php)
- `plg_editors_tinymce` — [`onGetInsertMethod($name)`](../../../core/plugins/editors/tinymce/tinymce.php)

## `editors.onInit`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_editors_ckeditor` — [`onInit()`](../../../core/plugins/editors/ckeditor/ckeditor.php)
- `plg_editors_ckeditor5` — [`onInit()`](../../../core/plugins/editors/ckeditor5/ckeditor5.php)
- `plg_editors_codemirror` — [`onInit()`](../../../core/plugins/editors/codemirror/codemirror.php)
- `plg_editors_none` — [`onInit()`](../../../core/plugins/editors/none/none.php)
- `plg_editors_pagedown` — [`onInit()`](../../../core/plugins/editors/pagedown/pagedown.php)
- `plg_editors_tinymce` — [`onInit()`](../../../core/plugins/editors/tinymce/tinymce.php)
- `plg_editors_wikitoolbar` — [`onInit()`](../../../core/plugins/editors/wikitoolbar/wikitoolbar.php)
- `plg_editors_wikiwyg` — [`onInit()`](../../../core/plugins/editors/wikiwyg/wikiwyg.php)

## `editors.onSave`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_editors_ckeditor` — [`onSave()`](../../../core/plugins/editors/ckeditor/ckeditor.php)
- `plg_editors_ckeditor5` — [`onSave()`](../../../core/plugins/editors/ckeditor5/ckeditor5.php)
- `plg_editors_codemirror` — [`onSave($id)`](../../../core/plugins/editors/codemirror/codemirror.php)
- `plg_editors_none` — [`onSave()`](../../../core/plugins/editors/none/none.php)
- `plg_editors_pagedown` — [`onSave()`](../../../core/plugins/editors/pagedown/pagedown.php)
- `plg_editors_tinymce` — [`onSave($editor)`](../../../core/plugins/editors/tinymce/tinymce.php)

## `editors.onSetContent`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_editors_ckeditor` — [`onSetContent($id, $html)`](../../../core/plugins/editors/ckeditor/ckeditor.php)
- `plg_editors_ckeditor5` — [`onSetContent($id, $html)`](../../../core/plugins/editors/ckeditor5/ckeditor5.php)
- `plg_editors_codemirror` — [`onSetContent($id, $content)`](../../../core/plugins/editors/codemirror/codemirror.php)
- `plg_editors_none` — [`onSetContent($id, $html)`](../../../core/plugins/editors/none/none.php)
- `plg_editors_pagedown` — [`onSetContent()`](../../../core/plugins/editors/pagedown/pagedown.php)
- `plg_editors_tinymce` — [`onSetContent($editor, $html)`](../../../core/plugins/editors/tinymce/tinymce.php)
