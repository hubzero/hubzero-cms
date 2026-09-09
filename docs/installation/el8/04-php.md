<!--
status: imported
source: https://help.hubzero.org/documentation/240/installation/el8/install/php
source-id: 3684
modified: 2018-07-03
imported: 2026-09-09
-->
# PHP: Hypertext Preprocessor

## Install PHP: Hypertext Preprocessor

The full HUBzero® Platform currently requires PHP: Hyptertext Preprocessor 8.2.x. The basic HUBzero CMS product available here might work with later 8.x versions but this is currently untested.

> $ sudo dnf -y install module php:8.2
> $ sudo systemctl enable --now php-fpm
