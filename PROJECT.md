# PROJECT.md — PSRsearch (pulsars.nanograv.org)

This repository is a customized [HUBzero](https://hubzero.org) CMS instance that powers
**PSRsearch (Pulsar Candidate Search)** at `pulsars.nanograv.org` — the web platform where
students, teachers, and scientists analyze radio-astronomy data from the Green Bank Telescope
to discover new pulsars. (The program is run by the **Pulsar Science Collaboratory (PSC)** —
note "Science", not "Search". Do not describe it as "citizen science".)

- **Live FQDN:** `pulsars.nanograv.org` (`app/config/app.php`: `sitecode = psc`, `sitename = PSC`)
- **Base platform:** HUBzero CMS (Joomla-derived), tracked from `git@github.com:hubzero/hubzero-cms.git`
- **Current branch:** `2.4-main`
- **The custom application is `com_psrsearch`** ("Pulsar Search"), in `app/components/com_psrsearch`.

---

## 1. Repository layout and the two-tier customization model

HUBzero separates the **upstream platform** from **site-specific code** using two top-level trees:

| Tree | Tracked by | Contents |
|------|-----------|----------|
| `core/` | The main `hubzero-cms` git repo (this checkout, branch `2.4-main`) | Upstream HUBzero platform + a handful of in-tree patches/plugins made for this hub |
| `app/` | **Git-ignored by the main repo** (see `.gitignore`: `app/**`) | All site-specific code: the `com_psrsearch` component, custom template, custom plugins, and runtime config |

This split matters a lot:

- **Changes under `core/`** are commits/working changes on top of upstream HUBzero. They show up in
  `git status`/`git log` of this repo. They are platform patches that must be re-applied or carried
  forward across HUBzero upgrades.
- **Changes under `app/`** are invisible to the main repo's git. Several `app/` subtrees are their
  **own independent git repositories** (nested `.git/` dirs):
  - `app/components/com_psrsearch/.git`
  - `app/plugins/user/psc/.git`
  - `app/plugins/cron/psrsearch/.git`

  When working in those directories, use *their* git, not the top-level one.

`configuration.php` (repo root) and `app/config/*.php` hold runtime settings **including live
secrets** (DB passwords, Solr credentials, mail). Do not commit or paste these. `configuration.php`
and `config/` are git-ignored.

> Note: `app/components/_com_psrsearch2/` is a stale, near-identical backup copy of the component
> (same `psrsearch.xml`, plus an extra `d/` dir). The live component is `com_psrsearch`.

---

## 2. Customizations to HUBzero core (`core/`)

### 2.1 Site-specific plugins added in-tree

These are bespoke user plugins, recently added (see `git log`), implementing **export-control /
geographic access restriction** — driven by NANOGrav data-handling requirements. They run on every
login and adjust HUBzero group membership based on the user's IP geolocation (via the
`Hubzero\Geocode` IP→country database).

- **`core/plugins/user/d1/d1.php`** (`plg_user_d1`) — On login, looks up the IP's *country group*.
  If it resolves to `D1` (a restricted-nations group), the user is added to the hidden, closed
  HUBzero group **`d1_nation`**. Membership in the **`d1_override`** group (formerly `d1_approved`)
  bypasses the check and removes the user from `d1_nation`. Removes membership on user delete.
- **`core/plugins/user/us/us.php`** (`plg_user_us`) — On login, if the IP is a US address the user is
  added to the **`location_us`** group; otherwise removed. Users in `d1_nation` are never placed in
  `location_us`. Comment in the source notes a `purdue` group supersedes `location_us` via UNIX ACLs
  for US-restricted resources.
- **`core/plugins/user/geo/`** — supporting geocode user plugin.
- Group provisioning is done by the plugins' own migrations
  (`Migration20260421000000PlgUserD1.php`, `...PlgUserUs.php`), which `INSERT IGNORE` the
  `d1_nation` / `location_us` / `d1_override` groups into `#__xgroups` as hidden (discoverability=1),
  closed (join_policy=3) groups.

### 2.2 Modified upstream core files (current uncommitted working changes)

`git status` shows these tracked `core/` files modified in the working tree. They fall into two
buckets:

**Behavior changes (intentional, site-affecting):**
- `core/libraries/Hubzero/Api/RateLimit/RateLimitService.php` — an early `return($response);` that
  **disables API rate limiting** entirely.
- `core/plugins/system/incomplete/incomplete.php` — guard changed to `if (false && …)`, which
  **disables the "link account / complete registration" redirect** for incomplete profiles.
