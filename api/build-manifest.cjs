const fs = require('fs');
const path = require('path');

try {
    const manifestPath = path.join(__dirname, '../public/build/manifest.json');
    if (fs.existsSync(manifestPath)) {
        const manifest = fs.readFileSync(manifestPath, 'utf8');
        const phpContent = `<?php return <<<'JSON'\n${manifest}\nJSON;\n`;
        fs.writeFileSync(path.join(__dirname, 'manifest_data.php'), phpContent);
        console.log('Successfully compiled Vite manifest to api/manifest_data.php');
    } else {
        console.error('Vite manifest not found at ' + manifestPath);
        process.exit(1);
    }
} catch (e) {
    console.error('Failed to compile Vite manifest:', e);
    process.exit(1);
}
