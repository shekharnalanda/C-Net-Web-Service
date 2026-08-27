$ErrorActionPreference = "Stop"

$Repo = "shekharnalanda/C-Net-Web-Service"
$Alias = "cnetwebservices"
$ReleaseDirectory = Join-Path $env:USERPROFILE "CNetWebServices-Release"
$Keystore = Join-Path $ReleaseDirectory "cnet-web-services-upload-key.jks"

New-Item -ItemType Directory -Force -Path $ReleaseDirectory | Out-Null

if (Test-Path $Keystore) {
    throw "Signing key already exists at $Keystore. It was not overwritten."
}

$SecurePassword = Read-Host "Create a strong signing password (minimum 12 characters)" -AsSecureString
$Pointer = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($SecurePassword)
$Password = [Runtime.InteropServices.Marshal]::PtrToStringBSTR($Pointer)

try {
    if ($Password.Length -lt 12) {
        throw "Signing password must contain at least 12 characters."
    }

    keytool -genkeypair -v `
        -keystore $Keystore `
        -alias $Alias `
        -keyalg RSA `
        -keysize 4096 `
        -validity 10000 `
        -storepass $Password `
        -keypass $Password `
        -dname "CN=C-Net Web Services, OU=MCI Educational Group, O=C-Net Web Services, L=Bihar Sharif, ST=Bihar, C=IN"

    $Base64 = [Convert]::ToBase64String([IO.File]::ReadAllBytes($Keystore))

    if (Get-Command gh -ErrorAction SilentlyContinue) {
        $Base64 | gh secret set ANDROID_KEYSTORE_BASE64 --repo $Repo
        $Password | gh secret set ANDROID_KEYSTORE_PASSWORD --repo $Repo
        $Alias | gh secret set ANDROID_KEY_ALIAS --repo $Repo
        $Password | gh secret set ANDROID_KEY_PASSWORD --repo $Repo
        Write-Host "GITHUB_SIGNING_SECRETS=CONFIGURED" -ForegroundColor Green
    }
    else {
        $SecretFile = Join-Path $ReleaseDirectory "github-signing-secrets.txt"
        @"
ANDROID_KEYSTORE_BASE64=$Base64
ANDROID_KEYSTORE_PASSWORD=$Password
ANDROID_KEY_ALIAS=$Alias
ANDROID_KEY_PASSWORD=$Password
"@ | Set-Content -Path $SecretFile -Encoding UTF8
        Write-Host "GitHub CLI was not found." -ForegroundColor Yellow
        Write-Host "Add the four values from this private file to GitHub Actions secrets:" -ForegroundColor Yellow
        Write-Host $SecretFile -ForegroundColor Yellow
    }

    Write-Host "KEYSTORE_BACKUP_REQUIRED=$Keystore" -ForegroundColor Green
}
finally {
    if ($Pointer -ne [IntPtr]::Zero) {
        [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($Pointer)
    }
    $Password = $null
}
