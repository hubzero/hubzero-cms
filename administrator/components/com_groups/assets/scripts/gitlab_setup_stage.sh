#!/bin/bash

# change directory to groups
cd $1

# timestamp
now=$(date +"%m%d%Y")

# create a backup of the group folder
tar -zcf "$2_$now.tar.gz" "$2"

# change directory to group folder
cd $2

# remove contents of folder
rm -R -- *

# init git repo
git init

# set remote host
git remote add origin $3

# pull in latest code
git pull --rebase origin master

# set tracking branch
git branch --set-upstream master origin/master