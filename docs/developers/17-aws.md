<!--
status: rewritten
reviewed-against: 2.4-main @ ab49f763b0
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/240/webdevs/aws
source-id: 3533
modified: 2016-03-11
imported: 2026-09-09
-->
# AWS

Nothing in this repository is specific to Amazon Web Services, apart from one
storage plugin. The CMS does not know or care what it runs on: it needs PHP, a
web server, and a database.

The page this replaces was a short recipe for a particular hub that ran on an
EC2 instance in 2016 — how to `ssh` in with a key pair, `sudo su -` as the
`centos` user, and find `configuration.php` under `/var/www/hub`. None of that
is describable from this codebase, some of it is no longer true of the CMS,
and the rest is ordinary EC2 and MySQL administration that Amazon documents
better. It is not reproduced here.

## What is actually AWS-specific

One plugin, `plg_filesystem_awss3`
([`core/plugins/filesystem/awss3`](../../core/plugins/filesystem/awss3)),
connects a project's file store to an S3 bucket. It is one of five filesystem
connectors, alongside Dropbox, GitHub, Google Drive, and the local disk. It
takes five parameters — **App ID**, **App Secret**, **Region**, **Bucket**,
and **Directory** — which an administrator sets under **Extensions** →
**Plugins**. The parameters are listed in
[the generated reference](../reference/configuration/plugins/filesystem.md).

The S3 access itself is `league/flysystem-aws-s3-v3`, a Composer dependency
declared in [`core/composer.json`](../../core/composer.json).

That is the whole of it. There is no AWS deployment tooling, no CloudFormation
or Terraform, no AMI build, and no image or session storage that assumes S3.

## Where a hub's configuration lives

The old page's one durable claim was wrong for 2.4. A hub's configuration is
not a single `configuration.php` in the document root. It is a set of PHP
files under `app/config/`, one per concern — `database.php`, `mail.php`,
`session.php`, `cache.php`, and the rest — each returning an array. `muse
install` and the web installer write them; the administrator interface
rewrites them when global configuration is saved.

The database credentials are in `app/config/database.php`. That directory is
created mode `0770` by
[`Hubzero\Console\Command\Install\AppDirectory`](../../core/libraries/Hubzero/Console/Command/Install/AppDirectory.php)
and must stay unreadable to anyone but the web user and its group.

See [Configuration](05-basics/03-config.md) for how the files are loaded and
overridden per client.

## Installing a hub

Hubzero installs from packages on Enterprise Linux 8 or a compatible rebuild,
on a cloud instance or anywhere else. Nothing about the machine
being an EC2 instance changes how a hub is installed; see
[Installation](../installation/README.md) for where that is documented.
