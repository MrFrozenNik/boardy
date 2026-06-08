from fastapi import FastAPI
from datetime import datetime
from contextlib import asynccontextmanager
import asyncio
import json
import aiomysql
import redis.asyncio as aioredis
from database import get_db
from routers import comments
from routers import ws
from fastapi.middleware.cors import CORSMiddleware
import os
import logging
logger = logging.getLogger('uvicorn.error')

async def redis_subscriber():
    while True:
        try:
            redis_host = os.getenv('REDIS_HOST', 'redis')
            r = aioredis.from_url(f'redis://{redis_host}:6379')
            pubsub = r.pubsub()
            await pubsub.subscribe('new_post', 'user.renamed')
            logger.info('[startup] Redis subscriber запущен')

            while True:
                message = await pubsub.get_message(ignore_subscribe_messages=True, timeout=1.0)
                if message is None or message['type'] != 'message':
                    continue

                channel = message['channel'].decode()
                data = json.loads(message['data'])
                logger.info(f'[redis] событие: {channel} {data}')

                if channel == 'new_post':
                    await ws.manager.broadcast({'type': 'new_post', 'post': data})

                elif channel == 'user.renamed':
                    conn = await get_db()
                    async with conn.cursor() as cur:
                        await cur.execute(
                            'UPDATE comments SET author_name=%s WHERE author_id=%s',
                            (data['new_name'], data['id'])
                        )
                        await conn.commit()
                    conn.close()
                    await ws.manager.broadcast({
                        'type': 'user_renamed',
                        'user_id': data['id'],
                        'new_name': data['new_name'],
                    })
        except asyncio.CancelledError:
            raise
        except Exception as e:
            logger.error(f'[redis] subscriber упал: {e!r}, переподключение через 3с')
            await asyncio.sleep(3)


@asynccontextmanager
async def lifespan(app: FastAPI):
    task = asyncio.create_task(redis_subscriber())
    yield
    task.cancel()


app = FastAPI(title='Boardy API', version='0.5.0', lifespan=lifespan)

app.add_middleware(
    CORSMiddleware,
    allow_origins=[
        'http://localhost',
	'http://150.241.70.235',
        'https://nfrozensky.ai-info.ru',
    ],
    allow_credentials=True,
    allow_methods=['*'],
    allow_headers=['*'],
)

app.include_router(comments.router)
app.include_router(ws.router)



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
        await cur.execute('SELECT id, name, email, created_at FROM users')
        users = await cur.fetchall()
    conn.close()
    for u in users:
        u['created_at'] = str(u['created_at'])
    return {'users': users, 'count': len(users)}
