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

# clone git repo
git clone $4;

# move files
mv $3/* $3/.git* .; rmdir $3;

# add items for git to ignore
echo '.DS_Store' >> .git/info/exclude
echo 'uploads/*' >> .git/info/exclude
echo 'config/db.php' >> .git/info/exclude