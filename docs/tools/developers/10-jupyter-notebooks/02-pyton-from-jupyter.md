<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/jupyter-notebooks/pyton-from-jupyter
source-id: 3559
modified: 2022-11-22
imported: 2026-09-09
-->
# Using Python packages from Jupyter notebooks

Which Python packages a notebook can import depends on the kernel it runs
under. A kernel is a prepared environment installed on the hub, and a notebook
remembers the one you choose, so the choice travels with the notebook when it
becomes a tool.

> **Important:** Kernels, conda environments and the packages in them all live
> on the tool execution platform, which is separate software and is **not in
> this repository**. Nothing on this page could be checked against code here.
> It is the written record carried over from the platform documentation,
> corrected where it named software versions that are long gone.

## Kernels

The hub installs Python packages into conda environments and registers each
environment as a Jupyter kernel. Selecting a kernel points the notebook at
that environment, and its packages become importable.

You cannot install a kernel yourself: creating an environment and registering
it is an administrator's job, done on the execution hosts. If the packages you
need are not in any kernel, open a support ticket on your hub and ask for
them. Say which packages, which versions if it matters, and what you are
building. The procedure the administrator follows is
[Jupyter notebooks](../../administrators.md#jupyter-notebooks) in the
administrators section; reading it tells you what you are asking for, and why
adding a package to a shared environment is not always the answer.

> **Note:** A kernel is not a substitute for declaring your tool's
> dependencies. A tool that will be installed on the hub still needs its
> requirements agreed with the hub's staff — see
> [Installing tool dependencies](../../administrators.md#installing-tool-dependencies).

## Selecting a kernel

For a new notebook, pick the kernel when you create the notebook: the launcher
lists one entry per kernel the hub has installed. Save the notebook afterwards,
so the choice is recorded in it.

For a notebook that already exists:

1. Open it in the hub's Jupyter tool.
2. Shut the running kernel down first — **Kernel > Shutdown**.
3. Choose **Kernel > Change kernel** and pick the one you want.
4. Check the kernel name shown in the notebook's corner. It should be the one
   you chose.
5. Save the notebook.

The exact menu wording follows whichever Jupyter version your hub deploys.

## Finding out what a kernel contains

Two ways: read the environment file the administrator built the kernel from,
or ask conda directly. For the second, start the hub's workspace tool, open a
terminal, and put the hub's Anaconda installation on your path:

```bash
use anaconda-X
```

`X` is the version your hub deploys; `use |& grep anaconda` lists what is
installed. Then list the environments:

```bash
conda info --envs
```

> **Note:** Not every environment has a kernel registered for it. An
> environment missing from the notebook's kernel list is one to ask about
> rather than one to assume is broken.

List the packages in the environment currently active:

```bash
conda list
```

Or in any other environment:

```bash
conda list -n <envname>
```

To capture an environment as a file — useful for a ticket, or for recording
what your tool was built against:

```bash
conda activate <envname>
conda env export > <envname>.yml
```

## Further reading

- [Managing conda environments](https://docs.conda.io/projects/conda/en/latest/user-guide/tasks/manage-environments.html)
- [The Anaconda package repository](https://anaconda.org/anaconda/repo)
