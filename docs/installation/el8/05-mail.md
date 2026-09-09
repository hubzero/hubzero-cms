<!--
status: imported
source: https://help.hubzero.org/documentation/240/installation/el8/install/mail
source-id: 3686
modified: 2016-12-16
imported: 2026-09-09
source-state: unpublished
-->
# Mail

## Install Postfix

```
sudo yum install -y postfix

sudo service postfix start
sudo chkconfig postfix on
```

## Test

```
sudo postfix check
```

If the 'postfix check' command returns anything, resolve the reported issues with the Postfix installation before continuing.

## Configure Postfix

Configure Postfix as desired. The default installation may only handle mail on the localhost. HUBzero expects to be able to send mail to registered user's email address to confirm registration and also to send group and support ticket related messages. Incoming mail is also expected to work (see Mailgateway section) in order to receive support ticket updates and email replies to group forum messages. Setting up an appropriate mail configuration is up to the site administrator. The Mailgateway service expects postfix to be the Mail Transfer Agent on CentOS and RedHat systems.
