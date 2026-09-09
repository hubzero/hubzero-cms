<!--
status: imported
source: https://help.hubzero.org/documentation/platform_2_4/tooldevs/jupyter-notebooks/jupyter-deployment-styles
source-id: 3558
modified: 2022-11-22
imported: 2026-09-09
-->
# Jupyter Tool Deployment Styles

## Jupyter tool deployment styles

How do you want your Jupyter Notebook-based tool to behave when a user runs it? You have several choices, which we describe here:

1. Tool or App Mode style--code cells are hidden, UI widgets are visible
2. Notebook style--all code cells are shown. This is the default.

If you're working out how to develop and deploy the notebook, please refer to the other articles in this series. Continue reading to choose and set the tool display style.

#### Setting App Mode

You can achieve the dashboard-style effects by just selecting the App Mode in the Jupyter menu bar. Toggle back to edit your notebook using the Edit App button.

![appmode button](https://theghub.org/app/site/media/images/knowledgebase/appmode-button.png)

This screenshot shows the Jupyter tool menu bar, with its *Appmode* button

#### Deploying as App Mode

Then, to deploy the tool in App Mode, specify an -A in your invoke script, as follows:

```
/usr/bin/invoke_app "$@" -t toolname \
                         -C "start_jupyter -A -T @tool MyNotebook.ipynb" \
	                 -u anaconda-X \
                         -w headless \
                         -r none
```

The tool user will still be able to toggle the tool to show its code cells. To suppress that behavior, add a -t to the start_jupyter call.

```
/usr/bin/invoke_app "$@" -t toolname \
                         -C "start_jupyter -A -t -T @tool MyNotebook.ipynb" \
	                 -u anaconda-X \
                         -w headless \
                         -r none
```
