<!DOCTYPE html>
<html lang="ru">
<head><meta charset="utf-8"><title>Авторизация</title></head>
<body style="font-family:sans-serif;max-width:420px;margin:60px auto">
<h1>Запрос доступа</h1>
<p><strong>{{ $client->name }}</strong> запрашивает доступ к вашему аккаунту.</p>

@if (count($scopes) > 0)
    <p>Запрошенные права:</p>
    <ul>
        @foreach ($scopes as $scope)
            <li>{{ $scope->description }}</li>
        @endforeach
    </ul>
@endif

<div style="display:flex;gap:12px">
    {{-- Approve --}}
    <form method="post" action="{{ route('passport.authorizations.approve') }}">
        @csrf
        <input type="hidden" name="state" value="{{ $request->state }}">
        <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
        <input type="hidden" name="auth_token" value="{{ $authToken }}">
        <button type="submit">Authorize</button>
    </form>

    {{-- Deny --}}
    <form method="post" action="{{ route('passport.authorizations.deny') }}">
        @csrf
        @method('DELETE')
        <input type="hidden" name="state" value="{{ $request->state }}">
        <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
        <input type="hidden" name="auth_token" value="{{ $authToken }}">
        <button type="submit">Cancel</button>
    </form>
</div>
</body>
</html>
