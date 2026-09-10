<!--
status: generated
source: core/plugins/*/*/*.xml
-->
# Plugin parameters

One page per plugin group, listing every plugin in the group and its parameters. These are the settings behind each plugin's row under **Extensions** > **Plug-in Manager**.

## How to read these tables

- **Default** is the value written in the manifest, which is what a hub runs on until someone saves the screen. It is not always what the code falls back to when the setting is absent, and where the two disagree the narrative chapter for that extension says so.
- **Type** is the form field the administrator interface renders. An unrecognised type falls back to a plain text box.
- **Description** is the manifest's description string resolved through the extension's language files. A missing string leaves the raw key.
- A parameter listed here is not proof that anything reads it. Several shipped settings are stored and never used again; those are recorded in the chapter for the extension.
