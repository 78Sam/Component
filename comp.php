<?php

declare(strict_types=1);

$options = ['franken', 'server'];

$help = static function () use ($options) {
    $optionsString = implode('|', $options);
    print_r("php comp.php <{$optionsString}>\n");

    exit();
};

if ($argc !== 2) {
    $help();
}

$input = strtolower($argv[1]);
if (!in_array($input, $options, true)) {
    $help();
}

if ($input === 'server') {
    exec('cd ComponentPHP/Public && php -S localhost:8080');

    exit();
}

$output = [];
$found = false;
exec('docker images --format json', $output);

if (count($output) !== 0) {
    foreach ($output as $image) {
        $imageJSON = json_decode($image, true);
        if ($imageJSON === null) {
            throw new \Exception("Unable to decode image json: '{$image}'");
        }

        if (($imageJSON['Repository'] ?? null) === 'component_php') {
            $found = true;

            break;
        }
    }
}

if (!$found) {
    print_r("Please run 'docker build -t component_php .' and retry\n");

    exit();
}

// $output = [];
// exec('docker ps -a --format json', $output);
// if (count($output) !== 0) {
//     foreach ($output as $process) {
//         $processJSON = json_decode($process, true);
//         if ($processJSON === null) {
//             throw new \Exception("Unable to decode process json: '{$process}'");
//         }
        
//         if ($processJSON['Names'] === 'component_php') {
//             exec('docker compose down');
//         }
//     }
// }

exec('docker compose down');
exec('docker compose up');
