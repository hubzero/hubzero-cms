<!--
status: generated
source: core/components/com_jobs/config/config.xml
-->

# Jobs (com_jobs)

Manage job postings and job seekers

Parameters from [`core/components/com_jobs/config/config.xml`](../../../../core/components/com_jobs/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `component_enabled` | Enable this component? | list | `1 (Yes)` | Turn the whole component on/off. Options: `1` Yes, `0` No. |
| `industry` | Industry name | text | — | Name of industry where jobs are posted |
| `admingroup` | Admin group | text | — | Name of a HUB group to have unrestricted access to job postings and resumes, in addition to site administrators |
| `specialgroup` | Special services group | text | — | Name of a HUB group to have access to restricted-level subscriptions (usually above Premium) |
| `autoapprove` | Auto approve job postings | list | `1 (Yes)` | Auto approve job postings from employer with a valid subscription. Options: `1` Yes, `0` No. |
| `jobslimit` | Limit jobs per page | text | `25` | Specify how many jobs to show on one page by default |
| `allowsubscriptions` | Allow user subscriptions? | list | `1 (Yes)` | Allow users to subscribe to employer services and post jobs and browse resumes. Options: `1` Yes, `0` No. |
| `usonly` | US jobs only? | list | `1 (allow only US jobs)` | Allow to post only US-based jobs. Options: `1` allow only US jobs, `0` allow jobs from any country. |
| `expiry` | Posting Expiration Date | list | `1 (On - 180 day expiration)` | Expiration date for posts. When enabled, each post defaults to expire in 180 days, although this is able to be changed from the default. Options: `1` On - 180 day expiration, `0` Disabled. |
