#!/bin/sh

if [ "$1" = "travis" ]
then
    psql -U postgres -c "CREATE DATABASE educantes_test;"
    psql -U postgres -c "CREATE USER educantes PASSWORD 'educantes' SUPERUSER;"
else
    [ "$1" != "test" ] && sudo -u postgres dropdb --if-exists educantes
    [ "$1" != "test" ] && sudo -u postgres dropdb --if-exists educantes_test
    [ "$1" != "test" ] && sudo -u postgres dropuser --if-exists educantes
    sudo -u postgres psql -c "CREATE USER educantes PASSWORD 'educantes' SUPERUSER;"
    [ "$1" != "test" ] && sudo -u postgres createdb -O educantes educantes
    sudo -u postgres createdb -O educantes educantes_test
    LINE="localhost:5432:*:educantes:educantes"
    FILE=~/.pgpass
    if [ ! -f $FILE ]
    then
        touch $FILE
        chmod 600 $FILE
    fi
    if ! grep -qsF "$LINE" $FILE
    then
        echo "$LINE" >> $FILE
    fi
fi
