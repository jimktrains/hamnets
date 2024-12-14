#!/usr/bin/env bash

SQLFILE=hamnets-`date +%Y-%m-%d-%H-%M`.sql
S3PREFIX=s3://jsk-backup/hamnets/db/`date +%Y/%m/%d`

set -Eeuo pipefail
trap cleanup SIGINT SIGTERM ERR EXIT

script_dir=$(cd "$(dirname "${BASH_SOURCE[0]}")" &>/dev/null && pwd -P)

cleanup() {
  trap - SIGINT SIGTERM ERR EXIT
  rm $SQLFILE
}

pg_dump --exclude-table=gadm36 --exclude-table=gadm_410 hamnets > $SQLFILE
s3cmd put $SQLFILE $S3PREFIX/$SQLFILE
