<?php
require_once '../db.php';
require_once '../partials/jwt.php';

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

header('Content-Type: application/json');
echo json_encode(['token' => generate_jwt($_SESSION['user_id'], $_SESSION['user_name'])]);