# Hubzero documentation

Hubzero is software for websites where a research or teaching community
shares resources and works together. Each website is called a **hub**, with
its own address, accounts, content, and settings. This documentation covers
version 2.4 of the Hubzero content management system (**CMS**).

For example, a lab can use a hub to announce seminars in a group, prepare
data in a project, and release a publication that other researchers can
cite. A hub with the tool execution platform can also let members run
analysis programs in their browsers.

## Start here

You can read these books independently. Choose the task you need to do;
you do not need to install the software to use someone else's hub.

| You want to | Start with | You need |
|---|---|---|
| Use an existing hub | [Introduction](users/04-introduction.md), then [Getting started](users/30-gettingstarted.md) | The hub's address; an account for participating |
| Configure a hub or manage its members and content | [Getting started for hub managers](managers/01-getting-started/index.md) | An installed hub and permission to use `/administrator` |
| Install a hub on a server | [Installation overview](sysadmins/01-installing.md) | Access to a Linux server and permission to administer it |
| Try the CMS locally or change its code | [Development environment](developers/01-getting-started/06-devenvironment.md#developing-in-docker) | A Git checkout and Docker with Compose for the documented local setup |
| Publish a program that runs on a hub | [Tool developer prerequisites](tools/developers/prerequisites.md) | An account on a hub with the separate tool platform |
| Look up a setting, endpoint, command, or event | [Reference](reference/index.md) | The name of the feature you are working with |

A **hub manager** works in the browser to configure the site. A **system
administrator** looks after its server, files, database, and upgrades. One
person may do both jobs. A **CMS developer** changes the website software;
a **tool developer** writes a program that members run through it.

## What the CMS includes

The [hubzero-cms repository](../README.md) contains the website application:
member accounts, content, groups, projects, publications, administrator
screens, and the framework used to extend them. The installation guides
and development setup get this CMS running with PHP, a web server, and a
database.

Some features need additional services. The tool execution platform runs
programs on execution hosts; Solr provides an optional search engine;
statistics collection supplies usage reports. The CMS has interfaces for
these services, but installing it does not install the services themselves.
The [Tools book](tools/index.md) explains that boundary in more detail.

Each hub enables the features it needs. A missing menu or tab can reflect
the hub's configuration, your permissions, or an integration that has not
been set up. Ask that hub's support staff if the instructions describe a
feature you cannot find.

### Hosting and support notice

> **Warning:** We are no longer able to support the Hubzero Platform as a complete open source product. Please see https://hubzero.org for hosting options or email info@hubzero.org.

> **Note:** We are in the process of releasing a self service, self-hosted Hubzero CMS product here to explore before trying one of our hosting or development services. This will not include many integrations including tools. This is a work in progress.

## Common terms

| Term | Meaning |
|---|---|
| Member | A person with an account on this hub |
| Group | A community space with members, pages, and optional features such as a forum |
| Project | A workspace where a team prepares files and research outputs |
| Resource | An item in the hub's library, such as a presentation, dataset, or tool |
| Publication | A versioned research output released through a publishing workflow; it can carry a digital object identifier (DOI) for citation |
| Tool and session | A tool is a program offered by the hub; a session is one running instance of it |
| Extension | Code that adds a feature or presentation to the CMS: a component, module, plugin, or template |
| Muse | The CMS command-line tool, invoked as `php core/bin/muse` from the installation directory |

The [Hub users](users/index.md) book explains the features from a member's
perspective. [How a hub fits together](managers/01-getting-started/03-how-a-hub-fits-together.md)
explains how the website is assembled and where its content is stored.

## The books

The books below cover each area in more detail.

### Run a hub

- [System administrators](sysadmins/index.md) — putting a hub on a
  server and keeping it current: installing it, and upgrading it.
- [Hub managers](managers/index.md) — administering a hub once it runs:
  configuration, members and access, content, extensions, maintenance, and
  every component's administrative side.

### Use a hub

- [Hub users](users/index.md) — profiles, groups, projects, publications,
  resources, the wiki, forums, courses, and the other features a hub offers.
- [Tools](tools/index.md) — the tool platform: publishing simulation tools,
  invoke scripts, the submit command, Jupyter, and tool administration.

### Build on Hubzero

- [Developers](developers/index.md) — extending the CMS: the framework
  foundation, components, plugins, modules, templates, the database layer,
  and the REST API.
- [Contributing](developers/18-contributing.md) — sending a change back, and
  the [conventions](developers/19-conventions/index.md) the codebase follows: commit
  practice, testing, and how to send changes back to the project.

### Reference

- [Reference](reference/index.md) — generated references: configuration
  parameters, REST endpoints, muse console commands, and events.
- [Release notes](developers/01-getting-started/01-releasenotes.md) — how
  Hubzero is released, and where to find what changed.

## About the documentation

Paths such as `/register` and `/administrator` are addresses on **your
hub**. For a hub at `https://hub.example.org`, `/register` means
`https://hub.example.org/register`. Paths such as `core/` and `app/` are
directories on the server or in a development checkout. Each setup guide
says where to run its shell commands and which example values to replace.

- [Writing guide](STYLE.md) — how pages are written and built.
- [License](../LICENSE) — MIT, the same as the code.
- [Status](https://hubzero.github.io/hubzero-cms/status/) — which pages have
  been reviewed against the current code.

The source is Markdown under `docs/` in the repository and is published at
https://hubzero.github.io/hubzero-cms/. Review dates describe checks against
a particular version of the code. Generated references describe declarations
in that source; your hub's enabled features and saved settings may differ.
A page that has not been reviewed carries a notice at the top.
