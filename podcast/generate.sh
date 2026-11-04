#!/bin/bash
# Synthesize the PCDD study podcast from the S:/D: script lines in
# _outputs/demo-pcdd26-pimcore-study-podcast.md using macOS `say` + ffmpeg.
# Fully local. Voices: Samantha (S, host), Daniel (D, expert).
set -euo pipefail

REPO="$(cd "$(dirname "$0")" && pwd)"
SRC="$REPO/script.md"
OUT="$REPO/study-podcast.m4a"
TMP="$(mktemp -d)"
trap 'rm -rf "$TMP"' EXIT

i=0
: > "$TMP/list.txt"
# Read only the script lines (S: / D: prefixes), preserve order.
while IFS= read -r line; do
  case "$line" in
    "S: "*) voice="Samantha"; text="${line#S: }" ;;
    "D: "*) voice="Daniel";   text="${line#D: }" ;;
    *) continue ;;
  esac
  i=$((i+1))
  seg="$TMP/$(printf '%03d' "$i").aiff"
  say -v "$voice" -r 182 -o "$seg" "$text"
  printf "file '%s'\n" "$seg" >> "$TMP/list.txt"
done < "$SRC"

[ "$i" -gt 0 ] || { echo "No S:/D: lines found in $SRC" >&2; exit 1; }


ffmpeg -y -loglevel error -f concat -safe 0 -i "$TMP/list.txt" \
  -af "aresample=44100" -c:a aac -b:a 128k "$OUT"

dur=$(ffmpeg -i "$OUT" 2>&1 | sed -n 's/.*Duration: \([0-9:.]*\).*/\1/p' || true)
echo "Wrote $OUT ($i segments, duration $dur)"
