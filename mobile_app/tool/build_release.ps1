$ErrorActionPreference = "Stop"

$AppName = "cnet_web_services"
$Organization = "com.mciedu"

Write-Host "===== C-NET WEB SERVICES MOBILE RELEASE =====" -ForegroundColor Cyan

flutter --version
flutter doctor

flutter create --platforms=android --project-name $AppName --org $Organization .

$BrandingDirectory = Join-Path $PWD "assets\branding"
New-Item -ItemType Directory -Force -Path $BrandingDirectory | Out-Null
if (-not (Get-Command magick -ErrorAction SilentlyContinue)) {
    throw "ImageMagick is required. Install it once with: winget install ImageMagick.ImageMagick"
}
magick "..\public\images\cnet-web-logo.jpeg" -resize "1024x1024^" -gravity center -extent 1024x1024 "$BrandingDirectory\app_icon.png"
Copy-Item "..\public\images\cnet-web-logo.jpeg" "$BrandingDirectory\splash_logo.jpeg" -Force
magick "$BrandingDirectory\app_icon.png" -resize 48x48 "android\app\src\main\res\mipmap-mdpi\ic_launcher.png"
magick "$BrandingDirectory\app_icon.png" -resize 72x72 "android\app\src\main\res\mipmap-hdpi\ic_launcher.png"
magick "$BrandingDirectory\app_icon.png" -resize 96x96 "android\app\src\main\res\mipmap-xhdpi\ic_launcher.png"
magick "$BrandingDirectory\app_icon.png" -resize 144x144 "android\app\src\main\res\mipmap-xxhdpi\ic_launcher.png"
magick "$BrandingDirectory\app_icon.png" -resize 192x192 "android\app\src\main\res\mipmap-xxxhdpi\ic_launcher.png"

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
$ManifestContent = Get-Content -Raw $Manifest
$ManifestContent = $ManifestContent.Replace('android:label="cnet_web_services"', 'android:label="C-Net Web Services"')
Set-Content -Path $Manifest -Value $ManifestContent -Encoding UTF8

Remove-Item "test\widget_test.dart" -Force -ErrorAction SilentlyContinue
flutter pub get
dart run flutter_native_splash:create
dart format lib test
flutter analyze
flutter test
flutter build apk --release
flutter build appbundle --release

$Apk = Join-Path $PWD "build\app\outputs\flutter-apk\app-release.apk"
if (-not (Test-Path $Apk)) {
    throw "APK was not created."
}
$Aab = Join-Path $PWD "build\app\outputs\bundle\release\app-release.aab"
if (-not (Test-Path $Aab)) {
    throw "AAB was not created."
}

Write-Host ""
Write-Host "APK_BUILD=COMPLETED" -ForegroundColor Green
Write-Host "APK_PATH=$Apk" -ForegroundColor Green
Write-Host "AAB_BUILD=COMPLETED" -ForegroundColor Green
Write-Host "AAB_PATH=$Aab" -ForegroundColor Green
