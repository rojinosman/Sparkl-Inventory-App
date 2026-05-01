#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")" && pwd)"
LIVE="$ROOT/live"
PHP="${PHP:-/opt/homebrew/bin/php}"
MYSQL="${MYSQL:-/opt/homebrew/bin/mysql}"

if [[ ! -x "$PHP" ]]; then
  echo "PHP not found at $PHP — install with: brew install php" >&2
  exit 1
fi

if [[ ! -x "$MYSQL" ]]; then
  echo "mysql client not found at $MYSQL — install with: brew install mysql" >&2
  exit 1
fi

echo "Starting MySQL (Homebrew)…"
brew services start mysql >/dev/null 2>&1 || true
sleep 2

echo "Applying dev DB bootstrap…"
"$MYSQL" -u root paul_bulkdb < "$LIVE/dev-bootstrap.sql"
echo "Applying API / RFID schema upgrades…"
"$MYSQL" -u root paul_bulkdb < "$LIVE/dev-api-upgrade.sql"
echo "Applying locations / movements schema…"
"$MYSQL" -u root paul_bulkdb < "$LIVE/dev-schema-v2.sql"

# 0.0.0.0 = listen on all interfaces so a physical phone on Wi‑Fi can reach this Mac (use Mac's LAN IP in the app).
# Emulator still uses http://10.0.2.2:PORT/ in the Android app (maps to this host).
HOST="${DEV_SERVER_HOST:-0.0.0.0}"
PORT="${DEV_SERVER_PORT:-8080}"

echo "Serving $LIVE at http://${HOST}:${PORT}/"
echo "Sign in: dev@sparkl.local / LocalDev123!"
echo "Physical device: set app server URL to http://$(ipconfig getifaddr en0 2>/dev/null || echo 'YOUR_MAC_LAN_IP'):${PORT}/"
echo "JSON API: http://127.0.0.1:${PORT}/api/v1/login.php (see api/v1/)"
exec "$PHP" -S "${HOST}:${PORT}" -t "$LIVE"
