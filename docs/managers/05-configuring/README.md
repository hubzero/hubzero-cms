<!--
status: rewritten
reviewed-against: 2.4-main @ 123ea53b14
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/configuring
-->
# Configuring

Almost everything about a hub is set from the administrator interface. This
section covers the four places settings live: the global configuration that
applies to the whole site, the options attached to each component, the
parameters on each module instance, and the parameters on each plugin.

## Where settings live

| Setting | Where you edit it |
|---|---|
| Site-wide settings — site name, mail, sessions, caching, SEO, permissions | **Site > Global Configuration**. See [Global configuration](01-hub.md). |
| One component's options | The **Options** button in that component's toolbar. See [Components](03-components.md). |
| One module instance's parameters | **Extensions > Module Manager**, then the module. See [Modules](04-modules.md). |
| One plugin's parameters | **Extensions > Plug-in Manager**, then the plugin. See [Plugins](05-plugins.md). |
| Which fields a new member must fill in | **Users > Members > Registration**. See [Registration](02-registration.md). |
| How people sign in | **Extensions > Plug-in Manager**, filtered to the `authentication` type. See [Authentication](06-authentication.md) and [External authenticators](07-extauth.md). |

## In this section

- [Global configuration](01-hub.md)
- [Registration](02-registration.md)
- [Components](03-components.md)
- [Modules](04-modules.md)
- [Plugins](05-plugins.md)
- [Authentication](06-authentication.md)
- [External authenticators](07-extauth.md)

Every parameter that any component or plugin manifest declares is listed in
the generated [configuration reference](../../reference/configuration/README.md).
These chapters explain the screens; the reference is the exhaustive list.
