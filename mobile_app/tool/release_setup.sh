#!/usr/bin/env bash
set -euo pipefail

APP_NAME="cnet_web_services"
ORG="com.mciedu"
PACKAGE="com.mciedu.cnetwebservices"

flutter create --platforms=android,ios --project-name "$APP_NAME" --org "$ORG" .
flutter pub get
flutter analyze
flutter test

echo "ANDROID_APPLICATION_ID=$PACKAGE"
echo "IOS_BUNDLE_IDENTIFIER=$PACKAGE"
echo "CNET_WEB_SERVICES_MOBILE_SETUP=COMPLETED"
