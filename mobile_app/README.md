# C-Net Web Services Mobile App

Flutter client for Android and iOS, connected to the production Laravel website.

## Production API

`https://web.mciedu.com/api/mobile/v1`

## Foundation included

- C-Net Web Services branded interface
- Live services and plans from Laravel
- Free Trial Website shortcut
- Home, Services, Projects and Profile navigation
- Call, email, website and WhatsApp actions
- Network failure screen with Retry
- Secure-storage dependency ready for client tokens
- Android/iOS automated platform setup

## Platform setup

From `mobile_app/` on a computer with Flutter installed:

```bash
bash tool/release_setup.sh
```

Identifiers:

- Android application ID: `com.mciedu.cnetwebservices`
- iOS bundle identifier: `com.mciedu.cnetwebservices`

## Build

```bash
flutter build apk --debug
flutter build appbundle --release
```

The next secured module adds email-OTP client login, My Trials, project tracking, notifications and Admin/Staff access.
