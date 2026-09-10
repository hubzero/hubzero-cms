<!--
status: reviewed
reviewed-against: 2.4-main @ e097e0236d
reviewed: 2026-09-10
screenshots: none
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/jupyter-notebooks/invoke-jupyter
source-id: 3562
modified: 2022-11-22
imported: 2026-09-09
-->
# Invoke scripts for Jupyter notebooks

Every tool has an invoke script in its `middleware` directory, and the one the
pipeline creates launches a Rappture or Linux-GUI tool. A Jupyter tool needs
it replaced. This page gives the script for each of the three
[deployment styles](01-jupyter-deployment-styles.md), and what the arguments
mean.

> **Important:** `invoke_app` and `start_jupyter` are programs on the tool
> execution platform, which is separate software and is **not in this
> repository**. The scripts and argument lists below are the written record
> carried over from the platform documentation, last revised in 2022, and
> could not be checked here. Run `start_jupyter --help` on your own hub before
> relying on a flag. For invoke scripts in general, see
> [Launching tools with invoke scripts](../03-invoke.md).

## The shape of it

`invoke_app` sets up the session, and the command it is given starts the
notebook server. For a Jupyter tool that command is `start_jupyter`:

```sh
/usr/bin/invoke_app "$@" -t TOOLNAME \
                         -C "start_jupyter -T @tool APP.ipynb" \
                         -r none \
                         -w headless \
                         -u anaconda-X
```

- `TOOLNAME` is the tool's short name, the one you registered.
- `APP.ipynb` is the notebook that runs the tool.
- `anaconda-X` is the Anaconda installation your hub deploys. Ask which
  version, rather than copying a number out of documentation.
- `@tool` stands for the installed tool's own directory, which is where
  `-T` starts looking for the notebook.

That script gives Notebook style: every code cell visible.

## Arguments

`invoke_app`, in the flags a notebook tool uses:

| Flag | Meaning |
|---|---|
| `-t` | Tool name |
| `-C` | Command to run |
| `-r` | Rappture version. Notebook tools pass `none` |
| `-w` | Window manager. Notebook tools pass `headless` |
| `-u` | Environment package to load. Repeat for more than one |

`start_jupyter`:

| Flag | Meaning |
|---|---|
| `-h`, `--help` | Show the help |
| `-d` | Verbose output, for debugging |
| `-t` | Tool style: no notebook controls |
| `-c` | Copy the notebook files instead of linking them |
| `-A` | App style |
| `-T dir` | Start looking for the notebook in `dir` |
| `--themes` | Enable notebook themes |

> **Warning:** `-t` means one thing to `invoke_app` and another to
> `start_jupyter`. On `invoke_app` it names the tool; on `start_jupyter` it
> hides the code cells. `-A` differs between the two as well. Check which
> command a flag is attached to before you change it.

## App style

Code cells hidden, with an **Edit App** button to reveal them:

```sh
/usr/bin/invoke_app "$@" -t TOOLNAME \
                         -C "start_jupyter -A -T @tool APP.ipynb" \
                         -u anaconda-X \
                         -w headless \
                         -r none
```

## Tool style

Code cells hidden, with no way to reveal them:

```sh
/usr/bin/invoke_app "$@" -t TOOLNAME \
                         -C "start_jupyter -A -t -T @tool APP.ipynb" \
                         -u anaconda-X \
                         -w headless \
                         -r none
```

## Common errors

**`could not find a rappture installation: RAPPTURE_PATH=,`**

`invoke_app` looked for Rappture because the script did not tell it not to.
Add `-r none` to the `invoke_app` call — no quotation marks — and run it
again.

Rappture itself is deprecated; a Jupyter notebook is the current path for a
new tool on most hubs. `-r none` stays necessary all the same, because
`invoke_app` still looks for Rappture unless told otherwise.

## Testing the script

Run it by hand before you flag the code as ready:

```bash
cd ~/apps/toolname/middleware
./invoke
```

[Testing a Jupyter tool](03-testing-jupyter.md) goes through what the output
should say and how to open the notebook it starts.

Notebooks that dispatch work to a cluster call `submit` from a cell rather
than from the invoke script — see
[Jupyter integration with submit](../05-grid/04-jupyter_submit.md).
