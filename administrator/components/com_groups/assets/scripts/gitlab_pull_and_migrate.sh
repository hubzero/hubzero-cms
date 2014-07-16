#!/bin/bash

# change directory to group folder
cd $1

# echo header
echo "Pulling Code\n------------------------------"

# pull from the remote
git pull --rebase origin master

# echo migrate header
echo "\n\nRunning Migrations\n------------------------------\n"

# run muse migration
php $2/cli/muse.php migration --group=$3 -f --no-colors