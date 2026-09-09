# Auth Shell

Centered card layout for authentication and identity flows. Used instead of
`<x-page-container>` when a full-page chrome-less shell is appropriate.

## When to use

Use the auth-shell pattern for **com_login** and the **com_users login/logout
sub-templates** where the user is not yet authenticated or is completing a
sign-out. These pages intentionally skip the standard page header, sidebar,
and tab navigation.

**com_users is a hybrid.** Only its login form (`default_login.blade.php`) and
logout confirmation (`default_logout.blade.php`) use the auth-shell
`login-wrapper` / `login-card` pattern. The remaining com_users views — MFA
challenge, consent gate, account linking, and SSO logout — render inside
`<x-page-container>` because those flows occur mid-session (the user has
already authenticated and is completing an identity transition within the
normal page chrome).

**Not all credential forms use this pattern.** com_oauth's authorization page
collects credentials *and* consent but renders inside `<x-page-container>`
because it is an in-session action (the user may already be authenticated and
is granting access to a third-party app), not a gateway-to-session flow.

## Structure

```blade
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-icon">
            {{-- SVG icon --}}
        </div>
        <h2 class="login-heading">{{ $heading }}</h2>

        {{-- page-specific content --}}
    </div>
</div>
```

### CSS classes (defined in `site.src.css`)

| Class | Purpose |
|-------|---------|
| `login-wrapper` | Full-viewport flex centering container |
| `login-card` | Centered card with max-width, padding, rounded corners |
| `login-icon` | Centered icon area above heading |
| `login-heading` | Page title (e.g., "Sign in", "Identity validation") |
| `login-providers` | Grid layout for authentication provider buttons |
| `login-divider` | Horizontal rule with centered "or" text |
| `login-form` | Stacked form layout for username/password fields |
| `login-footer` | Bottom links (forgot password, help) |
| `login-register` | "Create account" call-to-action below the card |

## Variants

### Login page (com_login, com_users login sub-template)

Full login form with optional provider buttons above the local credential
form:

```blade
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-icon">{{-- lock icon --}}</div>
        <h2 class="login-heading">Sign in to {{ $siteName }}</h2>

        @if(count($providers))
            <div class="login-providers">
                @foreach($providers as $provider)
                    {!! $provider->html !!}
                @endforeach
            </div>
            <div class="login-divider">
                <span>or sign in with your local account</span>
            </div>
        @endif

        <form class="login-form" method="post" action="{{ $formAction }}">
            {{-- username, password, remember-me, submit --}}
        </form>

        <div class="login-footer">
            <a href="{{ $forgotUrl }}">Forgot password?</a>
        </div>
    </div>
    <p class="login-register">
        Don't have an account? <a href="{{ $registerUrl }}">Create one</a>
    </p>
</div>
```

Both `com_login` and `com_users` render login views using these same CSS
classes for visual consistency.

### MFA challenge (com_login factors)

Renders plugin-provided MFA form HTML inside the shell:

```blade
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-icon">{{-- shield icon --}}</div>
        <h2 class="login-heading">Identity validation</h2>
        {!! $factor->html !!}
    </div>
</div>
```

### Consent gate (com_login userconsent)

System usage agreement with Cancel/Agree buttons:

```blade
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-icon">{{-- document icon --}}</div>
        <h2 class="login-heading">Terms of Use</h2>
        <p>{{ $consentMessage }}</p>
        <form method="post">
            <div class="flex gap-2 justify-center">
                <a class="btn btn-ghost" href="{{ $cancelUrl }}">Cancel</a>
                <button class="btn btn-primary" type="submit">Agree</button>
            </div>
        </form>
    </div>
</div>
```

### Account linking (com_login link)

Multi-step wizard within the auth shell. The current step is selected
server-side via `Request::getInt('step', 1)`, and each step block is
conditionally shown using `hidden` in the Blade template:

- Step 1: "Have you logged in before?" — Yes/No
- Step 2: "Link existing account or create new?" — Link/Create
- Step 3: "Confirm and sign out to re-authenticate" — Confirm/Go back

### SSO logout (com_login endsinglesignon)

Two-button choice between ending all sessions or leaving other sessions
untouched.

### com_users — page-container views (NOT auth-shell)

com_users' MFA challenge (`factors`), consent gate (`userconsent`), account
linking wizard (`link`), and SSO logout (`endsinglesignon`) all render inside
`<x-page-container>`, not the auth shell. These views use centered
`max-w-lg mx-auto` cards within the standard page chrome because they occur
mid-session — the user has already authenticated and is completing an identity
transition step. This is intentionally different from com_login, which uses
the auth shell for the same flows because the admin login has no page chrome
at all.

## Why not `<x-page-container>`

`<x-page-container>` renders a page header bar, optional sidebar, and tab
navigation — all of which assume the user has an authenticated session and is
browsing normal site content. Auth-shell pages are the gateway *to* that
session, so the standard page chrome is inappropriate:

- No navigation — the user can't go elsewhere until they complete the flow
- No sidebar — no contextual widgets apply
- Centered focus — draws attention to the single required action

The `login-*` CSS classes in `site.src.css` provide the styling. No shared
Blade component wraps this pattern because the inner content varies
significantly across views (forms, plugin HTML, multi-step wizards).