- `core/plugins/authentication/orcid/orcid.php` — extensive `\Log::debug()` instrumentation plus a
  real behavior change: ORCID auth now only auto-creates a Hubzero auth link when
  `com_members` `allowUserRegistration` is on (`find_or_create` vs `find`), and the old
  `-<id>@invalid` placeholder username/email assignment is commented out so the suggested-username
  flow is used instead.

**PHP 8 null-safety / compatibility fixes:**
- `core/libraries/Hubzero/Form/Fields/Calendar.php` — null guard around `strtoupper($this->value)`.
- `core/libraries/Hubzero/Utility/Uri.php` — initialize `$theURI` and require `HTTP_HOST` before
  building the URI (avoids notices in CLI/cron contexts).
- `core/components/com_members/models/registration.php` — `Request::getString(..., '')` defaults
  instead of `null`.
- `core/components/com_courses/models/section.php` — date-window availability tweaks (treat
  `0000-00-00` end date as not-ended; commented-out start-date gate).
- `core/plugins/courses/{outline,reviews}/.../default.php` — minor null/whitespace fixes.

### 2.3 In-tree platform hardening (recent committed history)

Recent commits on `2.4-main` are mostly defensive fixes against real production issues (PHP 8
notices, OOM, file handling, security), e.g.:
- `publications/browse` per-page limit capped at 100 to stop OOM; batch author lookups.
- `projects/files` oversized-upload guard; configured virus scanner used in `repo::_addFromExtracted`.
- `Filesystem\Adapter\Local::isSafe` fails closed; `Database\Driver\Pdo::connected()` returns false
  instead of throwing.

---

## 3. The `app/` overlay (site-specific application)

```
app/
├── components/
│   ├── com_psrsearch/      # THE pulsar-search application (own git repo) — see §4
│   └── _com_psrsearch2/    # stale backup copy; ignore
├── plugins/
│   ├── user/psc/           # PSC user plugin: SSO cookies + name sync (own git repo)
│   └── cron/psrsearch/     # PSC cron jobs: rollups + plot staging (own git repo)
├── templates/
│   ├── pulsar/             # active custom site template ("Pulsar")
│   └── mytemplate/         # scaffold/leftover
├── config/                 # runtime config (app.php, psrsearch.php, solr.*, db, mail …) — SECRETS
├── site/                   # per-component runtime data (courses, forum, groups, members, …)
├── cache/  tmp/  logs/     # runtime dirs
```

### 3.1 `app/plugins/user/psc` (`plg_user_psc`) — SSO + profile sync

Runs on user save/login/logout. Two jobs:

1. **Name sync** — keeps `psrsearch_members.firstname/lastname` in sync with the HUBzero user's
   `givenName`/`surname` (via `Usermap` → `Members::change_name`).
2. **Cross-app SSO cookies** — on login, mints two HMAC-signed cookies scoped to
   `.pulsars.nanograv.org`, cleared on logout:
   - **`session`** — authenticates the user to the separate **certification app**
     (payload: username, member_id, `basic_is_logged_in`).
   - **`psc-jhub-session`** — authenticates to **JupyterLab/JupyterHub**.

   The signing scheme is a gzip+base64url payload with an HMAC-SHA1 signature derived from a salt
   and a shared secret (the secret is externalized; the source shows an `"xxx"` placeholder). This
   ties the hub's login state to the JupyterHub and certification subsystems.

### 3.2 `app/plugins/cron/psrsearch` (`plg_cron_psrsearch`) — background jobs

Registers two HUBzero cron events:
- **`psrsearchRollups`** — recomputes leaderboard/stat aggregates:
  `ViewedPlots::rollupViews/rollupYears/rollupMonths/rollupWeeks/rollupTeamTotals/rollupSurveyTotals`.
- **`psrsearchStageUnfinishedPlots`** — for every survey, calls
  `Plots::stage_unfinished_plots($survey_id, 20000)` to pre-fill the work queue of plots awaiting
  grading. **The classification workflow depends on this cron running.**

### 3.3 `app/templates/pulsar` — custom site theme

The active front-end template ("Pulsar"), with its own `component.php`, `home.php`, `error.php`,
LESS/CSS/JS, language files, and migrations. It includes a `psrsearch.php` template entry,
indicating the component's grading UI is themed here.

### 3.4 `app/config`

