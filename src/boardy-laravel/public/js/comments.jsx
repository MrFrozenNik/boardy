import { startLogin, refreshToken } from '/js/auth.js'

const API = 'https://api.nfrozensky.ai-info.ru'
const WS_URL = API.replace(/^http/, 'ws') + '/ws'


function decodeSub(token) {
    try {
        const p = token.split('.')[1].replace(/-/g, '+').replace(/_/g, '/')
        return String(JSON.parse(atob(p)).sub)
    } catch { return null }
}

function Comments({ postId, userName }) {
    const [token, setToken] = React.useState(() => sessionStorage.getItem('access_token'))
    const [comments, setComments] = React.useState([])
    const [text, setText] = React.useState('')
    const [editingId, setEditingId] = React.useState(null)
    const [editText, setEditText] = React.useState('')

    const uid = token ? decodeSub(token) : null

    React.useEffect(() => {
        fetch(`${API}/api/posts/${postId}/comments`)
            .then(r => r.json())
            .then(d => setComments(Array.isArray(d) ? d : (d.items ?? [])))
            .catch(err => { console.error('Не удалось загрузить комментарии:', err); setComments([]) })
    }, [postId])

    React.useEffect(() => {
        let ws, alive = true, reconnectTimer
        function connect() {
            ws = new WebSocket(WS_URL)
            ws.onopen = () => console.log('✓ comments WS connected')
            ws.onmessage = (e) => {
                const msg = JSON.parse(e.data)
                if (msg.type === 'new_comment') {
                    const c = msg.comment
                    if (Number(c.post_id) !== Number(postId)) return
                    setComments(prev => prev.some(x => x.id === c.id) ? prev : [...prev, c])
                } else if (msg.type === 'update_comment') {
                    const { id, body } = msg.comment
                    setComments(prev => prev.map(x => x.id === id ? { ...x, body } : x))
                } else if (msg.type === 'delete_comment') {
                    setComments(prev => prev.filter(x => x.id !== msg.comment_id))
                } else if (msg.type === 'user_renamed') {
                    setComments(prev => prev.map(x =>
                        String(x.author_id) === String(msg.user_id) ? { ...x, author_name: msg.new_name } : x))
                }
            }
            ws.onclose = () => { if (alive) reconnectTimer = setTimeout(connect, 3000) }
            ws.onerror = () => ws.close()
        }
        connect()
        return () => { alive = false; clearTimeout(reconnectTimer); if (ws) ws.close() }
    }, [postId])

    async function authedFetch(url, options = {}) {
        let res = await fetch(url, { ...options, headers: { ...options.headers, 'Authorization': 'Bearer ' + token } })
        if (res.status === 401) {
            const nt = await refreshToken()
            if (!nt) return null
            setToken(nt); sessionStorage.setItem('access_token', nt)
            res = await fetch(url, { ...options, headers: { ...options.headers, 'Authorization': 'Bearer ' + nt } })
        }
        return res
    }

    async function addComment(e) {
        e.preventDefault()
        if (!text.trim()) return
        const res = await authedFetch(`${API}/api/posts/${postId}/comments`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ body: text, author_name: userName }),
        })
        if (res && res.ok) setText('')
    }

    async function saveEdit(id) {
        if (!editText.trim()) return
        const res = await authedFetch(`${API}/api/comments/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ body: editText }),
        })
        if (res && res.ok) { setEditingId(null); setEditText('') }
        else if (res && res.status === 403) alert('Это не ваш комментарий (403)')
    }

    async function removeComment(id) {
        if (!confirm('Удалить комментарий?')) return
        const res = await authedFetch(`${API}/api/comments/${id}`, { method: 'DELETE' })
        if (res && res.status === 403) alert('Это не ваш комментарий (403)')
    }

    return (
        <div>
            <ul style={{ listStyle: 'none', padding: 0 }}>
                {comments.map(c => (
                    <li key={c.id} style={{ marginBottom: 8 }}>
                        {editingId === c.id ? (
                            <span>
                                <input value={editText} onChange={e => setEditText(e.target.value)} />
                                <button onClick={() => saveEdit(c.id)}>Сохранить</button>
                                <button onClick={() => setEditingId(null)}>Отмена</button>
                            </span>
                        ) : (
                            <span>
                                <b>{c.author_name}:</b> {c.body}
                                {uid && String(c.author_id) === uid && (
                                    <span style={{ marginLeft: 8 }}>
                                        <button onClick={() => { setEditingId(c.id); setEditText(c.body) }}>✎</button>
                                        <button onClick={() => removeComment(c.id)}>🗑</button>
                                    </span>
                                )}
                            </span>
                        )}
                    </li>
                ))}
            </ul>

            {token ? (
                <form onSubmit={addComment}>
                    <input value={text} onChange={e => setText(e.target.value)} placeholder="Комментарий…" />
                    <button type="submit">Отправить</button>
                </form>
            ) : (
                <button onClick={startLogin}>Войти для комментирования</button>
            )}
        </div>
    )
}

const el = document.getElementById('comments-app')
if (el) {
    ReactDOM.createRoot(el).render(
        <Comments postId={Number(el.dataset.postId)} userName={el.dataset.userName} />
    )
}
