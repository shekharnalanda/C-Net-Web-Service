# C-Net Web Services Mobile App

Flutter mobile client connected to the production Laravel platform at `https://web.mciedu.com`.

## Included modules

- Branded Home, Services, Projects and Profile navigation
- Live services and plans from the Laravel API
- Free Trial Website shortcut
- Email-OTP client login
- Client Trial Websites and final project tracking
- Admin/Staff login and enquiry management
- Secure local token storage
- Call, email, website and WhatsApp actions
- Network failure screen with Retry
- Automated Android APK build through GitHub Actions

## Production API

`https://web.mciedu.com/api/mobile/v1`

## Application identity

- App name: C-Net Web Services
- Android application ID: `com.mciedu.cnetwebservices`
- iOS bundle identifier: `com.mciedu.cnetwebservices`
- Version: `1.0.0+1`

## Automatic Android build

Every change under `mobile_app/` runs the GitHub Actions workflow:

`Build C-Net Web Services Android App`

The workflow automatically:

1. installs stable Flutter;
2. generates the Android platform;
3. installs packages;
4. verifies formatting;
5. runs static analysis and tests;
6. builds `app-release.apk`;
7. uploads `C-Net-Web-Services-Android-APK` as a downloadable artifact.

It can also be started manually from **GitHub → Actions → Build C-Net Web Services Android App → Run workflow**.

## One-command Windows build

Open PowerShell inside `mobile_app` and run:

```powershell
powershell -ExecutionPolicy Bypass -File .\tool\build_release.ps1
```

Successful output:

`build\app\outputs\flutter-apk\app-release.apk`

## Linux/macOS platform setup

```bash
bash tool/release_setup.sh
flutter build apk --release
```

The Android artifact produced by automation is suitable for direct device testing. A Play Store production release should use the owner's permanent upload keystore and build an Android App Bundle.
