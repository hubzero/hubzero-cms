<!--
status: generated
source: Event::trigger('wiki.*') call sites and core/plugins/wiki/
-->

# Wiki events

Events in the `wiki` group. A plugin in `core/plugins/wiki/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `wiki.onAfterDisplayContent`

Fired from:

- [`core/components/com_wiki/site/controllers/pages.php:263`](../../../core/components/com_wiki/site/controllers/pages.php#L263) with `[&$this->page, &$revision, $this->config]`

No plugin in the source tree listens for this event.

## `wiki.onAfterDisplayTitle`

Fired from:

- [`core/components/com_wiki/site/controllers/pages.php:257`](../../../core/components/com_wiki/site/controllers/pages.php#L257) with `[$this->page, &$revision, $this->config]`

No plugin in the source tree listens for this event.

## `wiki.onBeforeDisplayContent`

Fired from:

- [`core/components/com_wiki/site/controllers/pages.php:260`](../../../core/components/com_wiki/site/controllers/pages.php#L260) with `[&$this->page, &$revision, $this->config]`

No plugin in the source tree listens for this event.

## `wiki.onDisplayEditor`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_wiki_editortoolbar` — [`onDisplayEditor($name, $id, $content, $cls = 'wiki-toolbar-content', $col = 10, $row = 35)`](../../../core/plugins/wiki/editortoolbar/editortoolbar.php)
- `plg_wiki_editorwykiwyg` — [`onDisplayEditor($name, $id, $content, $cls = 'wiki-toolbar-content', $col = 10, $row = 35)`](../../../core/plugins/wiki/editorwykiwyg/editorwykiwyg.php)

## `wiki.onGetWikiParser`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_wiki_parserdefault` — [`onGetWikiParser($config, $getnew = false)`](../../../core/plugins/wiki/parserdefault/parserdefault.php)
- `plg_wiki_parsermarkdown` — [`onGetWikiParser($config, $getnew = false)`](../../../core/plugins/wiki/parsermarkdown/parsermarkdown.php)

## `wiki.onInitEditor`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_wiki_editortoolbar` — [`onInitEditor()`](../../../core/plugins/wiki/editortoolbar/editortoolbar.php)
- `plg_wiki_editorwykiwyg` — [`onInitEditor()`](../../../core/plugins/wiki/editorwykiwyg/editorwykiwyg.php)

## `wiki.onWikiAfterBeforeComment`

Fired from:

- [`core/components/com_wiki/admin/controllers/comments.php:243`](../../../core/components/com_wiki/admin/controllers/comments.php#L243) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `wiki.onWikiAfterDelete`

Fired from:

- [`core/components/com_wiki/admin/controllers/pages.php:354`](../../../core/components/com_wiki/admin/controllers/pages.php#L354) with `[$id]`

No plugin in the source tree listens for this event.

## `wiki.onWikiAfterDeleteComment`

Fired from:

- [`core/components/com_wiki/admin/controllers/comments.php:308`](../../../core/components/com_wiki/admin/controllers/comments.php#L308) with `[$id]`

No plugin in the source tree listens for this event.

## `wiki.onWikiAfterDeleteVersion`

Fired from:

- [`core/components/com_wiki/admin/controllers/versions.php:349`](../../../core/components/com_wiki/admin/controllers/versions.php#L349) with `[$id]`

No plugin in the source tree listens for this event.

## `wiki.onWikiAfterSave`

Fired from:

- [`core/components/com_wiki/admin/controllers/pages.php:273`](../../../core/components/com_wiki/admin/controllers/pages.php#L273) with `[&$page, $isNew]`
- [`core/components/com_wiki/site/controllers/history.php:491`](../../../core/components/com_wiki/site/controllers/history.php#L491) with `[&$this->page, false]`
- [`core/components/com_wiki/site/controllers/pages.php:786`](../../../core/components/com_wiki/site/controllers/pages.php#L786) with `[&$this->page, $isNew]`

No plugin in the source tree listens for this event.

## `wiki.onWikiAfterSaveComment`

Fired from:

- [`core/components/com_wiki/admin/controllers/comments.php:261`](../../../core/components/com_wiki/admin/controllers/comments.php#L261) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `wiki.onWikiAfterSaveVersion`

Fired from:

- [`core/components/com_wiki/admin/controllers/versions.php:246`](../../../core/components/com_wiki/admin/controllers/versions.php#L246) with `[&$version, $isNew]`

No plugin in the source tree listens for this event.

## `wiki.onWikiBeforeSave`

Fired from:

- [`core/components/com_wiki/admin/controllers/pages.php:246`](../../../core/components/com_wiki/admin/controllers/pages.php#L246) with `[&$page, $isNew]`
- [`core/components/com_wiki/site/controllers/pages.php:672`](../../../core/components/com_wiki/site/controllers/pages.php#L672) with `[&$this->page, $isNew]`

No plugin in the source tree listens for this event.

## `wiki.onWikiBeforeSaveVersion`

Fired from:

- [`core/components/com_wiki/admin/controllers/versions.php:210`](../../../core/components/com_wiki/admin/controllers/versions.php#L210) with `[&$version, $isNew]`

No plugin in the source tree listens for this event.

## `wiki.onWikiParseText`

Fired from:

- [`core/plugins/content/formatwiki/formatwiki.php:139`](../../../core/plugins/content/formatwiki/formatwiki.php#L139) with `[$content, $params, $params['fullparse'], true]`

Listeners:

- `plg_wiki_parserdefault` — [`onWikiParseText($text, $config, $fullparse = true, $getnew = false)`](../../../core/plugins/wiki/parserdefault/parserdefault.php)
- `plg_wiki_parsermarkdown` — [`onWikiParseText($text, $config, $fullparse = true, $getnew = false)`](../../../core/plugins/wiki/parsermarkdown/parsermarkdown.php)
