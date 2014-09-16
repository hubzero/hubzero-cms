#!/bin/bash

# change directory to group folder
cd $1

# echo header
echo "Pulling Code\n------------------------------\n"

# pull from the remote
UPDATE=$(php $2/cli/muse.php group update --no-colors 2>&1)
echo ${UPDATE}

# check to see if the update failed
if echo "${UPDATE}" | grep -q ineligible 2>&1;
	then
		MIGRATE='Refusing to run migrations due to failed update.'
	else
		# run muse migration
		MIGRATE=$(php $2/cli/muse.php group migrate -f --no-colors 2>&1)
fi

# echo migrate header & result
echo "\n\nRunning Migrations\n------------------------------\n"
echo ${MIGRATE}



