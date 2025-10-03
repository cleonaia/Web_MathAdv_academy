const http = require('http');
const fs = require('fs');
const path = require('path');

const server = http.createServer((req, res) => {
    // Get the file path
    let filePath = '.' + req.url;
    if (filePath === './') {
        filePath = './index.html';
    }

    // Get file extension
    const extname = String(path.extname(filePath)).toLowerCase();
    
    // Set content type based on file extension
    const mimeTypes = {
        '.html': 'text/html',
        '.js': 'text/javascript',
        '.css': 'text/css',
        '.json': 'application/json',
        '.png': 'image/png',
        '.jpg': 'image/jpg',
        '.gif': 'image/gif',
        '.svg': 'image/svg+xml',
        '.wav': 'audio/wav',
        '.mp4': 'video/mp4',
        '.woff': 'application/font-woff',
        '.ttf': 'application/font-ttf',
        '.eot': 'application/vnd.ms-fontobject',
        '.otf': 'application/font-otf',
        '.wasm': 'application/wasm'
    };

    const contentType = mimeTypes[extname] || 'application/octet-stream';

    // Read file
    fs.readFile(filePath, (error, content) => {
        if (error) {
            if (error.code === 'ENOENT') {
                // File not found
                res.writeHead(404, { 'Content-Type': 'text/html' });
                res.end('<h1>404 - Archivo no encontrado</h1>', 'utf-8');
            } else {
                // Server error
                res.writeHead(500);
                res.end(`Error del servidor: ${error.code}`, 'utf-8');
            }
        } else {
            // Success
            res.writeHead(200, { 'Content-Type': contentType });
            res.end(content, 'utf-8');
        }
    });
});

const PORT = 8000;
server.listen(PORT, () => {
    console.log('🎓 Math Advantage - Servidor iniciado');
    console.log('===============================================');
    console.log(`🌐 Web disponible en: http://localhost:${PORT}`);
    console.log('📱 También puedes usar: http://127.0.0.1:' + PORT);
    console.log('');
    console.log('✨ Características del diseño científico:');
    console.log('   • Color lila protagonista');
    console.log('   • Logo científico con ∑ y partículas');
    console.log('   • Efectos glassmorphism');
    console.log('   • Chatbot Dr. Pythagoras ∫AI');
    console.log('   • Símbolos matemáticos animados');
    console.log('');
    console.log('🛑 Presiona Ctrl+C para detener el servidor');
});

// Handle graceful shutdown
process.on('SIGINT', () => {
    console.log('\n\n👋 Cerrando servidor Math Advantage...');
    console.log('¡Gracias por usar Math Advantage!');
    process.exit(0);
});