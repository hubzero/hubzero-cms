<!--
status: imported
source: https://help.hubzero.org/documentation/240/installation/el8/install/hzvncproxydws
source-id: 3698
modified: 2016-12-16
imported: 2026-09-09
source-state: unpublished
-->
# VNC Proxy Server

## Install

```
sudo yum install -y hubzero-vncproxyd-ws
```

## Configure

```
sudo hzvncproxyd-ws-config configure --enable
sudo service hzvncproxyd-ws start
sudo chkconfig hzvncproxyd-ws on
```

## Install SSL certificate files

Copy your Apache SSL certificate files to /etc/hzvncproxyd-ws/ssl-cert-hzvncproxyd-ws.pem and /etc/hzvncproxyd-ws/ssl-cert-hzvncproxyd-ws.key and make sure they are readable by the user "hzvncproxy" to be found automatically by the proxy service.

> **Warning:** If you are using a self-signed or otherwise invalid certificate the tool viewer will likely reject it and not work. If you are using the same certificate as your website and you allowed Chrome to use the invalid cert then the tool viewer will probably accept it. If you are using Firefox the tool viewer will always reject the invalid certificate. Always use a valid SSL certificate with hzvncproxyd-ws.
