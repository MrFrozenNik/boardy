const { useState, useEffect } = React;
const API = 'https://api.nfrozensky.ai-info.ru';
const PARENT_ID = 9;

function ItemList() {
    const [items, setItems] = useState([]);
    const [text, setText] = useState('');
    const [editId, setEditId] = useState(null);
    const [editText, setEditText] = useState('');

    const load = async () => {
        const res = await fetch(`${API}/api/posts/${PARENT_ID}/comments`);
        const data = await res.json();
        setItems(data.items);
    };

    useEffect(() => { load(); }, []);

    const add = async () => {
        if (!text.trim()) return;
        await fetch(`${API}/api/posts/${PARENT_ID}/comments`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({body: text})
        });
        setText('');
        load();
    };

    const save = async (id) => {
        await fetch(`${API}/api/comments/${id}`, {
            method: 'PUT',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({body: editText})
        });
        setEditId(null);
        load();
    };

    const del = async (id) => {
        if (!confirm('Удалить комментарий?')) return;
        await fetch(`${API}/api/comments/${id}`, { method: 'DELETE' });
        load();
    };

    return (
        <div className="row">
            <div className="col-md-8">
                {items.map(item => (
                    <div key={item.id} className="card mb-3 shadow-sm">
                        <div className="card-body">
                            <h6 className="card-subtitle mb-2 text-muted">{item.author_name}</h6>
                            {editId === item.id ? (
                                <div className="input-group">
                                    <input className="form-control" value={editText} onChange={e => setEditText(e.target.value)} />
                                    <button className="btn btn-success" onClick={() => save(item.id)}>✅</button>
                                    <button className="btn btn-secondary" onClick={() => setEditId(null)}>❌</button>
                                </div>
                            ) : (
                                <div className="d-flex justify-content-between align-items-center">
                                    <p className="card-text mb-0">{item.body}</p>
                                    <div>
                                        <button className="btn btn-sm btn-outline-primary me-1" onClick={() => { setEditId(item.id); setEditText(item.body); }}>✏️</button>
                                        <button className="btn btn-sm btn-outline-danger" onClick={() => del(item.id)}>🗑️</button>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                ))}

                <div className="input-group mt-4 shadow-sm">
                    <input className="form-control" placeholder="Написать комментарий..." value={text} onChange={e => setText(e.target.value)} />
                    <button className="btn btn-primary" onClick={add}>Отправить</button>
                </div>
            </div>
        </div>
    );
}

const root = ReactDOM.createRoot(document.getElementById('app'));
root.render(<ItemList />);
