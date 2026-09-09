<!--
status: generated
source: core/components/com_tools/config/config.xml
-->

# Tools (com_tools)

Parameters from [`core/components/com_tools/config/config.xml`](../../../../core/components/com_tools/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `contribtool_on` | Contribtool | radio | `1 (ON)` | Indicate whether the component is active or not. Options: `0` OFF, `1` ON, `2` ON (admin only). |
| `contribtool_redirect` | Redirect | text | `/home` | The URL to redirect to when the component is OFF |
| `admingroup` | Admin Group | text | `apps` | Name of contribtool admin group |
| `storagehost` | Storage Host | text | — | The host the storage indicator should use |
| `show_storage` | Show Storage | list | `1 (Yes)` | Select whether to show the storage meter or not. Options: `1` Yes, `0` No. |
| `params_whitelist` | Directory Parameter Whitelist | text | `/home` | White-list of directories acceptable to include in tool parameter passing feature |
| `github` | External GitHub Repo | list | `1 (Yes)` | Offer an option to pull code from a remote GitHub repository. Options: `1` Yes, `0` No. |
| `jupyter` | Enable Jupyter | list | `1 (Yes)` | Offer an option to publish as a Jupyter notebook. Options: `1` Yes, `0` No. |
| `simtool` | Enable Sim2L | list | `1 (Yes)` | Offer an option to publish as a Sim2L. Options: `1` Yes, `0` No. |

## Middleware

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `mw_on` | Middleware | list | `0 (OFF)` | Indicate whether the component is active or not. Options: `0` OFF, `1` ON, `2` ON (admin only). |
| `zones` | Zones | radio | `0 (OFF)` | Allow Tool Session Zones. Options: `0` OFF, `1` ON. |
| `mw_redirect` | Redirect | text | `/home` | The URL to redirect to when middleware is OFF |
| `mwDBDriver` | Middleware DB Driver | text | — | Middleware DB Driver |
| `mwDBHost` | Middleware DB Host | text | — | Middleware DB Host |
| `mwDBPort` | Middleware DB Port | text | — | Middleware DB Port |
| `mwDBUsername` | Middleware DB Username | text | — | Middleware DB Username |
| `mwDBPassword` | Middleware DB Password | password | — | Middleware DB Password |
| `mwDBDatabase` | Middleware Database | text | — | Middleware Database |
| `mwDBPrefix` | Middleware DB Prefix | text | — | Middleware DB Prefix |

## Sessions

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `stopRedirect` | Session Stop Redirect | text | `index.php?option=com_members&task=myaccount` | The URL to redirect to after stopping a session |
| `shareable` | Shareable | radio | `1 (ON)` | Allow tool sessions to be shared. Options: `0` OFF, `1` ON. |
| `warn_multiples` | Warn User of Multiples | radio | `0 (OFF)` | Warn the user when starting another instance of a tool. Options: `0` OFF, `1` ON. |
| `launch_ipad` | Launch on iPad | list | `0 (No)` | Launch tool sessions on iPad?. Options: `0` No, `1` Yes. |
| `launch_ipad_app` | iPad app name | text | — | iPad app name |

## Tool

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `default_mw` | Default Middleware | text | `narwhal` | Name of default middleware |
| `default_vnc` | Default VNC Size | text | `780x600` | Default VNC geometry values |
| `default_hostreq` | Default Required Host Types | text | `sessions` | Default required session host types for new tools (comma seperated) |
| `developer_site` | Developer Site | text | `FORGE` | Name of project development site |
| `project_path` | Path to Projects | text | `/tools/` | Path to projects on development site (usually followed by the tool alias) |
| `invokescript_dir` | Invoke Script Dir | text | `/apps` | Directory for invoke script |
| `dev_suffix` | Dev Tool Suffix | text | `_dev` | Suffix indicating the development instance of a tool (e.g. _dev for toolname_dev) |
| `group_prefix` | Dev group prefix | text | `app-` | Prefix to name of development group (e.g. app-) |
| `sourcecodePath` | Source code path | text | `site/protected/source` | Source code path. |
| `learn_url` | Learn More URL | text | `http://rappture.org/wiki/FAQ_UpDownloadSrc` | URL to a uploading source tutorial |
| `rappture_url` | Rappture URL | text | `http://rappture.org` | URL to a rappture tutorial |
| `demo_url` | Demo URL | text | — | URL to demo explaining contribution process |
| `exec_pu` | Include PU | radio | `1 (Yes)` | Include Purdue campus as a tool access restriction. Options: `0` No, `1` Yes. |
| `screenshot_edit` | Edit Screenshots | radio | `0 (OFF)` | Allow editing of screenshots via contribtool and display of screenshots for individual versions. Options: `0` OFF, `1` ON. |
| `downloadable_on` | Allow Downloadables | radio | `0 (No)` | Include an option to create a downloadable tool. Options: `0` No, `1` Yes. |

## Doi

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `new_doi` | Enable DOI service? | radio | `0 (No)` | Register DOI handle for new tool releases with specified DOI service. Options: `0` No, `1` Yes. |
| `doi_service_switch` | DOI service | list | `1 (EZID)` | Select the DOI service to use for minting DOis. Options: `0` None, `1` EZID, `2` DataCite. |
| `doi_newservice` | DOI service path | text | — | URL for DOI service |
| `doi_userpw` | DOI Service User/Password | text | — | DOI Service User/Password |
| `doi_shoulder` | DOI shoulder | text | — | First part of DOI namespace (what goes right after doi: and before /, e.g. 10.5072 ) |
| `doi_newprefix` | DOI handle prefix | text | — | Hub-specific DOI namespace end (usually 2-3 characters going after /, e.g. F2K) |
| `doi_xmlschema` | DOI XML Schema | text | — | URL of XML schema to validate against |
| `doi_publisher` | DOI publisher | text | — | Publisher name (use full site name) for DOI service |
| `doi_resolve` | DOI resolve url | text | `https://doi.org/` | URL for resolving DOIs |
| `doi_verify` | DOI verify url | text | `http://n2t.net/ezid/id/` | URL for verifying DOIs |

## Windows

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `windows_key_id` | Access Key ID | text | — | Amazon Web Services (AWS) Access Key ID |
| `windows_secret_key` | Secret Access Key | text | — | Amazon Web Services (AWS) Secret Access Key |
| `windows_type` | Resource Type | resourcetype | — | Resource type to use for tool information |
| `windows_monthly_max_hours` | Max Monthly Hours | text | — | Max hours of windows applicaiton sessions per month |
