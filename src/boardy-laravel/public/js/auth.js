import { generateVerifier, generateChallenge, generateState } from './pkce.js'

const CLIENT_ID = document.querySelector('meta[name="oauth-client-id"]').content
const REDIRECT_URI = window.location.origin + '/oauth/callback'

const form = (obj) => new URLSearchParams(obj)

export async function startLogin() {
    const verifier = generateVerifier()
    const challenge = await generateChallenge(verifier)
    const state = generateState()

    sessionStorage.setItem('pkce_verifier', verifier)
    sessionStorage.setItem('oauth_state', state)
    sessionStorage.setItem('oauth_return', window.location.pathname + window.location.search)

    const params = new URLSearchParams({
        client_id: CLIENT_ID,
        response_type: 'code',
        redirect_uri: REDIRECT_URI,
        code_challenge: challenge,
        code_challenge_method: 'S256',
        state,
        scope: '',
    })
    window.location = '/oauth/authorize?' + params
}

export async function handleCallback() {
    const params = new URLSearchParams(window.location.search)
    const code = params.get('code')
    const state = params.get('state')
    if (!code) return null

    const savedState = sessionStorage.getItem('oauth_state')
    if (state !== savedState) throw new Error('Invalid state — CSRF attack?')

    const verifier = sessionStorage.getItem('pkce_verifier')
    if (!verifier) throw new Error('No verifier in sessionStorage')

    const res = await fetch('/oauth/token', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        credentials: 'include',
        body: form({
            grant_type: 'authorization_code',
            client_id: CLIENT_ID,
            redirect_uri: REDIRECT_URI,
            code_verifier: verifier,
            code,
        }),
    })
    if (!res.ok) throw new Error('Token exchange failed: ' + await res.text())
    const data = await res.json()

    sessionStorage.removeItem('pkce_verifier')
    sessionStorage.removeItem('oauth_state')
    return data.access_token
}

export async function refreshToken() {
    const res = await fetch('/oauth/token', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: form({
            grant_type: 'refresh_token',
            client_id: CLIENT_ID,
            scope: '',
        }),
    })
    if (!res.ok) { startLogin(); return null }
    const data = await res.json()
    return data.access_token
}
