const fs = require('fs');
let content = fs.readFileSync('resources/views/layouts/app.blade.php', 'utf8');
content = content.replace(/<a([^>]+)href="([^"]+)"([^>]*)>/g, (match, p1, p2, p3) => {
    if (match.includes('target="_blank"')) return match;
    if (match.includes('wire:navigate')) return match;
    if (match.includes('route(\'logout\')')) return match;
    if (p2.startsWith('{{ route(') || p2.startsWith('{{ url(')) {
        return `<a${p1}href="${p2}"${p3} wire:navigate>`;
    }
    return match;
});
fs.writeFileSync('resources/views/layouts/app.blade.php', content);
