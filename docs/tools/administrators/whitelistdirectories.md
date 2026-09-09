<!--
status: imported
source: https://help.hubzero.org/documentation/platform_2_4/tool-administrators/whitelistdirectories
source-id: 3566
modified: 2018-10-24
imported: 2026-09-09
-->
# White list of directories through the CMS

The parameter passing tool execution feature provides a mechanism for passing file names to a tool through the URL used to launch the tool session. As a security measure the passed parameters must conform to a simple schema and only files in white listed directories may be referenced. The passed parameters are validated by the middleware. The files passed as parameters are restricted to the directories listed in the **Directory Parameter Whitelist.** If specified files lie outside of the white listed locations the the tool launch will fail.

Any super-user can whitelist a directory via the /administrator interface. Follow these steps to complete this request:

1. Navigate to the **/administrator** interface and login
2. Hover over **Components** then click on **Tools**
3. Click the **Options** button and under **Directory Parameter Whitelist**, modify the comma separated list of directories. At minimum the list should include /home.
4. Click **Save & Close**
5. Make sure that you complete this task on all the requested systems (i.e. production, stage, dev, qa, etc.)
