<!--
status: imported
source: https://help.hubzero.org/documentation/240/installation/el8/install/installmailgateway, https://help.hubzero.org/documentation/240/installation/el8/install/openldap, https://help.hubzero.org/documentation/240/installation/el8/install/firewall, https://help.hubzero.org/documentation/240/installation/el8/install/subversion, https://help.hubzero.org/documentation/240/installation/el8/install/trac, https://help.hubzero.org/documentation/240/installation/el8/install/forge, https://help.hubzero.org/documentation/240/installation/el8/install/openvz, https://help.hubzero.org/documentation/240/installation/el8/install/maxwell_file_service, https://help.hubzero.org/documentation/240/installation/el8/install/maxwell_client, https://help.hubzero.org/documentation/240/installation/el8/install/metrics, https://help.hubzero.org/documentation/240/installation/el8/install/workspace, https://help.hubzero.org/documentation/240/installation/el8/install/filexfer, https://help.hubzero.org/documentation/240/installation/el8/install/solr
imported: 2026-09-09
-->
# Optional services

Each of these services is installed from its own Hubzero package and switched on with `hzcms configure <service> --enable`. Install only the ones your hub needs; none of them is required for the CMS itself.

## Mailgateway

### Install the Hubzero Mailgateway

```
sudo yum install -y hubzero-mailgateway
```

### Configure the Hubzero Mailgateway

```
sudo hzcms configure mailgateway --enable
```

## OpenLDAP

### Install hubzero-openldap

```
sudo yum install -y hubzero-openldap

sudo service slapd start
sudo chkconfig slapd on
sudo chkconfig sssd on
```

### Configure OpenLDAP database

```
sudo hzldap init dc=hubname,dc=org
sudo hzcms configure ldap --enable
sudo hzldap syncusers
```

### Test

```
sudo getent passwd
```

You should see an entry for user 'admin' toward the end of the list if everything is working correctly.

## Firewall

### Install

```
sudo yum remove -y firewalld
sudo yum install -y hubzero-iptables-basic

sudo service hubzero-iptables-basic start
sudo chkconfig hubzero-iptables-basic on
```

If installing the Hubzero tool infrastructure:

```
sudo yum install -y hubzero-mw2-iptables-basic

sudo service hubzero-mw2-iptables-basic start
sudo chkconfig hubzero-mw2-iptables-basic on
```

HUBzero requires the use of iptables to route network connections between application sessions and the external network. The scripts controlling this can also be used to manage basic firewall operations for the site. The basic scripts installed here block all access to the host except for those ports required by HUBzero (http,https,http-alt,ldap,ssh.smtp,mysql,submit,etc).

## Subversion

### Install

```
sudo yum install -y hubzero-subversion
```

### Configure

```
sudo hzcms configure subversion --enable
```

## Trac

### Install

```
sudo yum install -y hubzero-trac
```

### Configure

```
sudo hzcms configure trac --enable
```

## Forge

### Install

```
sudo yum install -y hubzero-forge
```

### Configure

```
sudo hzcms configure forge --enable
```

## OpenVZ

### Install

```
sudo curl -s -o /etc/yum.repos.d/openvz.repo https://download.openvz.org/openvz.repo
sudo rpm --import http://download.openvz.org/RPM-GPG-Key-OpenVZ
```

Then install

```
sudo yum install -y hubzero-openvz
```

### Configure

```
sudo hzcms configure openvz --enable
```

> **Note:** If configuration is successful it should prompt you to reboot the server to activate the new kernel.

```
sudo reboot
```

### Test

```
sudo vzlist
Container(s) not found
```

Or it will list the containers currently running if you check this on a running hub. The salient point being that the command doesn't issue any kind of error message.

## Maxwell File Service

### Install

```
sudo yum install -y hubzero-mw2-file-service
```

## Maxwell Client

### Install

```
sudo yum install -y hubzero-mw2-client
sudo yum install -y hubzero-expire-sessions
```

### Configure

```
sudo hzcms configure mw2-client --enable
sudo service expire-sessions start
sudo chkconfig expire-sessions on
```

## Metrics

### Install

```
sudo yum install -y hubzero-metrics
```

### Configure

```
sudo hzcms configure metrics --enable
```

## Workspace

### Install

```
sudo yum install -y hubzero-app
sudo yum install -y hubzero-app-workspace
sudo hubzero-app install --publish /usr/share/hubzero/apps/workspace-1.3.hza
```

### Test

You should then be able to log in to the site and see the "Workspace" tool in the tool list and launch it in your browser.

## Filexfer

### Install

```
sudo yum install -y hubzero-filexfer-xlate
```

### Configure

```
sudo hzcms configure filexfer --enable
```

## SOLR Search

### Install

```
sudo yum install -y hubzero-solr
```

```

```
