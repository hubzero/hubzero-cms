<!--
status: imported
source: core/components/com_newsletter/admin/help/en-GB/template.phtml
imported: 2026-09-09
-->
# Newsletter Templates

A template is a reusable frame for one or all your newlsetter & email needs. Newsletter templates are built with HTML code.

Its a good idea to read some material before attempting to build your own template.

## 1 Click Unsubscribe

One click unsubscribe allows a user to unsubscribe from the mailinglist without a whole lot of hassle. Simply place the unsubscribe placeholder (see below) anywhere in your templates html code. When the newsletter is sent, that placeholder will automatically get replaced with the appropriate unsubscribe link for that user & mailing list.

```
{{UNSUBSCRIBE_LINK}}
```
