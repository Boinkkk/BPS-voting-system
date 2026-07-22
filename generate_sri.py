import urllib.request
import hashlib
import base64

url = 'https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js'
req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
try:
    with urllib.request.urlopen(req) as response:
        content = response.read()
        digest = hashlib.sha384(content).digest()
        b64 = base64.b64encode(digest).decode('utf-8')
        print(f'integrity="sha384-{b64}"')
except Exception as e:
    print('Failed:', e)
