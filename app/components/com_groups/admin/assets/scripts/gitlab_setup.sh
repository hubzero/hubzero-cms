#!/bin/bash

# change directory to group folder
cd $1

# init git repo
git init

# tell repo to always pull from master
git config --add branch.master.remote origin
git config --add branch.master.merge refs/heads/master

# add items for git to ignore
echo '.DS_Store' >> .git/info/exclude
echo 'uploads/*' >> .git/info/exclude
echo 'config/db.php' >> .git/info/exclude

# add all files to git repo
git add *

# make initial commit
git commit -m "Initial Commit" --author="$2"

# set remote host
git remote add origin $3

# push changes
git push -u origin master