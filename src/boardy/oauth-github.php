<?php
require_once 'db.php';
$client_id = 'Ov23lijypRA9BwMmJtzg';
$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

$params = http_build_query([
    'client_id' => $client_id,
    'redirect_uri' => 'https://nfrozensky.ai-info.ru/oauth-callback.php',
    'scope' => 'read:user',
    'state' => $state,
]);

header("Location: https://github.com/login/oauth/authorize?$params");
exit;