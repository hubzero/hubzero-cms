update_status () { if [ $1 -ne 0 ]; then status=$1; fi }
status=0
php_args=`echo $@ | grep \.php$`
if [ -z "$php_args" ]; then exit 0; fi

vendor/squizlabs/php_codesniffer/scripts/phpcs -np --standard=~/build/standards/Php/ruleset.xml --ignore=*/tests/*,/core/libraries/*,/core/bin/* $php_args
update_status $?
for file in $php_args; do
	php -l $file 2>&1 1>temp
	update_status $?
done
sed "/No syntax errors detected/d" -i temp
cat temp
rm temp
exit $status
