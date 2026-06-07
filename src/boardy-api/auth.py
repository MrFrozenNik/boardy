import jwt
from fastapi import Header, HTTPException

with open('/opt/boardy-api/oauth-public.key', 'r', encoding='utf-8') as f:
    PUBLIC_KEY = f.read().strip()

async def get_current_user(authorization: str = Header(None)):
    if not authorization or not authorization.startswith('Bearer '):
        raise HTTPException(401, 'Token required')
    token = authorization.split(' ')[1]
    try:
        payload = jwt.decode(
            token,
            PUBLIC_KEY.encode('utf-8'),
            algorithms=['RS256'],
            options={'verify_aud': False}
        )
        return payload
    except jwt.ExpiredSignatureError:
        raise HTTPException(401, 'Token expired')
    except jwt.InvalidTokenError as e:
        raise HTTPException(401, f'Invalid token: {e}')
