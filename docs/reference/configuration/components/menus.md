<!--
status: generated
source: core/components/com_menus/config/config.xml
-->

# Menus (com_menus)

Component for creating menus

Parameters from [`core/components/com_menus/config/config.xml`](../../../../core/components/com_menus/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Page Display Options

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `page_title` | Browser Page Title | text | — | Optional text for the &quot;Browser page title&quot; element. If blank, a default value is used based on the Menu Item Title. |
| `show_page_heading` | Show Page Heading | radio | `0 (No)` | Show / Hide the Browser Page Title in the heading of the page ( If no optional text entered - will default to value based on the Menu Item Title ). The Page heading is usually displayed inside the &quot;H1&quot; tag. Options: `0` No, `1` Yes. |
| `page_heading` | Page Heading | text | — | Optional alternative text for the Page heading. |
| `pageclass_sfx` | Page Class | text | — | Optional CSS class to add to elements in this page. This allows CSS styling specific to this page. |
