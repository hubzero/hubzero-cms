<!--
status: imported
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/toolrepostructure
source-id: 3535
modified: 2022-01-27
imported: 2026-09-09
-->
# Tool Repository Structure

The directory structure for tool repositories should start from the following:

```
      toolname/
         bin/
            .keep
         data/
            .keep
         doc/
            .keep
         examples/
            .keep
         middleware/
            invoke
         rappture/
            .keep
         simtool/
            TOOLNAME.ipynb
         src/
            Makefile
```

The .keep files are present to force git to track initially empty directories. Should the directories become populated the .keep file can be removed. The `simtool` directory should be present only if the tool was registered with the publication option = Sim2L. The `rappture` directory is only required for tools actually using Rappture. The `src` directory should contain any source code that needs to be compiled and a `Makefile` to direct building of executables. The `Makefile` must have the `install`, `clean`, and `distclean` targets. The `bin` directory should contain any executables created by issuing the `make install` command in the `src` directory. The `bin` directory is automatically added to the PATH environment variable used to search for executable files. It is common practice to put Jupyter notebook files in either the top level or `bin` directories. It is also permissible to create additional directories as deemed necessary.

## Invoke script template

All tools require that the executable file middleware/invoke be present. The specifics of the invoke file depend on the tool publication classification. Templates for the three categories of tools are shown here. For tools where the repository is maintained on the HUB known values such as tool name are substituted in the template to provide an initial invoke file.

## Sim2L

```
#!/bin/sh

#
# Sim2L
#
/usr/bin/invoke_app "$@" -t @TOOLNAME@ \
                         -C "start_jupyter -T @tool -t @TOOLNAME@Example.ipynb" \
                         -u anaconda-X \
                         -r none \
                         -w headless
```

## Jupyter Notebook

```
#!/bin/sh

#
# jupyter tool
#
/usr/bin/invoke_app "$@" -t @TOOLNAME@ \
                         -C "start_jupyter -T @tool -t @TOOLNAME@.ipynb" \
                         -u anaconda-X \
                         -r none \
                         -w headless
```

## Rappture/Linux GUI

```
#!/bin/sh

#
# standard tool
#
/usr/bin/invoke_app "$@" -t @TOOLNAME@ \
                         -C rappture
```
