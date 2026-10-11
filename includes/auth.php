<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}


if (!isset($_SESSION['username'])) {
    $appRoot = realpath(dirname(__DIR__));
    $documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
    $appBasePath = '';

    if ($appRoot !== false && $documentRoot !== false) {
        $normalizedAppRoot = str_replace('\\', '/', $appRoot);
        $normalizedDocumentRoot = rtrim(str_replace('\\', '/', $documentRoot), '/');

        if (strcasecmp($normalizedAppRoot, $normalizedDocumentRoot) === 0) {
            $appBasePath = '';
        } elseif (stripos($normalizedAppRoot, $normalizedDocumentRoot . '/') === 0) {
            $appBasePath = substr($normalizedAppRoot, strlen($normalizedDocumentRoot));
        }
    }

    header('Location: ' . $appBasePath . '/login.php');
    exit;
}