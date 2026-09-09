<!--
status: imported
source: https://help.hubzero.org/documentation/240/webdevs/templates/structure
source-id: 3506
modified: 2015-08-24
imported: 2026-09-09
-->
# Structure

## Overview

All templates should include a manifest in the form of an XML document named `templateDetails.xml`. The file holds key "metadata" about the template and is essential. Without it, your template won't be seen by the system.

## Directory & Files

Templates are found in the `/templates` directory of a hub's `/app`. Specific template files are contained within a directory of the same name as the template. While a template may contain any number of files and sub-directories, it must contain at least two files: the primary layout (`index.php`) and a XML manifest named `templateDetails.xml`.

```
/app
.. /templates
.. .. /{TemplateName}
.. .. .. /css
.. .. .. /html
.. .. .. /img
.. .. ..  /js
.. .. .. error.php
.. .. .. component.php
.. .. .. index.php
.. .. .. templateDetails.xml
.. .. .. template_thumbnail.png
.. .. .. favicon.ico
```
