## Project Overview

Hubzero is an open source PHP platform for building scientific collaboration websites ("hubs"). It's a component-based CMS.

Current design is @hubzero-2.4-design.md

This project is converting it to a Laravel application following the plan @hubzero-3.0-design.md

## Build Commands

### Blade Template CSS (Tailwind + daisyUI)

Requires Node 20+ via nvm:

```bash
cd app/templates/hubzero
source ~/.nvm/nvm.sh && nvm use 20
npm run build:css        # one-time build (minified)
npm run dev:css          # watch mode for development
```

Source: `css/blade.src.css` → Output: `css/blade.css`