- `app.php` — core hub config (`fqdn=pulsars.nanograv.org`, `application_env=production`,
  `force_ssl=1`, `editor=ckeditor`, `offset=America/New_York`, `captcha=image`, log/tmp paths).
- `psrsearch.php` — **second database** connection for the component: DB `psrsearch` on `localhost`,
  table prefix `psrsearch_`, MariaDB, plus the `psrsearch@pulsars.nanograv.org` mail-from.
- `solr.json` / `solr.php` — Solr search backend credentials/host.
- Standard hub config: `database.php`, `mail.php`, `session.php`, `rate_limit.php`, `seo.php`, etc.

> **The component uses its own MariaDB database** (`psrsearch`), separate from the main HUBzero
> database. See `app/components/com_psrsearch/models/Database.php`.

---

## 4. `com_psrsearch` — the Pulsar Search application

A full HUBzero component implementing distributed, consensus-based classification of pulsar
candidate plots. Manifest: `app/components/com_psrsearch/psrsearch.xml` ("Pulsar Search").

### 4.1 Domain

Users examine **diagnostic plots** produced by pulsar-search pipelines and classify each one:
- **prepfold** plots (PRESTO folded-candidate PostScript, served gzipped), and
- **singlepulse** plots (single-pulse search results, served as JSON).

For each plot a grader scores four sub-panels on a 0–10 scale — **profile, subints, subbands, DM
(dispersion measure)** — and assigns a verdict: **new_pulsar / known / maybe / rfi / noise**.
Multiple independent gradings per plot are combined into a **consensus** ("finished") result.

Participation is organized as **schools → teams → members**, with **surveys** grouping the plots to
be worked, and a **certification/training** system that gates experience levels.

### 4.2 Architecture

Standard HUBzero component layout under `app/components/com_psrsearch/`:

```
site/        # front-end controllers (the grader UI and dashboards)
admin/       # backend administration
api/         # versioned REST API (v1.0)
models/      # data-access layer (mostly static query methods, prepared statements, transactions)
helpers/     # Log, Mail, Html, PsrsearchSession
config/      # access.xml, config.xml
migrations/  # Migration20211001213638ComPsrsearch.php
my_schema.sql  # full DDL (~27 tables)
```

Models are thin static query wrappers over the dedicated `psrsearch` database rather than a full ORM.

### 4.3 Data model (database `psrsearch`, prefix `psrsearch_`)

Core entities (tables from `my_schema.sql`):

**Plots & grading**
- `psrsearch_plots` — master plot catalog: `plot_id`, `survey_id`, file `directory`/`filename`,
  physical params (period, DM, chi², MJD, RA/Dec), `telescope`, `candidate`, and `data_type`
  (lifecycle: unprocessed → ungraded → graded).
- `psrsearch_viewed_plots` — **one row per individual grading**: scores (profile/subints/subbands/DM),
  verdict booleans (noise/rfi/maybe/known/new_pulsar), `weight` (default 1.0), member/team/survey ids.
- `psrsearch_finished_plots` — **consensus aggregate** per plot, recomputed from `viewed_plots`
  (averaged sub-scores, a single consensus verdict, count and weighted count).
- `psrsearch_unfinished_plots` — work-queue state per plot (queued/assigned/skipped/graded).
- `psrsearch_plots_tranche` — per-member, per-survey **batch** of plot_ids (JSON) with a cursor;
  prevents two graders racing on the same plot. OOP wrapper: `models/PlotsTranche.php`.

**Surveys / teams / schools / members**
- `psrsearch_surveys` (+ `_members`, `_teams`, `_total_rollup`) — survey definitions and grading
  policy (`grade_nmembers`, `grade_nteams`, weighting, certification requirement, experience tiers,
  join policy, discoverability).
- `psrsearch_teams` (+ `_members`, `_total/_weekly/_monthly/_yearly_rollup`) — teams and leaderboard
  rollups.
- `psrsearch_schools` — school directory.
- `psrsearch_members` (+ `_roles`) — participant profiles; member types admin/astronomer/student/
  teacher/mentor; global roles (e.g. admin, `psc-certified`).
- `psrsearch_usermap` — maps HUBzero `#__users.id` ↔ `psrsearch_members.member_id`.

**Workflow support**
- `psrsearch_requests` — pending access requests (category role/team/survey; accept/reject/cancel).
- `psrsearch_certifications`, `_certification_plot`, `_certification_sets`,
  `_current_certification_set`, `_completed_certifications` — training/certification (pass ≥ 80).
