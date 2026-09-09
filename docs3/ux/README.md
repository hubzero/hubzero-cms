# Hubzero UX Docs

Subject-based UX documentation for the Hubzero Blade migration.

Built around Tailwind CSS v4, daisyUI v5, and WCAG 2.2 AA targets.

## Table of Contents

### Patterns

Common page types and interaction flows.

- [Patterns Index](patterns/README.md)
- [List / Browse](patterns/list-browse.md)
- [Single Item / Detail](patterns/single-item.md)
- [Edit / Form](patterns/edit-form.md)
- [Delete Confirmation](patterns/delete-confirmation.md)
- [Dashboard](patterns/dashboard.md)
- [Multi-step Wizard](patterns/multi-step-wizard.md)

### Guides

Implementation guidance for site/admin Blade work, theming, shell behavior, and accessibility.

- [Guides Index](guides/README.md)
- [Site Blade Guide](guides/site-blade-guide.md)
- [Plugin Blade Guide](guides/plugin-blade-guide.md)
- [Admin Views](guides/admin.md)
- [Blade Page Shell](guides/blade-page-shell.md)
- [Accessibility](guides/accessibility.md)
- [Tailwind + daisyUI Theming](guides/tailwind-theming.md)
- [Client-Side Framework Detection](guides/client-side-framework-detection.md)

### Reference

Reusable API-style references for components, classes, and migration status.

- [Reference Index](reference/README.md)
- [Global Blade Components](reference/global-components.md)
- [Semantic CSS Classes](reference/semantic-classes.md)
- [Resource Plugin Data](reference/resource-plugin-data.md)
- [View Inventory](reference/view-inventory.md)

## Reading Order

For new site conversions:

1. [Site Blade Guide](guides/site-blade-guide.md)
2. [Global Blade Components](reference/global-components.md)
3. Relevant page pattern in [patterns/README.md](patterns/README.md)
4. [Accessibility](guides/accessibility.md)
5. [View Inventory](reference/view-inventory.md)

For plugin conversions:

1. [Plugin Blade Guide](guides/plugin-blade-guide.md)
2. [Client-Side Framework Detection](guides/client-side-framework-detection.md)
3. [Resource Plugin Data](reference/resource-plugin-data.md) (for resource plugins)

For admin conversions:

1. [Admin Views](guides/admin.md)
2. [Semantic CSS Classes](reference/semantic-classes.md)
3. [Tailwind + daisyUI Theming](guides/tailwind-theming.md)

## Current Notes

- Admin Blade views are CSP-compliant today.
- Site Blade views should be written to the same standard, but the site shell
  and a few early site templates still need CSP cleanup.
- For Blade links built with `Route::url()`, see [Admin Views — Text Encoding](guides/admin.md#text-encoding).
