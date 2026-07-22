import re
with open('c:/BPS/voting/web/resources/views/layouts/app.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()
scripts = re.findall(r'<script[^>]*src=[\"\'](https?://[^\"\']+)[\"\'][^>]*>', content)
links = re.findall(r'<link[^>]*href=[\"\'](https?://[^\"\']+)[\"\'][^>]*>', content)
print('SCRIPTS:', scripts)
print('LINKS:', links)
