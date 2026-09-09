<!--
status: imported
source: https://help.hubzero.org/documentation/240/installation/el8/install/webdav
source-id: 3689
modified: 2016-12-16
imported: 2026-09-09
source-state: unpublished
-->
# WebDAV

## Install WebDAV

```
sudo yum install -y hubzero-webdav
```

## Configure WebDAV

```
sudo hzcms configure webdav --enable
```

## Test

Test the fuse filesystem mount. Create the file mytest and then view the directory contents. You should see no errors and the file "mytest" should appear in the directory listing

```
sudo touch /webdav/home/admin/mytest
sudo ls -l /webdav/home/admin
```

Now test using a WebDAV client.

```
sudo yum install -y cadaver
sudo cadaver https://localhost/webdav
```

You will be prompted to accept self signed certificate (if it is still installed) and then to enter your username and password. Use the 'admin' account to test. The admin credentials are located in /etc/hubzero.secrets if you have forgetten them. When you get the "dav:/webdav/>" prompt just enter "ls" and you should see the "mytest" file listed.

Finally clean up test case

```
sudo yum remove cadaver
sudo rm /webdav/home/admin/mytest
```

## Troubleshooting

If the test doesn't work, check if the fuse kernel module is loaded

```
sudo lsmod | grep fuse
fuse                   54176  0
```

If there is no output then try starting the kernel module manually

```
sudo modprobe fuse
```

Then try the test again
