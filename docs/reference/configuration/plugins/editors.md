<!--
status: generated
source: core/plugins/editors/*/*.xml
-->

# Editors plugins

Parameters of every plugin in the `editors` group, from each plugin's manifest. Set them under **Extensions > Plugins** in the administrator interface.

## Editor - CKEditor (`plg_editors_ckeditor`)

WYSIWYG Editor built on CKEditor

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `startupMode` | Start-up Mode | radio | `wysiwyg` | The mode to start the plugin in. Options: `wysiwyg`, `source`. |
| `sourceViewButton` | Source View | radio | `0 (Off)` | Display a source view button. Options: `0` Off, `1` On. |
| `colorButton` | Allow buttons for changing text or backgorund colors | radio | `0 (No)` | Allow colors?. Options: `0` No, `1` Yes. |
| `fontSize` | Allow option for changing font size | radio | `0 (No)` | Allow font size?. Options: `0` No, `1` Yes. |
| `autoGrowAutoStart` | Auto-grow | radio | `0 (Off)` | Auto Grow content area. Options: `0` Off, `1` On. |
| `autoGrowMinHeight` | Auto-grow Min Height | text | `5em` | Minimum height for the auto-grow |
| `autoGrowMaxHeight` | Auto-grow Max Height | text | `500em` | Maximum height for the auto-grow |
| `spellCheckAutoStart` | Spellcheck Auto Start | radio | `0 (Off)` | Spellcheck Auto Start. Options: `0` Off, `1` On. |
| `allowScriptTags` | Allow Javascript Tags | radio | `0 (No)` | Allow javascript tags?. Options: `0` No, `1` Yes. |
| `skin` | Editor Skin | list | `0` | Editor Skin. Options: `moono` Moono, `moono-lisa` Moono-Lisa, `moonocolor` Moono Color, `moono-dark` Moono Dark, `bootstrapck` BootstrapCK4, `office2013` Office 2013. |
| `filebrowserBrowseUrl` | File Browser URL | text | — | The location of an external file browser that should be launched when the Browse Server button is pressed. |
| `filebrowserImageBrowseUrl` | Image Browser URL | text | — | The location of an external file browser that should be launched when the Browse Server button is pressed in the Image dialog window. |
| `filebrowserUploadUrl` | File Upload URL | text | — | The location of the script that handles file uploads. |

## Editor - CKEditor 5 (`plg_editors_ckeditor5`)

WYSIWYG Editor built on CKEditor5

This plugin has no parameters.

## Editor - CodeMirror (`plg_editors_codemirror`)

This plugin loads the CodeMirror editor.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `linenumbers` | Line numbers | radio | `0 (Off)` | Display line numbers. Options: `0` Off, `1` On. |
| `tabmode` | Tab mode | radio | `indent (Indent)` | Indent - causes tab to adjust the indentation of the selection or current line using the parser's rules . Shift - Pressing tab indents the current line (or selection) one indent Unit  deeper, pressing shift-tab, un-indents it. Options: `indent` Indent, `shift` Shift. |

## Editor - None (`plg_editors_none`)

This loads a basic text entry field.

This plugin has no parameters.

## Editor - PageDown (`plg_editors_pagedown`)

Editor built on StackExchange/pagedown

This plugin has no parameters.

## Editor - TinyMCE (`plg_editors_tinymce`)

TinyMCE is a platform-independent Web-based JavaScript HTML WYSIWYG Editor control.

### Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `mode` | Functionality | list | `2 (Extended)` | Select Functionality. Options: `0` Simple, `1` Advanced, `2` Extended. |
| `skin` | Skin | list | `0 (Default)` | Select skin. Options: `0` Default, `1` Office2007 Blue, `2` Office2007 Silver, `3` Office2007 Black. |
| `entity_encoding` | Entity Encoding | list | `raw` | Controls how HTML entities are encoded. Recommended setting is 'raw'. 'named' = used named entity encoding (for example, '&lt;'). 'numeric' = use numeric HTML encoding (for example, '%03c'). raw = Do not encode HTML entities. Note that searching content may not work properly if setting is not 'raw'. Options: `named`, `numeric`, `raw`. |
| `lang_mode` | Automatic Language Selection | radio | `0 (No)` | If Yes, editor language will automatically match selected UI language. Do not activate if appropriate editor languages are not installed. Options: `0` No, `1` Yes. |
| `lang_code` | Language Code | text | `en` | Editor UI Language. A value here is mandatory if manual language selection is set. |
| `text_direction` | Text Direction | list | `ltr (Left to Right)` | Choose default text direction. Options: `ltr` Left to Right, `rtl` Right to Left. |
| `content_css` | Template CSS classes | radio | `1 (Yes)` | By default the Plug-in looks for an editor.css file. If it cannot find one in the default template css folder, it loads the editor.css file from the system template. Options: `0` No, `1` Yes. |
| `content_css_custom` | Custom CSS classes | text | — | Optional CSS file that will override the standard editor.css file. Enter a file name to point to a file in the CSS folder of the default template (for example, templates/beez_20/css/). Or enter a full URL path to the custom CSS file. If you enter a value in this field, this file will be used instead of the editor.css file. |
| `relative_urls` | URLs | list | `1 (Relative)` | URL behaviour. Options: `0` Absolute, `1` Relative. |
| `newlines` | New Lines | list | `0 (P Elements)` | New lines will be created using the selected option. Options: `1` BR Elements, `0` P Elements. |
| `invalid_elements` | Prohibited Elements | textarea | `script,applet,iframe` | Elements that will be cleaned from the text. Do not leave empty - if you do not want to prohibit anything enter dummy text e.g.cms |
| `extended_elements` | Extended Valid Elements | textarea | — | Allows the addition of specific valid elements to the existing rule set. |

### Advanced parameters

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `toolbar` | Toolbar | list | `top (Top)` | Position of the toolbar. Options: `top` Top, `bottom` Bottom. |
| `toolbar_align` | Toolbar align | list | `left (Left)` | Alignment of the toolbar. Options: `left` Left, `center` Center, `right` Right. |
| `html_height` | HTML Height | text | `550` | Height of HTML mode pop-up window. Only works in Extended mode. |
| `html_width` | HTML Width | text | `750` | Width of HTML mode pop-up window. Only works in Extended mode. |
| `resizing` | Resizing | radio | `true (On)` | Enable/disable the resizing button. Options: `false` Off, `true` On. |
| `resize_horizontal` | Horizontal resizing | radio | `false (Off)` | Enable/disable the horizontal resizing. Options: `false` Off, `true` On. |
| `element_path` | Element Path | radio | `1 (On)` | If set to ON, it displays the set classes for the marked text. Options: `0` Off, `1` On. |
| `fonts` | Fonts | radio | `1 (Show)` | Show/Hide the Fonts control selectors. Only applies in Extended mode. Options: `0` Hide, `1` Show. |
| `paste` | Paste | radio | `1 (Show)` | Show/Hide the Paste buttons. Only applies in Extended mode. Options: `0` Hide, `1` Show. |
| `searchreplace` | Search-Replace | radio | `1 (Show)` | Show/Hide the Search &amp; Replace button. Only works in Extended mode. Options: `0` Hide, `1` Show. |
| `insertdate` | Insert Date | radio | `1 (Show)` | Show/Hide the Insert Date button. Only works in Extended mode. Options: `0` Hide, `1` Show. |
| `format_date` | Date Format | text | `%Y-%m-%d` | Format of inserted date. Only works in Extended Mode. |
| `inserttime` | Insert Time | radio | `1 (Show)` | Show/Hide the Insert Time button. Only works in Extended mode. Options: `0` Hide, `1` Show. |
| `format_time` | Time Format | text | `%H:%M:%S` | Format of inserted time. Only works in Extended Mode |
| `colors` | Colours | radio | `1 (Show)` | Show/Hide the Colours control buttons. Only applies in Extended mode. Options: `0` Hide, `1` Show. |
| `table` | Table | radio | `1 (Show)` | Show/Hide the table control buttons. Only works in Extended mode. Options: `0` Hide, `1` Show. |
| `smilies` | Smilies | radio | `1 (Show)` | Show/Hide the smilies buttons. Only works in Extended mode. Options: `0` Hide, `1` Show. |
| `media` | Media | radio | `1 (Show)` | Show/Hide the Media button. Only applies in Extended mode. Options: `0` Hide, `1` Show. |
| `hr` | Horizontal Rule | radio | `1 (Show)` | Show/Hide the Horizontal Rule button. Options: `0` Hide, `1` Show. |
| `directionality` | Directionality | radio | `1 (Show)` | Select whether to display the RTL button. Only Works in Extended mode. Options: `0` Hide, `1` Show. |
| `fullscreen` | Fullscreen | radio | `1 (Show)` | Show/Hide the Fullscreen button. Only works in Extended mode. Options: `0` Hide, `1` Show. |
| `style` | Style | radio | `1 (Show)` | Show/Hide the CSS Style control button. Only works in Extended mode. Options: `0` Hide, `1` Show. |
| `layer` | Layer | radio | `1 (Show)` | Show/Hide the Add new Layer button. Only works in Extended mode. Options: `0` Hide, `1` Show. |
| `xhtmlxtras` | XHTMLxtras | radio | `1 (Show)` | Show/Hide the additional XHTML features. Only works in Extended mode. Options: `0` Hide, `1` Show. |
| `visualchars` | Visualchars | radio | `1 (Show)` | Possibility to see invisible characters, specifically non-breaking spaces. Options: `0` Hide, `1` Show. |
| `visualblocks` | Visualblocks | radio | `1 (Show)` | Possibility to see the outline of HTML block elements. Options: `0` Hide, `1` Show. |
| `nonbreaking` | Nonbreaking | radio | `1 (Show)` | Insert nonbreaking space entities. Options: `0` Hide, `1` Show. |
| `template` | Template | radio | `1 (Show)` | Show/Hide the Insert predefined template content button. Only applies in Extended mode. Options: `0` Hide, `1` Show. |
| `blockquote` | Blockquote | radio | `1 (On)` | Turn on/off Blockquotes. Options: `0` Off, `1` On. |
| `wordcount` | Wordcount | radio | `1 (On)` | Turn on/off Wordcount. Options: `0` Off, `1` On. |
| `advimage` | Advanced image | radio | `1 (On)` | Turn on/off a more advanced image dialog. Options: `0` Off, `1` On. |
| `advlink` | Advanced link | radio | `1 (On)` | Turn on/off a more advanced link dialog. Options: `0` Off, `1` On. |
| `advlist` | Advanced List | radio | `1 (On)` | Turn on/off to enable to set number formats and bullet types in ordered and unordered lists. Options: `0` Off, `1` On. |
| `autosave` | Save Warning | radio | `1 (On)` | Save Warning: gives warning if you cancel without saving changes. Options: `0` Off, `1` On. |
| `contextmenu` | Context menu | radio | `1 (On)` | Turn on/off Context menu. Options: `0` Off, `1` On. |
| `inlinepopups` | Inline popups | radio | `1 (On)` | All dialogs to open as floating DIV layers instead of popup windows. This option can be very useful in order to get around popup blockers. Options: `0` Off, `1` On. |
| `custom_plugin` | Custom plugin | text | — | Add custom plugin(s) |
| `custom_button` | Custom button | text | — | Add custom button(s) |

## Editor - Wikietoolbar (`plg_editors_wikitoolbar`)

PLG_EDITORS_WIKITOOLBAR_XML_DESCRIPTION

This plugin has no parameters.

## Editor - Wikiwyg (`plg_editors_wikiwyg`)

PLG_EDITORS_WIKIWYG_XML_DESCRIPTION

This plugin has no parameters.
