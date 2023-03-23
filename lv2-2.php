<?php

$upload_dir = "uploads/";
$db_host = "localhost:3306";
$db_user = "root";
$db_pass = "admin";
$db_name = "LV2_2";

function encrypt_file($input_file, $output_file, $key) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length("aes-256-cbc"));
    $encrypted = openssl_encrypt(file_get_contents($input_file), "aes-256-cbc", $key, 0, $iv);
    file_put_contents($output_file, $encrypted);
    return $iv;
}

function decrypt_file($input_file, $output_file, $key, $iv) {
    $decrypted = openssl_decrypt(file_get_contents($input_file), "aes-256-cbc", $key, 0, $iv);
    file_put_contents($output_file, $decrypted);
}


if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {

    $key = openssl_random_pseudo_bytes(32);

    $original_filename = basename($_FILES['file']['name']);
    $encrypted_filename = md5($original_filename . time()) . ".enc";
    $encrypted_file_path = $upload_dir . $encrypted_filename;

    $iv = encrypt_file($_FILES['file']['tmp_name'], $encrypted_file_path, $key);


    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $sql = "INSERT INTO uploads (original_filename, encrypted_filename, iv, key) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssss", $original_filename, $encrypted_filename, $iv, $key);
    $stmt->execute();
    $stmt->close();
    $mysqli->close();

}