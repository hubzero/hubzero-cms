<!--
status: draft
-->
# Concepts

The words that come up in every book.

## The hub

A **hub** is one installation of the Hubzero CMS: a website with its own
members, content, and configuration. One codebase can serve one hub. The
site has two faces: the **site**, which members and visitors use, and the
**administrator** interface at `/administrator`, which hub managers use to
configure it.

## Extensions

Everything a hub does is provided by extensions of three kinds.

- A **component** is a whole feature with its own URL space, database
  tables, and administrator screens: resources, groups, publications, the
  wiki, support tickets. Component code lives under
  `core/components/com_<name>/`, split into `site/`, `admin/`, and `api/`
  sides.
- A **plugin** hooks into an event that a component fires. Plugins are
  grouped by the event family they answer to: `groups` plugins add tabs to a
  group page, `resources` plugins add tabs to a resource page, `search`
  plugins index content, `authentication` plugins log people in. Plugin code
  lives under `core/plugins/<group>/<name>/`.
- A **module** is a box of content placed in a template position: a login
  form, a list of recent questions, a menu. Module code lives under
  `core/modules/mod_<name>/`.

A **template** decides how the whole page looks and which positions modules
can occupy. Templates live under `core/templates/` and a hub can override
any extension's views inside its template.

## People

A **member** is a registered account. Members belong to **access groups**,
which decide what they may do in the administrator interface, and to
**groups**, which are collaboration spaces with their own pages, files,
forums, and membership roles. A **super group** is a group with its own
template and extensions, effectively a site within the hub.

## Content

A **resource** is a published item in the hub's catalogue: a tool, a
dataset, a presentation, a course. **Publications** are versioned,
citable resources that go through a curation workflow. A **project** is a
private workspace where a team prepares files, notes, and publications
before releasing them. Wiki pages, blog entries, forum posts, questions,
knowledge base articles, and events are content as well, each managed by
its own component.

## Tools

A **tool** is a simulation program that runs in a session on the hub's
execution hosts and is displayed in the member's browser. Tools are
developed in a repository, built and tested through the tool pipeline, and
published as resources. The [Tools](../tools/README.md) book covers that
side of the platform.
