<!--
status: generated
source: Event::trigger('citation.*') call sites and core/plugins/citation/
-->

# Citation events

Events in the `citation` group. A plugin in `core/plugins/citation/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `citation.onCitationAfterSave`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_citation_doi` — [`onCitationAfterSave()`](../../../core/plugins/citation/doi/doi.php)

## `citation.onImport`

Fired from:

- [`core/components/com_citations/site/controllers/import.php:168`](../../../core/components/com_citations/site/controllers/import.php#L168) with `[$file]`
- [`core/plugins/citation/default/default.php:69`](../../../core/plugins/citation/default/default.php#L69) with `array($new_file), $scope, $scope_id`
- [`core/plugins/groups/citations/citations.php:1255`](../../../core/plugins/groups/citations/citations.php#L1255) with `[$file, 'group', $this->group->get('gidNumber')]`
- [`core/plugins/members/citations/citations.php:1127`](../../../core/plugins/members/citations/citations.php#L1127) with `[$file, 'member', User::get('id')]`

Listeners:

- `plg_citation_bibtex` — [`onImport($file, $scope = null, $scope_id = null)`](../../../core/plugins/citation/bibtex/bibtex.php)
- `plg_citation_default` — [`onImport($file, $scope = null, $scope_id = null)`](../../../core/plugins/citation/default/default.php)
- `plg_citation_endnote` — [`onImport($file, $scope = null, $scope_id = null)`](../../../core/plugins/citation/endnote/endnote.php)

## `citation.onImportAcceptedFiles`

Fired from:

- [`core/components/com_citations/site/controllers/import.php:117`](../../../core/components/com_citations/site/controllers/import.php#L117) with `[]`
- [`core/plugins/groups/citations/citations.php:1182`](../../../core/plugins/groups/citations/citations.php#L1182) with `[]`
- [`core/plugins/members/citations/citations.php:1067`](../../../core/plugins/members/citations/citations.php#L1067) with `[]`

Listeners:

- `plg_citation_bibtex` — [`onImportAcceptedFiles()`](../../../core/plugins/citation/bibtex/bibtex.php)
- `plg_citation_default` — [`onImportAcceptedFiles()`](../../../core/plugins/citation/default/default.php)
- `plg_citation_endnote` — [`onImportAcceptedFiles()`](../../../core/plugins/citation/endnote/endnote.php)
