<!--
status: imported
source: https://help.hubzero.org/documentation/240/installation/el8/install/maxwell_service
source-id: 3696
modified: 2016-12-16
imported: 2026-09-09
source-state: unpublished
-->
# Maxwell Service

## Install

```
sudo yum install -y hubzero-mw2-exec-service
sudo yum install -y hubzero-mw2-iptables-basic
sudo service hubzero-mw2-iptables-basic start
sudo chkconfig hubzero-mw2-iptables-basic on
```

## Configure

```
sudo mkvztemplate amd64 wheezy ellie
```

```
sudo hzcms configure mw2-service --enable
sudo hzcms mw-host add localhost up openvz pubnet sessions workspace fileserver
```

## Test

```
sudo maxwell_service startvnc 1 800x600 24
```

> **Note:** Enter an 8 character password when prompted (e.g., "testtest")

This should result in a newly create OpenVZ session with an instance of a VNC server running inside of it. The output of the above command should look something like:

```
Reading passphrase:
testtest
===================== begin /etc/vz/conf/hub-session-5.0-amd64.umount =========================

Removing /var/lib/vz/root/1 :root etc var tmp dev/shm dev
===================== end /etc/vz/conf/hub-session-5.0-amd64.umount ==========================
stunnel already running
Starting VE ...
===================== begin /etc/vz/conf/1.mount ==========================
Removing and repopulating: root etc var tmp dev
Mounting: /var/lib/vz/template/debian-5.0-amd64-maxwell home apps
===================== end /etc/vz/conf/1.mount ============================
VE is mounted
Setting CPU units: 1000
Configure meminfo: 2000000
VE start in progress...
TIME: 0 seconds.
Waiting for container to finish booting.
/usr/lib/mw/startxvnc: Becoming nobody.
/usr/lib/mw/startxvnc: Waiting for 8-byte vncpasswd and EOF.
1+0 records in
1+0 records out
8 bytes (8 B) copied, 3.5333e-05 s, 226 kB/s
Got the vncpasswd
Adding auth for 10.51.0.1:0 and 10.51.0.1/unix:0
xauth:  creating new authority file Xauthority-10.51.0.1:0
Adding IP address(es): 10.51.0.1
if-up.d/mountnfs[venet0]: waiting for interface venet0:0 before doing NFS mounts (warning).
WARNING: Settings were not saved and will be resetted to original values on next start (use --save flag)
```

```
sudo vzlist
      VEID      NPROC STATUS  IP_ADDR         HOSTNAME
         1          6 running 10.51.0.1       -
```

```
sudo openssl s_client -connect localhost:4001
```

This should report an SSL connection with a self signed certificate and output text should end with:

```
---
RFB 003.008
```

If you see this then you successfully connected to the VNC server running inside the newly created OpenVZ session.

Clean up

```
sudo maxwell_service stopvnc 1
```

Which should give output similar to:

```
Killing 6 processes in veid 1 with signal 1
Killing 7 processes in veid 1 with signal 2
Killing 5 processes in veid 1 with signal 15
Got signal 9
Stopping VE ...
VE was stopped
===================== begin /etc/vz/conf/1.umount =========================
Unmounting /var/lib/vz/root/1/usr
Unmounting /var/lib/vz/root/1/home
Unmounting /var/lib/vz/root/1/apps
Unmounting /var/lib/vz/root/1/.root

Removing /var/lib/vz/root/1 :root etc var tmp dev/shm dev
Removing /var/lib/vz/private/1: apps bin emul home lib lib32 lib64 mnt opt proc sbin sys usr .root
===================== end /etc/vz/conf/1.umount ==========================
VE is unmounted
```
