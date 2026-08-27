$ErrorActionPreference = "Stop"

$AppName = "cnet_web_services"
$Organization = "com.mciedu"

Write-Host "===== C-NET WEB SERVICES MOBILE RELEASE =====" -ForegroundColor Cyan

flutter --version
flutter doctor

flutter create --platforms=android --project-name $AppName --org $Organization .
flutter pub get
dart format lib test
flutter analyze
flutter test
flutter build apk --release

$Apk = Join-Path $PWD "build\app\outputs\flutter-apk\app-release.apk"
if (-not (Test-Path $Apk)) {
    throw "APK was not created."
}

Write-Host ""
Write-Host "APK_BUILD=COMPLETED" -ForegroundColor Green
Write-Host "APK_PATH=$Apk" -ForegroundColor Green
