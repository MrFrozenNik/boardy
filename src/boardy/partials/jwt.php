<?php
function generate_jwt($user_id, $user_name) {
    $secret_key = 'your-secret-key-change-me';

    $header = rtrim(strtr(base64_encode(json_encode(
        ['alg' => 'HS256', 'typ' => 'JWT']
    )), '+/', '-_'), '=');
    $payload = rtrim(strtr(base64_encode(json_encode([
        'user_id' => $user_id,
        'name' => $user_name,
        'exp' => time() + 3600
    ])), '+/', '-_'), '=');
    $signature = rtrim(strtr(base64_encode(
        hash_hmac('sha256', "$header.$payload", $secret_key, true)
    ), '+/', '-_'), '=');
    return "$header.$payload.$signature";
}