<!--
status: generated
source: core/components/*/config/config.xml
-->
# Component options

One page per component with a `config/config.xml`, listing every parameter with its label, type, default, and description. These are the settings behind each component's **Options** button in the administrator interface.

## How to read these tables

- **Default** is the value written in the manifest, which is what a hub runs on until someone saves the screen. It is not always what the code falls back to when the setting is absent, and where the two disagree the narrative chapter for that extension says so.
- **Type** is the form field the administrator interface renders. An unrecognised type falls back to a plain text box.
- **Description** is the manifest's description string resolved through the extension's language files. A missing string leaves the raw key.
- A parameter listed here is not proof that anything reads it. Several shipped settings are stored and never used again; those are recorded in the chapter for the extension.
