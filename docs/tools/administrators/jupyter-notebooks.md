<!--
status: reviewed
reviewed-against: 2.4-main @ be0bd4c772
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tool-administrators/jupyter-notebooks
source-id: 3565
modified: 2018-09-06
imported: 2026-09-09
-->
# Jupyter notebooks

Administering the Anaconda environments that Jupyter tools run in, and the
one setting on the CMS side that lets a tool be published as one.

The first section below was checked against `com_tools` in this repository.
Everything after it happens on the tool execution platform, which is separate
software and is **not in this repository**; those procedures are kept as the
written record but could not be verified.

## The CMS side

`com_tools` has an **Enable Jupyter** option, on by default. While it is on,
the tool registration form offers a third publishing choice, **Web
application (Jupyter, Rstudio, ...)**, alongside the Rappture or Linux-GUI
default and Sim2L. Turning the option off removes the choice from the form;
tools already registered with it keep their setting.

The choice is stored on the tool version as `publishType`, with the value
`jupyter`. Hubs upgraded from older releases may still hold the historical
value `weber=`; the component translates that to `jupyter` when it reads it,
so both behave the same. `publishType` is passed straight through to the
platform's install and publish scripts as `--publishOption`.

At launch the difference shows up in what the middleware returns. A standard
tool comes back as a VNC session that the `novnc` plugin renders in the page.
A Jupyter tool comes back with a proxy URL, and the CMS redirects the browser
to it instead. If the middleware also returns an authentication token, the
CMS sets a `weber-auth-<hub domain>` cookie — secure, HttpOnly, thirty days —
before redirecting.

For the option itself see the
[generated `com_tools` parameter reference](../../reference/configuration/components/tools.md);
for the pipeline the tool travels through, see
[Tools](../../managers/03-maintenance/02-tools.md) in the hub managers book.

Nothing else about Jupyter is configured in the CMS. Which Anaconda
environments exist, which packages they carry, and which kernels a notebook
can select are all decided on the execution hosts, by the procedures below.

## Adding packages to an Anaconda environment

> **Warning:** An Anaconda environment is shared by every tool that uses it.
> Adding a package can force other packages to be downgraded, and a downgrade
> can break a tool that depends on a feature the older version lacks. Read
> what conda proposes before agreeing to it. A minor downgrade is usually
> safe; a long list of downgrades, or a downgrade of anything central, is
> not — stop and create a separate environment instead.

Check the package's own installation instructions first. Many recommend a
channel other than the default.

1. Start a workspace tool. If the hub offers workspaces on more than one
   container image, pick the one matching the environment you are changing.
   Starting the Jupyter tool that uses the environment and opening a terminal
   in it works too.

2. Become the `apps` user. Your account has to be in the host's `apps` group.

   ```bash
   sudo su - apps
   ```

3. List the Anaconda environments available.

   ```bash
   use |& grep anaconda
   ```

4. Load the one you want, where `X` is the version.

   ```bash
   use -e -r anaconda-X
   ```

5. Install the package. Use `mamba` in place of `conda` if it is present — it
   is faster and says more when it fails. `pip` is the third option; mixing
   pip and conda installations in one environment needs care. Installation
   can take several minutes.

   ```bash
   conda install -c conda-forge -c defaults <pkgname>
   ```

   ```bash
   pip install -U --upgrade-strategy only-if-needed <pkgname>
   ```

6. Remove any world-writable bits the installer left behind. Do not skip
   this.

   ```bash
   chmod -R o-w /apps/share64/<os>/anaconda/anaconda-X
   ```

## Creating a separate Anaconda environment

Sometimes it is better to spawn a new named environment from the base one
than to disturb the base. Jupyter calls named environments *kernels*. A
notebook records its kernel in its own metadata, so a developer picks it once
and it sticks — which means the administrator does not have to create a
separate Jupyter tool for every kernel.

1. Load the base environment.

   ```bash
   use -e -r anaconda-X
   ```

2. Create the named environment.

   ```bash
   conda create -n <name>
   ```

3. Activate it. On current conda this is `conda activate <name>`; older
   installations need `source activate <name>`.

   ```bash
   conda activate <name>
   ```

4. Install packages as above.

   ```bash
   conda install -c conda-forge -c defaults <pkgname>
   ```

   ```bash
   pip install -U --upgrade-strategy only-if-needed <pkgname>
   ```

5. Register it as a kernel, either into the active environment's prefix or
   explicitly into the base environment's.

   ```bash
   python -m ipykernel install --sys-prefix --name <name> --display-name "Python3 (<name>)"
   ```

   ```bash
   python -m ipykernel install --prefix /apps/share64/<os>/anaconda/anaconda-X --name <name> --display-name "Python3 (<name>)"
   ```

6. Deactivate.

   ```bash
   conda deactivate
   ```

Fix world-writable files here too, as in the previous section.

Further reading:

- [Managing conda environments](https://docs.conda.io/projects/conda/en/latest/user-guide/tasks/manage-environments.html)
- [Installing the IPython kernel](https://ipython.readthedocs.io/en/stable/install/kernel_install.html#kernels-for-different-environments)

## Updating hublib

`hublib` is the Hubzero utility library for notebooks. It belongs in every
base Anaconda environment and in every named environment spawned from one. It
is distributed through pip only.

```bash
use -e -r anaconda-X
pip install -U hublib
```
