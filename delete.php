<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP 1.1.
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

function decrypt($data, $key) {
    $cipher = "aes-256-cbc";
    $data = base64_decode($data);
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = substr($data, 0, $ivlen);
    $ciphertext = substr($data, $ivlen);
    return openssl_decrypt($ciphertext, $cipher, $key, $options=0, $iv);
}

$key = 'vikry'; // Gantilah ini dengan kunci rahasia yang aman

if (isset($_GET['file'])) {
    $file = decrypt($_GET['file'], $key);
    $filePath = __DIR__ . '/uploads/' . $file;

    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            echo 'File deleted successfully';
        } else {
            echo 'Error deleting file';
        }
    } else {
        echo 'File not found';
    }
} else {
    echo 'No file specified';
}
?>
