#!/usr/bin/env python3
"""Publish the service guide without overwriting live account-recovery routes."""
import datetime
import hashlib
import shutil
import subprocess
import sys
from pathlib import Path

ROOT = Path('/home4/mcied45x/web.mciedu.com') if len(sys.argv) < 2 else Path(sys.argv[1])
STAGE = Path(__file__).resolve().parent.parent
PHP = '/opt/cpanel/ea-php83/root/usr/bin/php'
BACKUP = Path('/home4/mcied45x/web-deploy-backups') / ('service-guide-' + datetime.datetime.utcnow().strftime('%Y%m%d-%H%M%S'))
expected = {
    'resources/views/home.blade.php': 'e600b235c20c2426d8c28b755cb03f26790b8e6d08c76fc40e9bfe9c249b87e8',
    'resources/views/service-detail.blade.php': '0af5dee6f2dffa69261d1c693d97782b0177438ca101f8aaede619029390a3aa',
    'resources/views/enquiry.blade.php': '5c2d820b532215036cb8a17d4b6efc7220e1c432afd72df7241e3a64ef80c718',
    'resources/views/partials/seo.blade.php': 'c0e140db1fd26b6cc498fc4769379649d63f00f4e818ef58da203675600c07e2',
    'app/Http/Controllers/ServiceController.php': '26cc8cf7cf3ab57c4a74edc4c0ca299a402c433ffa452a509483acff6ad9e7c3',
    'app/Http/Controllers/EnquiryController.php': '61f130df55c2338767888fb4031edaf94c966fb1ffce715722bd613c6336655d',
}
files = list(expected) + [
    'config/service-guide.php', 'database/seeders/PublicServiceGuideSeeder.php',
    'resources/views/layouts/public.blade.php', 'resources/views/partials/public-style.blade.php',
    'resources/views/partials/service-card.blade.php',
    'scripts/publish-service-guide.php', 'scripts/install-service-guide.py',
]
changes = {name: (STAGE / name).read_bytes() for name in files}
for name, digest in expected.items():
    actual = (ROOT / name).read_bytes()
    if hashlib.sha256(actual).hexdigest() != digest and actual != changes[name]:
        raise RuntimeError('Live file changed; review before replacing: ' + name)

name = 'routes/web.php'
routes = (ROOT / name).read_text()
old_domain = "Route::domain('web.mciedu.com')->get('/', function () {\n    return view('home');\n});"
new_domain = "Route::domain('web.mciedu.com')->get('/', [\\App\\Http\\Controllers\\ServiceController::class, 'home']);"
old_home = "Route::view('/', 'home')->name('home');"
new_home = "Route::get('/', [\\App\\Http\\Controllers\\ServiceController::class, 'home'])->name('home');"
if old_domain in routes:
    routes = routes.replace(old_domain, new_domain, 1)
elif new_domain not in routes:
    raise RuntimeError('Live domain route needs review.')
if old_home in routes:
    routes = routes.replace(old_home, new_home, 1)
elif new_home not in routes:
    raise RuntimeError('Live homepage route needs review.')
changes[name] = routes.encode()

BACKUP.mkdir(parents=True)
originals = {}
try:
    for name, content in changes.items():
        target = ROOT / name
        originals[name] = target.read_bytes() if target.exists() else None
        if target.exists():
            backup = BACKUP / name
            backup.parent.mkdir(parents=True, exist_ok=True)
            shutil.copy2(str(target), str(backup))
        target.parent.mkdir(parents=True, exist_ok=True)
        target.write_bytes(content)
    for name in changes:
        if name.endswith('.php'):
            subprocess.check_call([PHP, '-l', str(ROOT / name)])
    for operation in ['config:clear', 'route:clear', 'view:clear']:
        subprocess.check_call([PHP, 'artisan', operation, '--no-interaction'], cwd=str(ROOT))
    subprocess.check_call([PHP, str(ROOT / 'scripts/publish-service-guide.php'), str(BACKUP)], cwd=str(ROOT))
except Exception:
    for name, original in originals.items():
        target = ROOT / name
        if original is None:
            if target.exists():
                target.unlink()
        else:
            target.write_bytes(original)
    for operation in ['config:clear', 'route:clear', 'view:clear']:
        subprocess.run([PHP, 'artisan', operation, '--no-interaction'], cwd=str(ROOT), check=False)
    raise
print('WEB_SERVICE_GUIDE_INSTALLED', len(changes), 'files; backup:', BACKUP)
