from fastapi import FastAPI
from datetime import datetime
import aiomysql
from database import get_db
from routers import comments
from fastapi.middleware.cors import CORSMiddleware

app = FastAPI(title='Boardy API', version='0.2.0')

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"], # Разрешить все домены (для разработки)
    allow_credentials=True,
    allow_methods=["*"], # Разрешить все методы (GET, POST, PUT, DELETE)
    allow_headers=["*"], # Разрешить все заголовки
)

app.include_router(comments.router)

async def get_db():
    return await aiomysql.connect(**DB_CONFIG)

@app.get('/api/status')
async def status():
    return {'status': 'ok', 'time': str(datetime.now())}

@app.get('/api/messages')
async def get_messages():
    conn = await get_db()
    async with conn.cursor(aiomysql.DictCursor) as cur:
        await cur.execute(
            'SELECT posts.body AS message, users.name, '
            'posts.created_at FROM posts '
            'JOIN users ON posts.author_id = users.id '
            'ORDER BY posts.created_at DESC'
        )
        messages = await cur.fetchall()
    conn.close()
    for m in messages:
        m['created_at'] = str(m['created_at'])
    return {'messages': messages, 'count': len(messages)}

@app.get('/api/users')
async def get_users():
    conn = await get_db()
    async with conn.cursor(aiomysql.DictCursor) as cur:
        await cur.execute(
            'SELECT id, name, email, created_at FROM users'
        )
        users = await cur.fetchall()
    conn.close()
    for u in users:
        u['created_at'] = str(u['created_at'])
    return {'users': users, 'count': len(users)}
