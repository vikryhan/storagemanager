<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $uploadDirectory = __DIR__ . '/uploads/';

        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        $filePath = $uploadDirectory . basename($file['name']);

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            echo 'File uploaded successfully.';
        } else {
            echo 'Failed to upload file.';
        }
    }
}
?>
