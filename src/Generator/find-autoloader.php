<?php
function detectAndLoadVendor($path = __DIR__){
    $path = realpath($path);
    if($path == '/') {
        die("Could not find a suitable /vendor directory! Maybe you need to run composer install!\n");
    }

    foreach(new DirectoryIterator($path) as $fileInfo){
        if($fileInfo->isDir() && $fileInfo->getFilename() == "vendor"){
            require_once($fileInfo->getRealPath() . "/autoload.php");
            return;
        }
    }
    detectAndLoadVendor($path . "/../");
}
detectAndLoadVendor();