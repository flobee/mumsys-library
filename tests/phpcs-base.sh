#!/bin/sh

echo "----------------------------------------";
echo "usage: $0 [phpcs options] <path or file>";
echo "----------------------------------------";

# ../vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/

STANDARD="--standard=${CUR_DIR}/../misc/coding/Mumsys"
IGNORELINE='--ignore=data/*,vendor/*,helper/*,tmp/*,misc/*'
PHP_BIN='php7.3'
