<!--
status: imported
source: https://help.hubzero.org/documentation/240/installation/el8/install/cms
source-id: 3687
modified: 2016-12-16
imported: 2026-09-09
source-state: unpublished
-->
# CMS

## Installation

```
sudo yum install -y hubzero-cms-2.2
sudo yum install -y hubzero-texvc
sudo yum install -y hubzero-textifier
sudo yum install -y wkhtmltopdf
```

## Configuration

```
sudo hzcms install hubname
```

> **Warning:** It is necessary to immediately run the updater to apply fixes that have not been incorporated into the initial installation.

```
sudo hzcms update
```

## SSL Configuration

The default SSL certificate is a self signed certificate (aka snakeoil) meant for evaluation purposes only. Some browers will not accept this certificate and will not allow access to the site without special configuration (https://support.mozilla.org/en-US/questions/1012036). For a production hub you will need to obtain a valid SSL certificate. A certificate may contain two or three pieces: a public certificate, a private key, and sometimes an intermediate certificate. All files are expected to be in PEM format.

Let's Encrypt and Certbot may be a viable option to obtain SSL certificates - https://certbot.eff.org/lets-encrypt/centosrhel7-apache.html
\*\*Do NOT use Snap. "yum install -y certbot python-certbot-apache", then follow the rest of the instructions to run the 'certbot' commands - start with step #7 'just get a certificate' "certbot certonly --apache".

Once you obtain the certificate, copy the SSL certificate files to the httpd SSL configuration directories and restart httpd.

```
sudo cp [your certificate pem file] /etc/httpd/conf/ssl.crt/hubname-cert.pem
sudo cp [your certificate private key pem file] /etc/httpd/conf/ssl.key/hubname-privkey.pem
sudo cp [your certificate intermediate certificate chain pem file] /etc/httpd/conf/ssl.crt/hubname-chain.pem
sudo chown root:root /etc/httpd/conf/ssl.crt/hubname-cert.pem
sudo chown root:root /etc/httpd/conf/ssl.key/hubname-privkey.pem
sudo chown root:root /etc/httpd/conf/ssl.crt/hubname-chain.pem
sudo chmod 0640 /etc/httpd/conf/ssl.crt/hubname-cert.pem
sudo chmod 0640 /etc/httpd/conf/ssl.key/hubname-privkey.pem
sudo chmod 0640 /etc/httpd/conf/ssl.crt/hubname-chain.pem
sudo hzcms reconfigure hubname
sudo service httpd restart
```

> **Note:** If you are using the HTML5 VNC Proxy Server, you must update your certificate settings there as well.

## First Hub Account Creation

You should now be able to create a new user account for yourself via hub web registration at "/register". Depending on your email hosting you may not receive the confirmation email. After your new user account is created, you can confirm, approve, and promote the new account as the installing admin by logging into /administrator with the admin account credentials. The 'admin' user password is stored in /etc/hubzero.secrets and can only be read by root user. The admin account is only to be used once to approve and promote your individual user account and should be disabled in the CMS afterwards.
