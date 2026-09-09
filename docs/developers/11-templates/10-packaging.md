<!--
status: imported
source: https://help.hubzero.org/documentation/240/webdevs/templates/packaging
source-id: 3513
imported: 2026-09-09
-->
# Packaging

## Preparation

### File Structure

The most basic files, such as `index.php`, `error.php`, `templateDetails.xml`, `template_thumbnail.png`, `favicon.ico` should be placed directly in your template folder. The most common is to place images, CSS files, JavaScript files etc in separate folders. Override files must be placed in folders in the folder "`html`".

```
/{TemplateName}
  /css
    ... CSS files ...
  /html
    ... Overrides ...
  /images
    ... Image files ...
  /js
    ... JavaScript files ...
  composer.json
  error.php
  index.php
  templateDetails.xml
  template_thumbnail.png
  favicon.ico
```

### Thumbnail Preview Image

A thumbnail preview image named `template_thumbnail` should be included in your template. Image size is 206 pixels in width and 150 pixels high. Recommended file format is PNG.

## Packaging

It is possible to install a template manually by copying the files using an SFTP client and modifying the database tables. It is more efficient to create a package file in the form on a [composer.json](https://getcomposer.org/doc/01-basic-usage.md) document that will allow the Installer to do this for you. This package file resides in the top-level of your template's directory and contains a variety of information:

- basic descriptive details about your template (i.e. name), and optionally, a description, copyright and license information.
- the extension type (component, module, plugin, template)
- optionally, a destined install directory

## Composer Manifest

This `composer.json` file just outlines basic information about the template such as the owner, version, etc. for identification by the installer and then tells the installer which files should be copied and installed.

A typical component manifest:

```javascript
{
  "name": "myorg/tpl_example",
  "description": "Example template",
  "license": "MIT",
  "type": "hubzero-template"
}
```

The hub includes some extra code that tells Composer where/how to install extensions, so it's important to use the designated `type`s. Available types are: `hubzero-component`, `hubzero-module`, `hubzero-plugin`, `hubzero-template`.

## XML Manifest (deprecated)

This XML file just lines out basic information about the template such as the owner, version, etc. for identification by the installer and then provides optional parameters which may be set in the Template Manager and accessed from within the module's logic to fine tune its behavior. Additionally, this file tells the installer which files should be copied and installed.

A typical template manifest:

```xml
<?xml version="1.0" encoding="utf-8"?>
<extension version="1.5" type="template">
	<name>mynewtemplate</name>
	<creationDate>2008-05-01</creationDate>
	<author>John Doe</author>
	<authorEmail>john@example.com</authorEmail>
	<authorUrl>http://www.example.com</authorUrl>
	<copyright>John Doe 2008</copyright>
	<license>GNU/GPL</license>
	<version>1.0.2</version>
	<description>My New Template</description>
	<files>
		<filename>index.php</filename>
		<filename>component.php</filename>
		<filename>templateDetails.xml</filename>
		<filename>template_thumbnail.png</filename>
		<filename>images/background.png</filename>
		<filename>css/style.css</filename>
	</files>
	<positions>
		<position>breadcrumb</position>
		<position>left</position>
		<position>right</position>
		<position>top</position>
		<position>user1</position>
		<position>user2</position>
		<position>user3</position>
		<position>user4</position>
		<position>footer</position>
	</positions>
</extension>
```

Let's go through some of the most important tags:

- **EXTENSION**  
  The install tag has several key attributes. The `type` must be "template".
- **NAME**  
  You can name the templates in any way you wish.
- **FILES**  
  The files tag includes all of the files that will will be installed with the template.
- **POSITIONS**  
  The module positions used in the template.

The one noticeable difference between this template manifest and the typical manifest of a module or component is the lack of `config`. While templates may have their own `params` for further configuration via the administrative back-end, they aren't as commonly found as in other extension manifests. Most HUBzero templates do not include them.
