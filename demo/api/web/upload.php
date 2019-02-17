<?php
header("Access-Control-Allow-Origin: *");
use common\models\common\BaseConfig;
require_once('../../common/models/common/BaseConfig.php');
// echo "hh";
// exit();
$file_arr= explode('backend',__DIR__);
$file  = current($file_arr);
$targetDir = $file.'upload_tmp';
$uploadDir =$file.'upload/wang';
$maxFileAge = 200;

$cleanupTargetDir = true;

if (!file_exists($targetDir)) {
    @mkdir($targetDir);
}

if (!file_exists($uploadDir)) {
    @mkdir($uploadDir);
}

if (isset($_REQUEST["name"])) {
    $fileName = $_REQUEST["name"];
} elseif (!empty($_FILES)) {
    $fileName = $_FILES["file"]["name"];
} else {
    $fileName = uniqid("file_");
}
$fileName = iconv('UTF-8', 'GB2312', $fileName);
$fileNameList = explode('.', $fileName);
$targetDir = $targetDir . DIRECTORY_SEPARATOR . time("Ymd");
//判断给定的文件或目录存在并且可读
if (!is_readable($targetDir)) {
    //is_file 如果是目录返回false,执行mkdir创建文件夹
    is_file($targetDir) or mkdir($targetDir, 0777);
}
$time1 = rand(10000,99999);
$fileName = $time1.time().".".$fileNameList[1];

$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
$uploadPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

$config =  \common\models\common\BaseConfig::getBaseConfig();

$imgUrl= $config['img_domain'].'/wang/'.$fileName;
echo $imgUrl;

$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;


if ($cleanupTargetDir) {
    if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
        die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
    }
    while (($file = readdir($dir)) !== false) {
        $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

        if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
            continue;
        }

        if (preg_match('/\.(part|parttmp)$/', $file) ) {
            @unlink($tmpfilePath);
        }
    }
    closedir($dir);
}



if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) {
    die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
}

if (!empty($_FILES)) {
    if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
        die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
    }

    if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
    }
} else {
    if (!$in = @fopen("php://input", "rb")) {
        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
    }
}

while ($buff = fread($in, 4096)) {
    fwrite($out, $buff);
}

@fclose($out);
@fclose($in);

rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");

$index = 0;
$done = true;
for( $index = 0; $index < $chunks; $index++ ) {
    if ( !file_exists("{$filePath}_{$index}.part") ) {
        $done = false;
        break;
    }
}
if ( $done ) {
    if (!$out = @fopen($uploadPath, "wb")) {
        die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
    }

    if ( flock($out, LOCK_EX) ) {
        for( $index = 0; $index < $chunks; $index++ ) {
            if (!$in = @fopen("{$filePath}_{$index}.part", "rb")) {
                break;
            }

            while ($buff = fread($in, 4096)) {
                fwrite($out, $buff);
            }

            @fclose($in);
            @unlink("{$filePath}_{$index}.part");
        }

        flock($out, LOCK_UN);
    }
    @fclose($out);
}