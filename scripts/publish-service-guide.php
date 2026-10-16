<?php

use Database\Seeders\PublicServiceGuideSeeder;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

require getcwd().'/vendor/autoload.php';
$app = require getcwd().'/bootstrap/app.php';
$app->make(ConsoleKernel::class)->bootstrap();
$slugs = array_keys(config('service-guide.services'));
$before = DB::table('services')->whereIn('slug', $slugs)->get()->toArray();
file_put_contents($argv[1].'/services-before.json', json_encode($before, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
DB::beginTransaction();
try {
    (new PublicServiceGuideSeeder)->run();
    $kernel = $app->make(HttpKernel::class);
    $paths = array_merge(['/', '/plans', '/trial/apply', '/admin/login', '/enquiry', '/enquiry?service=school-erp'], array_map(fn ($slug) => '/services/'.$slug, $slugs));
    foreach ($paths as $path) {
        $request = Request::create('https://web.mciedu.com'.$path);
        $response = $kernel->handle($request);
        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException('PAGE_FAILED '.$path.' '.$response->getStatusCode());
        }
        if ($path === '/' && ! str_contains($response->getContent(), 'आपकी संस्था की डिजिटल जरूरतों')) {
            throw new RuntimeException('Wrong homepage route.');
        }
        echo 'PAGE_OK '.$path.PHP_EOL;
        $kernel->terminate($request, $response);
    }
    DB::commit();
    echo 'SERVICE_GUIDE_RECORDS_PUBLISHED '.count($slugs).PHP_EOL;
} catch (Throwable $exception) {
    DB::rollBack();
    file_put_contents($argv[1].'/deployment-error.txt', (string) $exception);
    fwrite(STDERR, 'Service guide publication failed; service changes rolled back.'.PHP_EOL);
    exit(1);
}
