#!/usr/bin/env bash
# Copy table structure (no rows) from an existing Sparkl DB into another database.
#
# The app DB user often cannot CREATE DATABASE — create the target DB first, e.g.:
#   mysql -h127.0.0.1 -u root -p -e "CREATE DATABASE sparkl_test_empty CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; GRANT ALL ON sparkl_test_empty.* TO 'paul_bulku'@'127.0.0.1'; FLUSH PRIVILEGES;"
#
# Then run (defaults match Sparkl/live/_connection.php dev):
#   export MYSQL_PWD='your-app-user-password'
#   ./scripts/copy-schema-to-empty-db.sh sparkl_test_empty
#
# Use the app against the empty DB:
#   SPARKL_DB_NAME=sparkl_test_empty php -S 0.0.0.0:8080 -t live live/index.php
#
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

TARGET_DB="${1:-}"
if [[ -z "$TARGET_DB" ]]; then
  echo "Usage: $0 <target_database_name>" >&2
  exit 1
fi

HOST="${MYSQL_HOST:-127.0.0.1}"
SRC_DB="${MYSQL_SRC_DB:-paul_bulkdb}"
USER="${MYSQL_USER:-paul_bulku}"

if [[ -z "${MYSQL_PWD:-}" ]]; then
  echo "Set MYSQL_PWD to the password for MYSQL_USER (or export MYSQL_USER / MYSQL_HOST)." >&2
  exit 1
fi

echo "Dumping schema from ${SRC_DB} (no data)..."
DUMP=$(mktemp)
trap 'rm -f "$DUMP"' EXIT

mysqldump -h"$HOST" -u"$USER" "$SRC_DB" \
  --no-data \
  --no-tablespaces \
  --set-gtid-purged=OFF \
  --single-transaction \
  >"$DUMP"

echo "Importing schema into ${TARGET_DB}..."
mysql -h"$HOST" -u"$USER" "$TARGET_DB" <"$DUMP"

echo "Done. Tables in ${TARGET_DB}:"
mysql -h"$HOST" -u"$USER" "$TARGET_DB" -e "SHOW TABLES;"
