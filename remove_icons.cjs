const fs = require('fs');
const path = require('path');

const directories = [
    'resources/js/pages',
    'resources/js/pages/Admin',
    'resources/js/pages/auth'
];

const emojisToRemove = ['📊', '🎯', '📄', '⚙️', '🚪', '📷', '✅', '❌'];

directories.forEach(dir => {
    const fullDir = path.join(__dirname, dir);
    if (fs.existsSync(fullDir)) {
        const files = fs.readdirSync(fullDir);
        files.forEach(file => {
            if (file.endsWith('.svelte')) {
                const filePath = path.join(fullDir, file);
                let content = fs.readFileSync(filePath, 'utf8');
                
                // Remove SVG tags
                const originalLength = content.length;
                content = content.replace(/<svg[\s\S]*?<\/svg>/g, '');
                
                // Remove Emojis
                emojisToRemove.forEach(emoji => {
                    // Use a global regex to remove the emoji. Sometimes emojis have variant selectors so we just do a simple replace.
                    content = content.split(emoji).join('');
                });

                if (content.length !== originalLength) {
                    fs.writeFileSync(filePath, content, 'utf8');
                    console.log(`Removed icons from ${filePath}`);
                }
            }
        });
    }
});

console.log('Icon removal complete.');
