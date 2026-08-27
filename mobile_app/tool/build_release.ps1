$ErrorActionPreference = "Stop"

$AppName = "cnet_web_services"
$Organization = "com.mciedu"

Write-Host "===== C-NET WEB SERVICES MOBILE RELEASE =====" -ForegroundColor Cyan

flutter --version
flutter doctor

flutter create --platforms=android --project-name $AppName --org $Organization .

$Manifest = Join-Path $PWD "android\app\src\main\AndroidManifest.xml"
$ManifestContent = Get-Content -Raw $Manifest
if ($ManifestContent -notmatch "android.permission.INTERNET") {
    $Permissions = @"
    <uses-permission android:name="android.permission.INTERNET" />
    <uses-permission android:name="android.permission.ACCESS_NETWORK_STATE" />

"@
    $ManifestContent = $ManifestContent.Replace("    <application", $Permissions + "    <application")
    Set-Content -Path $Manifest -Value $ManifestContent -Encoding UTF8
}

Remove-Item "test\widget_test.dart" -Force -ErrorAction SilentlyContinue
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
