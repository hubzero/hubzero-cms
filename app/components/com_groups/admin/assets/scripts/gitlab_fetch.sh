#!/bin/bash

# change directory to group folder
cd $1

# pull from the remote
UPDATE=$(php $2/cli/muse.php group update --format=json 2>&1)
echo ${UPDATE}