- `psrsearch_event_log` — login/logout audit.
- (`new_map`, `psrsearch_remap_plots` — data migration/remapping helpers.)

Relationship sketch:

```
School ─< Team ─< Member ─< (grades) >─ Plot >─ Survey
                     │                    │
              ViewedPlots (raw votes) → FinishedPlots (consensus)
              UnfinishedPlots / PlotsTranche (work distribution)
```

### 4.4 Classification workflow

1. **Staging (cron):** `plg_cron_psrsearch::psrsearchStageUnfinishedPlots` fills
   `psrsearch_unfinished_plots` for each survey.
2. **Assignment:** `Plots::next_psc_plot_id($survey_id, $member_id, $team_id)` selects a plot the
   member hasn't graded, whose weighted view count is below the survey's `grade_nmembers`/`grade_nteams`
   thresholds. Plots are handed out in **tranches** (`PlotsTranche`) so a member can page next/prev.
3. **Render:** the `site/controllers/plots.php` actions serve the prepfold PostScript (gzipped) or
   singlepulse JSON and render the scoring UI.
4. **Submit:** `Plots::grade_plot($plot_id, $member_id, $grade)` runs transactionally — inserts the
   row into `viewed_plots`, calls `FinishedPlots::finished_plots_viewed_plots_sync()` to recompute
   consensus, and updates `unfinished_plots`/`plots.data_type` once enough gradings exist.
5. **Consensus rule** (`models/FinishedPlots.php`): verdict priority is
   **new_pulsar > known > maybe (when maybe ≥ rfi+noise) > rfi > noise**; sub-scores are averaged.
6. **Rollups (cron):** `ViewedPlots::rollup*` recompute team/survey/time-window leaderboards.

### 4.5 REST API (`api/`, version `v1_0`)

Controllers under `api/controllers/` (routed by `api/router.php`), serving the grader UI / external
clients. Notable ones:
- `plotsv1_0` — next/previous plot id & data, grade, skip.
- `plotdatav1_0` — raw plot file bytes (PostScript/JSON).
- `datatablev1_0` — server-side paginated/sorted tables (DataTables backend).
- `viewedplotsv1_0` — grading stats (e.g. totals by month, filtered by team/member/date).
- `surveysv1_0`, `teamsv1_0`, `schoolsv1_0`, `membersv1_0` — entity lookups & stats.
- `pssv1_0`, `gdmv1_0` — specialized endpoints.

Auth resolves the caller to a `psrsearch_members.member_id` via `Usermap`; unauthorized → 401,
unpermitted → 403.

### 4.6 External integrations

- **JupyterHub/JupyterLab** and a **certification app** — via the `psc-jhub-session` / `session`
  SSO cookies minted by `plg_user_psc` (§3.1), shared across `.pulsars.nanograv.org`.
- **Telescope data pipelines** — plot files (PRESTO prepfold PostScript, singlepulse JSON) live on
  disk under the directory/filename recorded in `psrsearch_plots`; `telescope`/`candidate` fields tie
  rows to upstream survey data.
- **Solr** — hub-wide search backend (`app/config/solr.*`).
- **ORCID** — authentication plugin (with the site patches noted in §2.2).

---

## 5. Operational notes & gotchas

- **Two databases:** the main HUBzero DB *and* the separate `psrsearch` DB (`app/config/psrsearch.php`,
  `models/Database.php`). Component queries hit the latter.
- **The grader needs the cron running** — without `psrsearchStageUnfinishedPlots`, the work queue
  empties and graders run out of plots; without `psrsearchRollups`, leaderboards go stale.
- **API rate limiting is currently disabled** in the working tree (`RateLimitService.php`); be
  deliberate before committing/reverting that.
- **Geo/export-control groups** (`d1_nation`, `location_us`, `d1_override`) are maintained *only at
  login* by the `d1`/`us` user plugins and depend on the `Hubzero\Geocode` IP database being present;
  if the geo DB is unavailable the plugins skip silently (membership left unchanged).
- **Secrets** live in `configuration.php`, `app/config/*.php`, and `app/config/solr.json` (DB/Solr/
  mail credentials, the SSO cookie secret). These are git-ignored; keep them out of commits and docs.
- **Two git contexts:** edits in `core/` are tracked by this repo; edits in
  `app/components/com_psrsearch`, `app/plugins/user/psc`, `app/plugins/cron/psrsearch` are tracked by
  their own nested repos and are invisible to the top-level git.
