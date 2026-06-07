<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="oauth-client-id" content="{{ config('services.passport.spa_client_id') }}">
    <title>Авторизация…</title>
</head>
<body>
<p>Завершаем вход…</p>
<script type="module">
    import { handleCallback } from '/js/auth.js'
    handleCallback()
        .then(token => {
            if (token) {
                sessionStorage.setItem('access_token', token)
                const back = sessionStorage.getItem('oauth_return') || '/'
                sessionStorage.removeItem('oauth_return')
                window.location = back
            }
        })
        .catch(err => { document.body.innerHTML = '<pre>' + err.message + '</pre>' })
</script>
</body>
</html>
