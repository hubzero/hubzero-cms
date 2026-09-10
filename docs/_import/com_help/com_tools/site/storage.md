<!--
status: imported
source: core/components/com_tools/site/help/en-GB/storage.phtml
imported: 2026-09-09
-->
# Tool Storage

1. [How to use WebDAV to access your storage](#webdav)

<a id="webdav"></a>

## How to use WebDAV to access your storage

WebDAV is the Distributed Authoring and Versioning extension to the standard HTTP/HTTPS web protocol. It allows a client to browse a remote filesystem, usually with a graphical browser that makes it appear that your files are on your desktop. You may access your storage using only the secure version of this service (HTTPS). We do not support HTTP. Most modern computer platforms support HTTPS transport for WebDAV with either small adjustments or freely available software.

## Linux/Unix

If you use the KDE graphical desktop environment, you can access your hub storage with the Konqueror browser by typing the special URL webdavs://webdav.example.hub/ into the Location field of the browser. It will prompt you for your hub login and password. Thereafter, you traverse your home directory by clicking on folders and you can drag and drop files to your desktop.

[Cadaver](http://www.webdav.org/cadaver/) is a text-mode WebDAV browser. It can be used if it is compiled with SSL support. Invoke it with the command cadaver https://webdav./ and it will prompt you for your hub login and password. You can then use it in a manner similar to FTP. If you are using Linux, you can use the [davfs](http://dav.sourceforge.net/) kernel module to mount your hub storage area as a local filesystem.

## Mac OSX

MacOS versions 10.4 and higher support HTTPS transport for WebDAV using the Finder. To access your storage directory, select the Go menu in the Finder and choose "Connect to Network Server". Enter the URL http://webdav.example.hub/webdav into the address field. You will be prompted for your login and password. MacOS versions lower than 10.4 do not have support for HTTPS, but a free client exists. Download Goliath and install it on your system. Invoke it and use http://webdav.example.hub/webdav as the address to connect to. Note that you may also use Goliath on newer MacOS platforms as well.

## Windows

Windows XP systems have native support for accessing WebDAV storage via HTTPS, but it may be disabled by default depending on how your OS was configured. Try these instructions first, and then check the next set of instructions to enable WebDAV. To access your hub storage,

- Double-click on the desktop icon called "My Network Places".
- This will invoke a dialog box where you can select "Add Network Place". This will invoke a wizard that you can step through.
- Select the "Choose another network location" function.
- The wizard will then ask you for the "Internet or Network Address." In the box, type https://webdav.example.hub/
- Specify the name that you would like to have associated with this storage space. An icon will be created for the hub storage folder. When you double-click on it to open it, you will be prompted for your hub login and password.

### Enabling WebDAV in Windows XP

If the steps above do not work, for Windows XP, or you cannot find the named options, your OS may have WebDAV turned off by default. Do the following additional steps and then try to add network storage again.

- Open Windows Explorer
- Go to the "Tools" menu and select "Folder Options"
- Select the radio button "Use Windows classic folders" under "Tasks".

### Login case sensitivity

Some users of Windows WebDAV clients have reported problems that occur when they use uppercase characters for their login. Always type your login using all lowercase characters.

### Windows 2003 users cannot access secure WebDAV

There is no support for secure WebDAV transport in Windows 2003. Various workarounds have been suggested, but it appears the easiest action to take is to simply upgrade the OS.

### Windows Vista users cannot access secure WebDAV

A default and up to date installation of Windows Vista has no support for secure WebDAV. You'll need to install the following [patch](http://support.microsoft.com/kb/907306).

### Windows XP users cannot change credentials for WebDAV authentication.

To clear previously entered authentication data from the credential cache, use the following menu selection: Start -> Settings -> Control Panel -> User Accounts -> Change an Account Then select the User account for which you wish to clear the stored password. Click on 'Manage My Network Passwords', highlight the mapped drive or WebDAV account and click 'Remove'.
