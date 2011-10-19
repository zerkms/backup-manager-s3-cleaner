<?php

$bucket = 'zerkms-backup';

$parameters = getParameters();

require_once 'Zend/Service/Amazon/S3.php';

$s3 = new Zend_Service_Amazon_S3($parameters['key'], $parameters['secret']);

$list = $s3->getObjectsByBucket($parameters['bucket']);

foreach ($list as $file) {
    $fullName = $parameters['bucket'] . '/' . $file;
    $info = $s3->getInfo($fullName);

    if ($info['mtime'] < strtotime(-$parameters['ttl'] . ' days')) {
        $s3->removeObject($fullName);
    }
}

function getParameters()
{
    $parameters = array();

    $config = file_get_contents('/etc/backup-manager.conf');

    preg_match('~BM_ARCHIVE_TTL\D+(\d+)~', $config, $matches);
    if (!isset($matches[1])) throw new Exception('BM_ARCHIVE_TTL parameter is not found');
    $parameters['ttl'] = $matches[1];

    preg_match('~BM_UPLOAD_S3_ACCESS_KEY="([^"]+)~', $config, $matches);
    if (!isset($matches[1])) throw new Exception('BM_UPLOAD_S3_ACCESS_KEY parameter is not found');
    $parameters['key'] = $matches[1];

    preg_match('~BM_UPLOAD_S3_SECRET_KEY="([^"]+)~', $config, $matches);
    if (!isset($matches[1])) throw new Exception('BM_UPLOAD_S3_SECRET_KEY parameter is not found');
    $parameters['secret'] = $matches[1];

    preg_match('~BM_UPLOAD_S3_DESTINATION="([^"]+)~', $config, $matches);
    if (!isset($matches[1])) throw new Exception('BM_UPLOAD_S3_DESTINATION parameter is not found');
    $parameters['bucket'] = $matches[1];

    return $parameters;
}
