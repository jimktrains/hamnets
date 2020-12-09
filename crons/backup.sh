#!/bin/sh

SQLFILE=hamnets-`date +%Y-%m-%d-%H-%M`.sql
S3PREFIX=s3://jsk-backup/hamnets/db/`date +%Y/%m/%d`

pg_dump --exclude-table=gadm36 hamnets > $SQLFILE
s3cmd put $SQLFILE $S3PREFIX/$SQLFILE
