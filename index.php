<?php
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

function encrypt($data, $key) {
    $cipher = "aes-256-cbc";
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext = openssl_encrypt($data, $cipher, $key, $options=0, $iv);
    return base64_encode($iv.$ciphertext);
}

function decrypt($data, $key) {
    $cipher = "aes-256-cbc";
    $data = base64_decode($data);
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = substr($data, 0, $ivlen);
    $ciphertext = substr($data, $ivlen);
    return openssl_decrypt($ciphertext, $cipher, $key, $options=0, $iv);
}

$key = 'vikry'; // Gantilah ini dengan kunci rahasia yang aman

// Cegah output HTML sebelum header
ob_start(); // Start output buffering
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1>Upload File</h1>
        <div class="upload-box" id="uploadBox">
            Drop your files here or <br><button id="filePicker">Choose from file</button>
            <input type="file" id="fileInput" multiple style="display: none;">
        </div>
        <div id="progressContainer" class="progress-container" style="display: none;">
            <div id="progressBar" class="progress-bar"></div>
        </div>
        <div id="notification" class="notification"></div>
        <ul id="fileList"></ul>
        <h2>Available Downloads</h2>
        <ul class="file-list" id="fileListContainer">
            <?php
            $uploadsDir = __DIR__ . '/uploads/';
            $files = array_diff(scandir($uploadsDir), array('.', '..'));

            foreach ($files as $file) {
                $filePath = $uploadsDir . $file;
                if (is_file($filePath)) {
                    $ext = pathinfo($file, PATHINFO_EXTENSION);
                    switch (strtolower($ext)) {
                        case 'jpg':
                        case 'jpeg':
                        case 'png':
                        case 'gif':
                            $icon = 'fa-file-image';
                            break;
                        case 'zip':
                        case 'rar':
                            $icon = 'fa-file-archive';
                            break;
                        default:
                            $icon = 'fa-file';
                    }
                    $encryptedFile = encrypt($file, $key);
                    echo '<li><i class="fas ' . $icon . '"></i> <a href="download.php?file=' . urlencode($encryptedFile) . '" data-type="' . strtolower($ext) . '" data-path="uploads/' . $file . '" download>' . $file . '</a> <button class="delete-btn" data-file="' . urlencode($encryptedFile) . '">Delete</button></li>';
                }
            }
            ?>
        </ul>
        <div id="imagePreview" class="image-preview"></div>
    </div>
    <footer>
        <p>Created <i class="fas fa-heart"></i> by Vikryhan</p>
    </footer>
    <script src="script.js"></script>
</body>
</html>

<?php
ob_end_flush(); // Flush output buffer and send to browser
?>
