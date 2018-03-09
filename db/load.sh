#!/bin/sh

BASE_DIR=$(dirname $(readlink -f "$0"))
if [ "$1" != "test" ]
then
    psql -h localhost -U educantes -d educantes < $BASE_DIR/educantes.sql
fi
psql -h localhost -U educantes -d educantes_test < $BASE_DIR/educantes.sql
