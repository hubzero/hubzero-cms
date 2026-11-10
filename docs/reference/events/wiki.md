<!--
status: generated
source: Event::trigger('wiki.*') call sites and core/plugins/wiki/
-->

# Wiki events

Events in the `wiki` group. A plugin in `core/plugins/wiki/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `wiki.onAfterDisplayContent`

Fired from:

- [`core/components/com_wiki/site/controllers/pages.php:251`](../../../core/components/com_wiki/site/controllers/pages.php#L251) with `[&$this->page, &$revision, $this->config]`

No plugin in the source tree listens for this event.

## `wiki.onAfterDisplayTitle`

Fired from:

- [`core/components/com_wiki/site/controllers/pages.php:245`](../../../core/components/com_wiki/site/controllers/pages.php#L245) with `[$this->page, &$revision, $this->config]`

No plugin in the source tree listens for this event.

## `wiki.onBeforeDisplayContent`

Fired from:

- [`core/components/com_wiki/site/controllers/pages.php:248`](../../../core/components/com_wiki/site/controllers/pages.php#L248) with `[&$this->page, &$revision, $this->config]`

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

- [`core/components/com_wiki/admin/controllers/comments.php:241`](../../../core/components/com_wiki/admin/controllers/comments.php#L241) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `wiki.onWikiAfterDelete`

Fired from:

- [`core/components/com_wiki/admin/controllers/pages.php:345`](../../../core/components/com_wiki/admin/controllers/pages.php#L345) with `[$id]`

No plugin in the source tree listens for this event.

## `wiki.onWikiAfterDeleteComment`

Fired from:

- [`core/components/com_wiki/admin/controllers/comments.php:301`](../../../core/components/com_wiki/admin/controllers/comments.php#L301) with `[$id]`

No plugin in the source tree listens for this event.

## `wiki.onWikiAfterDeleteVersion`

Fired from:

- [`core/components/com_wiki/admin/controllers/versions.php:339`](../../../core/components/com_wiki/admin/controllers/versions.php#L339) with `[$id]`

No plugin in the source tree listens for this event.

## `wiki.onWikiAfterSave`

Fired from:

- [`core/components/com_wiki/admin/controllers/pages.php:264`](../../../core/components/com_wiki/admin/controllers/pages.php#L264) with `[&$page, $isNew]`
- [`core/components/com_wiki/site/controllers/history.php:453`](../../../core/components/com_wiki/site/controllers/history.php#L453) with `[&$this->page, false]`
- [`core/components/com_wiki/site/controllers/pages.php:748`](../../../core/components/com_wiki/site/controllers/pages.php#L748) with `[&$this->page, $isNew]`

No plugin in the source tree listens for this event.

## `wiki.onWikiAfterSaveComment`

Fired from:

- [`core/components/com_wiki/admin/controllers/comments.php:255`](../../../core/components/com_wiki/admin/controllers/comments.php#L255) with `[&$row, $isNew]`

No plugin in the source tree listens for this event.

## `wiki.onWikiAfterSaveVersion`

Fired from:

- [`core/components/com_wiki/admin/controllers/versions.php:239`](../../../core/components/com_wiki/admin/controllers/versions.php#L239) with `[&$version, $isNew]`

No plugin in the source tree listens for this event.

## `wiki.onWikiBeforeSave`

Fired from:

- [`core/components/com_wiki/admin/controllers/pages.php:243`](../../../core/components/com_wiki/admin/controllers/pages.php#L243) with `[&$page, $isNew]`
- [`core/components/com_wiki/site/controllers/pages.php:643`](../../../core/components/com_wiki/site/controllers/pages.php#L643) with `[&$this->page, $isNew]`

No plugin in the source tree listens for this event.

## `wiki.onWikiBeforeSaveVersion`

Fired from:

- [`core/components/com_wiki/admin/controllers/versions.php:208`](../../../core/components/com_wiki/admin/controllers/versions.php#L208) with `[&$version, $isNew]`

No plugin in the source tree listens for this event.

## `wiki.onWikiParseText`

Fired from:

- [`core/plugins/content/formatwiki/formatwiki.php:135`](../../../core/plugins/content/formatwiki/formatwiki.php#L135) with `[$content, $params, $params['fullparse'], true]`

Listeners:

- `plg_wiki_parserdefault` — [`onWikiParseText($text, $config, $fullparse = true, $getnew = false)`](../../../core/plugins/wiki/parserdefault/parserdefault.php)
- `plg_wiki_parsermarkdown` — [`onWikiParseText($text, $config, $fullparse = true, $getnew = false)`](../../../core/plugins/wiki/parsermarkdown/parsermarkdown.php)
