<!--
status: merged
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/accesshomedir/webdav
source-id: 3546
modified: 2014-02-28
imported: 2026-09-09
merged-from: 2.2
-->
# WebDAV

## Accessing your home directory via WebDAV

WebDAV is the Distributed Authoring and Versioning extension to the standard HTTP/HTTPS web protocol. It allows a client to browse a remote filesystem, usually with a graphical browser that makes it appear that your files are on your desktop. You may access your hub storage using only the secure version of this service (HTTPS). We do not support HTTP. Most modern computer platforms support HTTPS transport for WebDAV with either small adjustments or freely available software.

## Linux/Unix

Firefox and Chrome browsers provide text-mode support by connecting to `https://hubname.org/webdav`. You will be prompted for hub login and password. If you use the KDE graphical desktop environment, you can access your hub storage with the Konqueror browser by typing the special URL `webdavs://hubname.org/webdav/`into the Location field of the browser. It will prompt you for your hub login and password. Thereafter, you traverse your home directory by clicking on folders and you can drag and drop files to your desktop.
[Cadaver](http://www.webdav.org/cadaver/) is a text-mode WebDAV browser. It can be used if it is compiled with SSL support. Invoke it with the command `cadaver https://hubname.org/webdav/` and it will prompt you for your hub login and password. You can then use it in a manner similar to FTP.
If you are using Linux, you can use the [davfs](http://dav.sourceforge.net/) kernel module to mount your hub storage area as a local filesystem.

## Macintosh

MacOS versions 10.4 and higher support HTTPS transport for WebDAV using the Finder.

1. Select the Go menu in the Finder and choose "Connect to Network Server".
2. Enter the URL `https://hubname.org/webdav/` into the address field.
3. When prompted, enter your Network ID credentials.

You should now be able to drag files and folders between your computer and the site to which you just connected.

## Windows 10, 8.1, 8 and 7

Windows 10, 8.1 and 8 use the **WebClient Services** to connect to a WebDAV Servers, by default the WebClient service is disabled, so we need to enable it.

1. From the Start menu, choose Control Panel, then System and Security, then Administrative Tools, and then Services.
2. Scroll down to WebClient, set the service to Automatic, and then click Apply.
3. If the service is not already running, click Start.
4. Click OK to close the Control Panel and close other windows.

## Windows 10

To set up a WebDAV connection in Windows 10:

1. From the **Start Menu** go to **File Explorer** and select **This PC** on the left hand pane
2. Select **Computer** from the top ribbon
3. Click on **Map Network Drive**
4. Click **Connect to a Web site that you can use to store your documents and pictures.**
5. Click **Next**
6. Select **Choose another network location** and click **Next**
7. Enter "`https://hubname.org/webdav/"`. Replace "hubname.org" with the URL of the destination hub and click **Next**
8. Enter your **password**, and click **Ok**
9. Click **Next**, then **Finish**
10. When prompted, enter your Hub credentials.
11. You should see a new Network Drive under your Computer/This PC. Double click on it to open.

You should now be able to drag files and folders between your computer and the hub via the network drive to which you just connected.

## Windows 8.x

To set up a WebDAV connection in Windows 8.x:

1. Using the Search interface in tile mode, locate and select the Computer tile.
2. In the quick menu at the top of the screen, click Map Network Drive.
3. In the "Folder" field, enter a URL that points to the destination hub similar to the following URL "`https://hubname.org/webdav/"` Replace "hubname.org" with the URL of the destination hub.
4. Select the Connect using different credentials box, and then click Finish.
5. When prompted, enter your Hub credentials.
6. You should see a new Network Drive under your Computer/This PC. Double click on it to open.

You should now be able to drag files and folders between your computer and the hub via the network drive to which you just connected.

## Windows 7

To set up a WebDAV connection in Windows 7.

1. From the Start menu, right-click Computer, and select Map network drive.
2. Enter a URL that points to the destination hub similar to the following URL "`https://hubname.org/webdav/"`. Replace "hubname.org" with the URL of the destination hub. Clink finish.
3. When prompted, enter your Hub credentials.
4. You should see a new Network Drive under your Computer/This PC. Double click on it to open.

You should now be able to drag files and folders between your computer and the hub via the network drive to which you just connected.

If you have difficulty dragging and dropping, right-click the file or folder you want to copy, and choose Copy. Then right-click the directory you want to put it in, and choose Paste.

## Material from the 2.2 documentation

> **Note:** The text below comes from the older 2.2 page of the same name, where it differed substantially from the 2.4 page above. Reconcile the two when reviewing.

## WebDAV

WebDAV is the Distributed Authoring and Versioning extension to the standard HTTP/HTTPS web protocol. It allows a client to browse a remote filesystem, usually with a graphical browser that makes it appear that your files are on your desktop. You may access your hub storage using only the secure version of this service (HTTPS). We do not support HTTP. Most modern computer platforms support HTTPS transport for WebDAV with either small adjustments or freely available software.

## Linux

If you use the KDE graphical desktop environment, you can access your hub storage with the Konqueror browser by typing the special URL `webdavs://webdav.hubname.org/webdav/` (open source: `webdavs://hubname.org/webdav/)`into the Location field of the browser. It will prompt you for your hub login and password. Thereafter, you traverse your home directory by clicking on folders and you can drag and drop files to your desktop.

For Cinnamon or other graphical desktops, you can use the file browser with webdav. In the location field, enter **davs://username@yourhub.org/webdav**.

OR

From the menu, select "Connect to Server" and fill out the form. Be sure to select "Secure WebDAV (HTTPS)" and folder "/webdav".
![Connect Menu for Linux](../../media/webdav-connect-menu-linux.png)![](../../media/webdav-connect-dialog-linux.png)
You can also use the [davfs2](http://savannah.nongnu.org/projects/davfs2) kernel module to mount your hub storage area as a local filesystem.

## Macintosh

MacOS versions 10.4 and higher support HTTPS transport for WebDAV using the Finder.

1. Select the Go menu in the Finder and choose "Connect to Network Server".
2. Enter the URL `https://webdav.hubname.org/webdav/ (open source: https://hubname.org/webdav/` into the address field.
3. When prompted, enter your Network ID credentials.

You should now be able to drag files and folders between your computer and the site to which you just connected.

## Windows 10

The **WebClient Services** to connect to a WebDAV Servers, by default, the WebClient service is disabled, so we need to enable it.

1. From the Start menu, choose Control Panel, then System and Security, then Administrative Tools, and then Services.
2. Scroll down to WebClient, set the service to Automatic, and then click Apply.
3. If the service is not already running, click Start.
4. Click OK to close the Control Panel and close other windows.

To set up a WebDAV connection in Windows 10:

1. From the **Start Menu** go to **File Explorer** and select **This PC** on the left hand pane
2. Select **Computer** from the top ribbon
3. Click on **Map Network Drive**
4. Click **Connect to a Web site that you can use to store your documents and pictures.**
5. Click **Next**
6. Select **Choose another network location** and click **Next**
7. Enter "`https://webdav.hubname.org/webdav/" (open source: https://hubname.org/webdav/)`. Replace "hubname.org" with the URL of the destination hub and click **Next**
8. Enter your **password**, and click **Ok**
9. Click **Next**, then **Finish**
10. When prompted, enter your Hub credentials.
11. You should see a new Network Drive under your Computer/This PC. Double click on it to open.

You should now be able to drag files and folders between your computer and the hub via the network drive to which you just connected.

## Windows 8.x

The **WebClient Services** to connect to a WebDAV Servers, by default, the WebClient service is disabled, so we need to enable it.

1. From the Start menu, choose Control Panel, then System and Security, then Administrative Tools, and then Services.
2. Scroll down to WebClient, set the service to Automatic, and then click Apply.
3. If the service is not already running, click Start.
4. Click OK to close the Control Panel and close other windows.

To set up a WebDAV connection in Windows 8.x:

1. Using the Search interface in tile mode, locate and select the Computer tile.
2. In the quick menu at the top of the screen, click Map Network Drive.
3. In the "Folder" field, enter a URL that points to the destination hub similar to the following URL "`https://webdav.hubname.org/webdav/" (open source: https://hubname.org/webdav/)`. Replace "hubname.org" with the URL of the destination hub.
4. Select the Connect using different credentials box, and then click Finish.
5. When prompted, enter your Hub credentials.
6. You should see a new Network Drive under your Computer/This PC. Double click on it to open.

You should now be able to drag files and folders between your computer and the hub via the network drive to which you just connected.

## Windows 7

The **WebClient Services** to connect to a WebDAV Servers, by default, the WebClient service is disabled, so we need to enable it.

1. From the Start menu, choose Control Panel, then System and Security, then Administrative Tools, and then Services.
2. Scroll down to WebClient, set the service to Automatic, and then click Apply.
3. If the service is not already running, click Start.
4. Click OK to close the Control Panel and close other windows.

To set up a WebDAV connection in Windows 7.

1. From the Start menu, right-click Computer, and select Map network drive.
2. Enter a URL that points to the destination hub similar to the following URL "`https://webdav.hubname.org/webdav/" (open source: https://hubname.org/webdav/)`. Replace "hubname.org" with the URL of the destination hub. Clink finish.
3. When prompted, enter your Hub credentials.
4. You should see a new Network Drive under your Computer/This PC. Double click on it to open.

You should now be able to drag files and folders between your computer and the hub via the network drive to which you just connected.

If you have difficulty dragging and dropping, right-click the file or folder you want to copy, and choose Copy. Then right-click the directory you want to put it in, and choose Paste.
