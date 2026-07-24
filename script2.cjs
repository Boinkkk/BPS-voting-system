const fs = require('fs');
let content = fs.readFileSync('resources/views/layouts/app.blade.php', 'utf8');

// 1. Undo the damage
content = content.replace(/- wire:navigate>/g, '->');

// 2. Remove any previously added wire:navigate that were placed correctly but we're going to redo them
content = content.replace(/ wire:navigate/g, '');

// 3. Add wire:navigate correctly to all <a> tags with href="{{ route(...) }}" or href="{{ url(...) }}"
// We need to parse correctly to not break on nested > inside blade tags.
// A safe way is to split by "<a " and then find the first closing ">" that is NOT inside "{{ ... }}"
const parts = content.split('<a ');
let newContent = parts[0];

for (let i = 1; i < parts.length; i++) {
    let part = parts[i];
    
    // Find the end of the opening <a> tag. We iterate char by char to balance {{ and }}
    let inBraces = 0;
    let tagEndIndex = -1;
    for (let j = 0; j < part.length; j++) {
        if (part[j] === '{' && part[j+1] === '{') {
            inBraces++;
            j++;
        } else if (part[j] === '}' && part[j+1] === '}') {
            inBraces--;
            j++;
        } else if (part[j] === '>' && inBraces === 0) {
            tagEndIndex = j;
            break;
        }
    }
    
    if (tagEndIndex !== -1) {
        let tagContent = part.substring(0, tagEndIndex);
        let rest = part.substring(tagEndIndex);
        
        if (tagContent.includes('target="_blank"')) {
            newContent += '<a ' + part;
        } else if (tagContent.includes("route('logout')")) {
            newContent += '<a ' + part;
        } else if (tagContent.includes('href="{{ route') || tagContent.includes('href="{{ url')) {
            newContent += '<a ' + tagContent + ' wire:navigate' + rest;
        } else {
            newContent += '<a ' + part;
        }
    } else {
        newContent += '<a ' + part;
    }
}

fs.writeFileSync('resources/views/layouts/app.blade.php', newContent);
