<?php
require_once 'aws/aws-autoloader.php';
date_default_timezone_set('America/Los_Angeles');


$jm_mino_s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region' => 'us-east-1',
    'endpoint' => $endpoint,
    'use_path_style_endpoint' => true,
    'credentials' => [
        'key' => $key,
        'secret' => $secret,
    ],
]);





function jm_minio_create_bucket($bucket) {

    global $jm_mino_s3;

    $result = $jm_mino_s3->createBucket([
    'Bucket' => $bucket,
    ]);

    echo 'create bucket berhasil';

}

function jm_minio_upload_file($bucket, $imageName, $souceFilePath, $contentType = 'image/jpg') {
    global $jm_mino_s3;
    $result = $jm_mino_s3->putObject([
        'Bucket' => $bucket,
        'Key' => $imageName,
        'Body' => 'body',
        'SourceFile' => $souceFilePath,
        'ContentType' =>  $contentType,
    ]);

}


?>
