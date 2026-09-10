# Hubzero documentation

Hubzero® is an open source software platform for building powerful websites that host analytical tools, publish data, share resources, collaborate and build communities in a single web-based ecosystem. Initially created by researchers in the NSF-sponsored [Network for Computational Nanotechnology](http://nanohub.org/groups/ncn) to support [nanoHUB.org](http://nanohub.org/). The Hubzero Platform now supports science gateways from a variety of disciplines with a collective of over 2 million visitors a year.

Hubzero includes a powerful content management system built to support scientific activities. Members on a hub can write blog entries, participate in discussion groups, work together in projects, publish datasets and computational tools with digital object identifiers (DOIs), and make these publications available for others to use—not as dusty downloads, but as live, interactive digital resources. Simulation/modeling tools published on a hub can be accessed with the click of a button, running on cloud computing resources, campus clusters, and other national high-performance computing (HPC) facilities and serve up compelling visualizations.

Hubzero partners to help researchers:

- Create datasets and interactive simulation tools using RStudio, Jupyter Notebooks, and other Web Applications
- Publish research products including, datasets, tools, and white papers, through a step-by-step guided system
- Provide spaces for your research teams and collaborators to discuss data concepts, track progress, and share files.

> **Warning:** We are no longer able to support the Hubzero Platform as a complete open source product. Please see https://hubzero.org for hosting options or email info@hubzero.org.

> **Note:** We are in the process of releasing a self service, self-hosted Hubzero CMS product here to explore before trying one of our hosting or development services. This will not include many integrations including tools. This is a work in progress.

---

This documentation covers Hubzero 2.4. It is organized as books, each for a
different kind of reader. Everything here is Markdown in the
[hubzero-cms repository](https://github.com/hubzero/hubzero-cms/tree/2.4-main/docs)
and is published at https://hubzero.github.io/hubzero-cms/.

## Run a hub

- [Hub managers](managers/README.md) — installing a hub, then administering
  it: configuration, members and access, content, extensions, maintenance,
  and every component's administrative side.

## Use a hub

- [Hub users](users/README.md) — profiles, groups, projects, publications,
  resources, the wiki, forums, courses, and the other features a hub offers.
- [Tools](tools/README.md) — the tool platform: publishing simulation tools,
  invoke scripts, the submit command, Jupyter, and tool administration.

## Build on Hubzero

- [Developers](developers/README.md) — extending the CMS: the framework
  foundation, components, plugins, modules, templates, the database layer,
  and the REST API.
- [Contributing](developers/18-contributing.md) — sending a change back, and
  the [conventions](developers/19-conventions.md) the codebase follows: commit
  practice, testing, and how to send changes back to the project.

## Reference

- [Reference](reference/README.md) — generated references: configuration
  parameters, REST endpoints, muse console commands, and events.
- [Release notes](developers/01-getting-started/01-releasenotes.md) — how
  Hubzero is released, and where to find what changed.

## About the documentation

- [Writing guide](STYLE.md) — how pages are written and built.
- [License](LICENSE.md) — MIT, like the code.
- [Status](https://hubzero.github.io/hubzero-cms/status/) — which pages have
  been reviewed against the current code.

Most of this material was imported from help.hubzero.org in September 2026
and is being reviewed against the code book by book. A page that has not yet
been reviewed says so at the top.
