<?php
   /*
Plugin Name: Jaswant Minio
Plugin URI: https://bhajandiary.com
description: >- Plugin to upload files to s3 server
Version: 1.0
Author: Mr. Jaswant Mandloi
Author URI: https://bhajandiary.com
License: GPL2
*/

//require_once 'config.php';


$isMinioEnabled = defined("MINIO_CREDENTIALS");

if(!$isMinioEnabled) {
	return ;
}

$credentials = MINIO_CREDENTIALS;

$key = $credentials['key'];
$secret = $credentials['secret'];
$endpoint = $credentials['endpoint'];
$jmBucketName = $credentials['jmBucketName'];
$allowedSrcs = $credentials['allowedSrcs']; // Array of allowed URLS


require_once 'mino-upload.php';


//add_filter( 'wp_headers', 'add_jm_headers' );
//add_action( 'send_headers', 'add_jm_headers' );

if(is_admin()) {
    //add_jm_headers();
}
   
/*Capture uploaded image filter and upload these to minio server*/
add_filter('wp_update_attachment_metadata', 
'wp_update_attachment_metadata_jm_minio', 12, 3);



function wp_update_attachment_metadata_jm_minio($data, $postarr) {
    
   if(count($data)) {

    uploadAttachmentsMinio($data);

   }

    return $data;
}

function uploadAttachmentsMinio($originalData) {
    global $jmBucketName;

    $data = $originalData;

    $wpUploadDirs = wp_get_upload_dir();
    $wpUploadPath = $wpUploadDirs['path'];
    $contentType = '';
    $sizes = $data['sizes'];
    $wpUploadSubDir = $wpUploadDirs['subdir'];

    $mainFileArray = explode('/', $data['file']);

    $mainFile = end($mainFileArray);

    array_push($sizes, array(
        'file' => $mainFile,
        'mime-type' => ''
    ));

    foreach($sizes as $size) {
        $file = $size['file'];
        $contentType = empty($size['mime-type']) ? $contentType : $size['mime-type'];
        $uploadingPath = "$wpUploadPath/$file";


        jm_minio_upload_file(
            $jmBucketName,
            "wp-content/uploads$wpUploadSubDir/$file",
            $uploadingPath,
            $contentType
        );
    }
}
   

function add_jm_headers() {
    global $allowedSrcs;
    $allowedSrcsStr = implode($allowedSrcs, ' ');

    header("Content-Security-Policy: worker-src *;");
}


?>
