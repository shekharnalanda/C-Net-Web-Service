$ErrorActionPreference = "Stop"

$Repo = "shekharnalanda/C-Net-Web-Service"
$Alias = "cnetwebservices"
$ReleaseDirectory = Join-Path $env:USERPROFILE "CNetWebServices-Release"
$Keystore = Join-Path $ReleaseDirectory "cnet-web-services-upload-key.jks"

New-Item -ItemType Directory -Force -Path $ReleaseDirectory | Out-Null

$KeytoolCommand = Get-Command keytool -ErrorAction SilentlyContinue
if (-not $KeytoolCommand) {
    $KeytoolCandidate = Get-ChildItem @(
        "C:\Program Files\Android\Android Studio\jbr\bin\keytool.exe",
        "C:\Program Files\Microsoft\jdk-*\bin\keytool.exe",
        "C:\Program Files\Eclipse Adoptium\jdk-*\bin\keytool.exe"
    ) -ErrorAction SilentlyContinue | Select-Object -First 1

    if (-not $KeytoolCandidate -and (Get-Command winget -ErrorAction SilentlyContinue)) {
        winget install --id Microsoft.OpenJDK.17 --exact --accept-package-agreements --accept-source-agreements
        $KeytoolCandidate = Get-ChildItem "C:\Program Files\Microsoft\jdk-*\bin\keytool.exe" -ErrorAction SilentlyContinue | Select-Object -First 1
    }

    if (-not $KeytoolCandidate) {
        throw "Java keytool was not found. Install JDK 17 and run this setup again."
    }
    $Keytool = $KeytoolCandidate.FullName
}
else {
    $Keytool = $KeytoolCommand.Source
}

$GhCommand = Get-Command gh -ErrorAction SilentlyContinue
if (-not $GhCommand -and (Get-Command winget -ErrorAction SilentlyContinue)) {
    winget install --id GitHub.cli --exact --accept-package-agreements --accept-source-agreements
    $GhCandidate = Get-Item "C:\Program Files\GitHub CLI\gh.exe" -ErrorAction SilentlyContinue
    if ($GhCandidate) {
        $Gh = $GhCandidate.FullName
    }
}
elseif ($GhCommand) {
    $Gh = $GhCommand.Source
}

$ExistingKeystore = Test-Path $Keystore
if ($ExistingKeystore) {
    Write-Host "Existing C-Net Web Services signing key found; setup will safely resume." -ForegroundColor Cyan
    $SecurePassword = Read-Host "Enter the existing signing password" -AsSecureString
}
else {
    $SecurePassword = Read-Host "Create a strong signing password (minimum 12 characters)" -AsSecureString
}
$Pointer = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($SecurePassword)
$Password = [Runtime.InteropServices.Marshal]::PtrToStringBSTR($Pointer)

try {
    if ($Password.Length -lt 12) {
        throw "Signing password must contain at least 12 characters."
    }

    if (-not $ExistingKeystore) {
        & $Keytool -genkeypair -v `
            -keystore $Keystore `
            -alias $Alias `
            -keyalg RSA `
            -keysize 4096 `
            -validity 10000 `
            -storepass $Password `
            -keypass $Password `
            -dname "CN=C-Net Web Services, OU=MCI Educational Group, O=C-Net Web Services, L=Bihar Sharif, ST=Bihar, C=IN"
    }

    & $Keytool -list -keystore $Keystore -alias $Alias -storepass $Password | Out-Null
    if ($LASTEXITCODE -ne 0) {
        throw "The existing signing password is incorrect."
    }

    $Base64 = [Convert]::ToBase64String([IO.File]::ReadAllBytes($Keystore))

    if ($Gh) {
        $PreviousErrorActionPreference = $ErrorActionPreference
        $ErrorActionPreference = "Continue"
        & $Gh auth status 2>$null
        $GitHubAuthStatus = $LASTEXITCODE
        $ErrorActionPreference = $PreviousErrorActionPreference
        if ($GitHubAuthStatus -ne 0) {
            & $Gh auth login --hostname github.com --git-protocol https --web
            if ($LASTEXITCODE -ne 0) {
                throw "GitHub login was not completed."
            }
        }
        $Base64 | & $Gh secret set ANDROID_KEYSTORE_BASE64 --repo $Repo
        $Password | & $Gh secret set ANDROID_KEYSTORE_PASSWORD --repo $Repo
        $Alias | & $Gh secret set ANDROID_KEY_ALIAS --repo $Repo
        $Password | & $Gh secret set ANDROID_KEY_PASSWORD --repo $Repo
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
