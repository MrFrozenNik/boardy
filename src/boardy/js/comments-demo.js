const API = 'https://api.nfrozensky.ai-info.ru'; 

const PARENT_ID = 9;

function esc(str) {

    const div = document.createElement('div');

    div.textContent = str;

    return div.innerHTML;

}



async function loadItems() {

    const res = await fetch(`${API}/api/posts/${PARENT_ID}/comments`);

    const data = await res.json();

    document.getElementById('list').innerHTML = data.items.map(item => `

        <div style="margin-bottom: 10px; border-bottom: 1px solid #ccc;">

            <strong>${esc(item.author_name)}</strong>

            <p>${esc(item.body)}</p>

        </div>

    `).join('');

}



document.getElementById('btn').addEventListener('click', async () => {

    const bodyInput = document.getElementById('body');

    const body = bodyInput.value.trim();

    if (!body) return;



    await fetch(`${API}/api/posts/${PARENT_ID}/comments`, {

        method: 'POST',

        headers: {'Content-Type': 'application/json'},

        body: JSON.stringify({body: body})

    });



    bodyInput.value = '';

    loadItems();

});



loadItems();
