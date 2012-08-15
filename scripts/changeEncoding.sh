#/bin/bash
#
# iconv-inplace.sh
# Does recursive charset conversion using iconv
#
# Copyright (c) 2009 Onlime Webhosting, Philip Iezzi
#                    http://www.onlime.ch
 
###### Configuration ######
#FROM_CHARSET="ASCII"
FROM_CHARSET="ISO-8859-1"
TO_CHARSET="UTF-8"
###########################
 
# Validate args
STARTDIR="$1"
if [ -z "$STARTDIR" ]
then
    echo "Usage: $0 <directory>"
    echo "where: <directory> is the directory to start the recursive UTF-8 conversion."
    exit 1
fi
 
LIST=`find $1 -name "*.php"`
for i in $LIST;
do
    file $i
    read -p "Convert $i (y/n)? "
    if [ "$REPLY" == "y" ]
    then
        iconv --from-code=$FROM_CHARSET --to-code=$TO_CHARSET $i > $i."utf8";
        mv $i."utf8" $i;
    fi
    echo "";
done
