<!--
status: imported
source: https://help.hubzero.org/documentation/240/installation/el8/install/database
source-id: 3685
modified: 2025-01-31
imported: 2026-09-09
-->
# MariaDB Community Server

## MariaDB Community Server Installation

The full HUBzero® Platform currently requires MariaDB Community Server 10.11.x. The basic HUBzero CMS product available here should work with any database server based on MySQL 5.6 (MySQL, Percona, etc), but will be limited in what additional HUBzero Platform components can be added later.

> $ sudo dnf module install mariadb:10.11
> $ sudo systemctl enable --now mariadb
> $ sudo mariadb
> MariaDB [(none)]> CREATE DATABASE `myhub`;
> MariaDB [(none)]> CREATE USER `myhub`@localhost IDENTIFIED BY 'mypassword';
> MariaDB [(none)]> GRANT ALL PRIVILEGES ON `myhub`.\* TO `myhub`@localhost;
> MariaDB [(none)]> exit
> Bye
