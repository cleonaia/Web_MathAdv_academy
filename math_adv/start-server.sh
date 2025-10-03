#!/bin/bash

# Math Advantage - Development Server Starter

echo "🎓 Math Advantage - Servidor de Desenvolupament"
echo "=============================================="
echo ""

# Check if PHP is available
if ! command -v php &> /dev/null; then
    echo "❌ PHP no trobat. Instal·la PHP per continuar."
    exit 1
fi

# Check PHP version
PHP_VERSION=$(php -v | head -n1 | cut -d' ' -f2 | cut -d'.' -f1,2)
echo "📋 PHP Version: $PHP_VERSION"

# Set default port
PORT=${1:-8000}

# Check if port is available
if lsof -Pi :$PORT -sTCP:LISTEN -t >/dev/null ; then
    echo "❌ Port $PORT ja està en ús. Prova un altre port:"
    echo "   ./start-server.sh 8001"
    exit 1
fi

echo "🚀 Iniciant servidor a http://localhost:$PORT"
echo "📁 Document root: $(pwd)"
echo ""
echo "✨ Funcionalitats disponibles:"
echo "   • Web principal: http://localhost:$PORT"
echo "   • Formulari inscripció: http://localhost:$PORT#inscripcio"
echo "   • Chatbot integrat"
echo ""
echo "🛑 Prem Ctrl+C per aturar el servidor"
echo ""

# Start PHP development server
php -S localhost:$PORT