#!/usr/bin/env bash
set -euo pipefail

: "${ANDROID_KEYSTORE_PASSWORD:?ANDROID_KEYSTORE_PASSWORD is required}"
: "${ANDROID_KEY_ALIAS:?ANDROID_KEY_ALIAS is required}"
: "${ANDROID_KEY_PASSWORD:?ANDROID_KEY_PASSWORD is required}"

KEYSTORE_FILE="android/app/cnet-web-services-upload-key.jks"
GRADLE_FILE="android/app/build.gradle.kts"

test -s "$KEYSTORE_FILE"
test -f "$GRADLE_FILE"

cat > android/key.properties <<EOF
storePassword=${ANDROID_KEYSTORE_PASSWORD}
keyPassword=${ANDROID_KEY_PASSWORD}
keyAlias=${ANDROID_KEY_ALIAS}
storeFile=cnet-web-services-upload-key.jks
EOF

python3 - "$GRADLE_FILE" <<'PY'
from pathlib import Path
import sys

path = Path(sys.argv[1])
text = path.read_text()

header = '''import java.util.Properties
import java.io.FileInputStream

val keystoreProperties = Properties()
val keystorePropertiesFile = rootProject.file("key.properties")
if (keystorePropertiesFile.exists()) {
    keystoreProperties.load(FileInputStream(keystorePropertiesFile))
}

'''

if "val keystoreProperties = Properties()" not in text:
    text = header + text

signing = '''    signingConfigs {
        create("release") {
            keyAlias = keystoreProperties["keyAlias"] as String
            keyPassword = keystoreProperties["keyPassword"] as String
            storeFile = file(keystoreProperties["storeFile"] as String)
            storePassword = keystoreProperties["storePassword"] as String
        }
    }

'''

marker = "    buildTypes {"
if 'create("release")' not in text:
    if marker not in text:
        raise SystemExit("Android buildTypes block was not found")
    text = text.replace(marker, signing + marker, 1)

debug_line = 'signingConfig = signingConfigs.getByName("debug")'
release_line = 'signingConfig = signingConfigs.getByName("release")'
if debug_line in text:
    text = text.replace(debug_line, release_line, 1)
elif release_line not in text:
    raise SystemExit("Release signingConfig assignment was not found")

path.write_text(text)
PY

grep -q 'getByName("release")' "$GRADLE_FILE"
keytool -list -keystore "$KEYSTORE_FILE" -storepass "$ANDROID_KEYSTORE_PASSWORD" -alias "$ANDROID_KEY_ALIAS" >/dev/null

echo "ANDROID_PERMANENT_SIGNING=CONFIGURED"
