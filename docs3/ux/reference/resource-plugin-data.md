# Resource Plugin Data Associations

How resource plugins (reviews, questions, citations, etc.) link their data to
resources. Essential knowledge for seeding, querying, and debugging.

## Plugin Tab Visibility

Plugin tabs are controlled by the `params` column in `jos_resource_types`.
The format is newline-delimited `plg_name=0|1` pairs:

```
plg_share=1
plg_reviews=1
plg_questions=1
plg_citations=1
plg_supportingdocs=1
plg_versions=0
```

If a plugin param is missing or set to `0`, its tab won't appear on resources
of that type. To enable a tab:

```sql
UPDATE jos_resource_types
SET params = CONCAT(params, '\nplg_reviews=1')
WHERE id = 1;
```

## Reviews

**Table**: `jos_resource_ratings`

| Column | Purpose |
|--------|---------|
| `resource_id` | Direct FK to `jos_resources.id` |
| `user_id` | Author |
| `rating` | Numeric star rating (e.g., 4.5) |
| `comment` | Review text |
| `state` | 1 = published |

**Nested replies**: `jos_item_comments` with `item_type = 'review'` and
`item_id` = the review's `id` from `jos_resource_ratings`.

**Resource averages**: The `rating` and `times_rated` columns on
`jos_resources` must be updated manually (or by the plugin) after
adding/removing reviews.

## Questions

Questions are linked to resources **indirectly via tags**, not by a direct
foreign key.

### Tag Format

| Resource Type | Tag Value (raw) | Tag Value (normalized in DB) |
|---------------|-----------------|------------------------------|
| Tool (type=7) | `tool:{alias}` | `tool{alias}` (no colons/hyphens) |
| All others | `resource:{id}` | `resource{id}` |

### Tag Normalization (Critical)

**`Tag::normalize()` strips ALL non-alphanumeric characters.**

The regex is `preg_replace("/[^a-zA-Z0-9]/", '', $tag)` followed by
`strtolower()`.

```
resource:145                → resource145
tool:nanosim-molecular-dynamics → toolnanosimmoleculardynamics
```

The `tag` column in `jos_tags` stores the **normalized** form. The `raw_tag`
column stores the original. Any code that creates tags directly in the database
(seed scripts, migrations, import tools) must normalize the `tag` column value.

**Source**: `core/components/com_tags/models/tag.php:122-176`

### Association Tables

```
jos_tags           — tag records (tag column is normalized)
jos_tags_object    — links tags to objects
    objectid       — question ID (from jos_answers_questions)
    tagid          — tag ID (from jos_tags)
    tbl            — must be 'answers' (not 'questions')
```

### Query Flow (questions plugin)

The questions plugin at `core/plugins/resources/questions/questions.php:168-177`
does:

```php
$cloud = new \Components\Answers\Models\Tags();
$tags = $cloud->parse($this->filters['tag']);  // normalizes the tag

$records
    ->join('#__tags_object', '#__tags_object.objectid', '#__answers_questions.id')
    ->join('#__tags', '#__tags.id', '#__tags_object.tagid')
    ->whereEquals('#__tags_object.tbl', 'answers')
    ->whereIn('#__tags.tag', $tags);
```

`$cloud->parse('resource:145')` returns `['resource145']` (normalized). The
query then matches against the `tag` column which must also contain `resource145`.

### Common Bug

If questions are in the database with tag associations but the questions tab
shows "0 questions", check the `tag` column in `jos_tags`. If it contains
`resource:145` (with colon) instead of `resource145` (normalized), the query
won't match.

## Citations

**Table**: `jos_citations`

| Column | Purpose |
|--------|---------|
| `type` | Citation type: `journal`, `book`, `inproceedings`, etc. |
| `title` | Citation title (required) |
| `author` | Author string |
| `year`, `volume`, `pages`, etc. | BibTeX-style fields |
| `doi` | DOI (displayed as clickable link) |
| `affiliated` | 0 = non-affiliated, 1 = affiliated (affects display grouping) |
| `published` | 1 = visible |
| `note` | Free text (useful for seed markers) |

**Association table**: `jos_citations_assoc`

| Column | Purpose |
|--------|---------|
| `cid` | Citation ID (`jos_citations.id`) |
| `oid` | Resource ID (`jos_resources.id`) |
| `tbl` | Must be `'resource'` (singular, not `'resources'`) |

The plugin query at `core/plugins/resources/citations/citations.php:85-92`:

```php
$citations = Citation::all()
    ->join('#__citations_assoc', ...)
    ->whereEquals('#__citations.published', 1)
    ->whereEquals('#__citations_assoc.tbl', 'resource')
    ->whereEquals('#__citations_assoc.oid', $model->id)
    ->order('affiliated', 'asc')
    ->order('year', 'desc')
    ->rows();
```

Citations display in two groups: non-affiliated first, then affiliated, both
sorted by year descending. Each citation has BibTeX and EndNote export links.

**Citation types**: Stored in `jos_citations_types`. Common types: `journal`
(id=1), `article` (2), `book` (3), `conference` (5), `inproceedings` (8),
`techreport` (15).

## Seed Script

`core/components/com_resources/seed-plugins.php` seeds reviews, questions,
answers, and citations across multiple resources. Run after `seed.php`:

```bash
php core/components/com_resources/seed.php           # creates resources first
php core/components/com_resources/seed-plugins.php    # adds plugin data
php core/components/com_resources/seed-plugins.php --down  # removes plugin data
```

The seed script uses `[SEED2-PLG]` as a content marker for idempotent
operations. It normalizes tags correctly when creating question associations.
