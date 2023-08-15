<?php
require '../vendor/autoload.php';        // Make sure to point this correctly

use WebPConvert\WebPConvert;

header("Cache-Control: max-age=2592000"); // 30days (60sec * 60min * 24hours * 30days)

$source = $_GET['source'];            // Absolute file path to source file. Comes from the .htaccess
// echo $source;
$destination = $source . '.webp';     // Store the converted images besides the original images (other options are available!)

$options = [

    // UNCOMMENT NEXT LINE, WHEN YOU ARE UP AND RUNNING!
//    'show-report' => true             // Show a conversion report instead of serving the converted image.
        'fail' => 'original'
    // More options available!
];
WebPConvert::serveConverted($source, $destination, $options);