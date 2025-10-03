#!/bin/bash

# Math Advantage - Installation Script
# This script helps set up the Math Advantage platform

echo "🎓 Math Advantage - Script d'Instal·lació"
echo "==========================================="
echo ""

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   echo "❌ No executis aquest script com a root"
   exit 1
fi

# Function to check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Check system requirements
echo "🔍 Verificant requisits del sistema..."

# Check PHP
if command_exists php; then
    PHP_VERSION=$(php -v | head -n1 | cut -d' ' -f2 | cut -d'.' -f1,2)
    echo "✅ PHP $PHP_VERSION detectat"
    
    if (( $(echo "$PHP_VERSION >= 8.0" | bc -l) )); then
        echo "✅ Versió de PHP compatible"
    else
        echo "❌ PHP 8.0+ requerit. Versió actual: $PHP_VERSION"
        exit 1
    fi
else
    echo "❌ PHP no trobat. Instal·la PHP 8.0+"
    exit 1
fi

# Check MySQL
if command_exists mysql; then
    echo "✅ MySQL detectat"
else
    echo "⚠️  MySQL no trobat. Assegura't que estigui instal·lat"
fi

# Check web server
if command_exists apache2 || command_exists httpd; then
    echo "✅ Apache detectat"
elif command_exists nginx; then
    echo "✅ Nginx detectat"
else
    echo "⚠️  No s'ha trobat servidor web (Apache/Nginx)"
fi

echo ""

# Create directories
echo "📁 Creant directoris necessaris..."
mkdir -p uploads/{students,professors,documents}
mkdir -p logs
mkdir -p backups
mkdir -p templates

# Set permissions
echo "🔒 Configurant permisos..."
chmod 755 uploads
chmod 755 logs  
chmod 755 backups
chmod 644 php/*.php
chmod 644 assets/css/*.css
chmod 644 assets/js/*.js

echo "✅ Permisos configurats"
echo ""

# Database setup
echo "🗄️  Configuració de Base de Dades"
echo "================================="
read -p "Vols crear la base de dades ara? (y/n): " create_db

if [[ $create_db == "y" || $create_db == "Y" ]]; then
    read -p "Usuari MySQL (per defecte: root): " db_user
    db_user=${db_user:-root}
    
    read -s -p "Contrasenya MySQL: " db_password
    echo ""
    
    read -p "Nom de la base de dades (per defecte: math_advantage): " db_name
    db_name=${db_name:-math_advantage}
    
    echo "Creant base de dades..."
    
    # Test connection
    if mysql -u "$db_user" -p"$db_password" -e ";" 2>/dev/null; then
        echo "✅ Connexió MySQL exitosa"
        
        # Create database
        mysql -u "$db_user" -p"$db_password" -e "CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
        
        # Import schema
        if [[ -f "database/schema.sql" ]]; then
            mysql -u "$db_user" -p"$db_password" "$db_name" < database/schema.sql
            echo "✅ Esquema de base de dades importat"
        else
            echo "❌ Fitxer schema.sql no trobat"
        fi
        
        # Update config file
        if [[ -f "php/config.php" ]]; then
            # This is a simple replacement, for production use a more robust method
            echo "📝 Actualitzant configuració..."
            echo "⚠️  Recorda actualitzar manualment php/config.php amb:"
            echo "   - Host: localhost"
            echo "   - Database: $db_name"
            echo "   - Username: $db_user"
            echo "   - Password: [la teva contrasenya]"
        fi
        
    else
        echo "❌ Error de connexió MySQL"
        echo "Verifica les credencials i torna-ho a provar"
    fi
fi

echo ""

# Email configuration
echo "📧 Configuració d'Email"
echo "======================="
read -p "Vols configurar l'email ara? (y/n): " setup_email

if [[ $setup_email == "y" || $setup_email == "Y" ]]; then
    read -p "Email del centre: " site_email
    read -p "Nom del centre: " site_name
    read -p "Servidor SMTP (per defecte: smtp.gmail.com): " smtp_host
    smtp_host=${smtp_host:-smtp.gmail.com}
    
    echo ""
    echo "📝 Configuració d'email a actualitzar manualment:"
    echo "   Email: $site_email"
    echo "   Nom: $site_name"
    echo "   SMTP: $smtp_host"
    echo "   ⚠️  Recorda configurar la contrasenya d'aplicació"
fi

echo ""

# WhatsApp configuration  
echo "📱 Configuració de WhatsApp"
echo "==========================="
read -p "Número de WhatsApp (format: 34612345678): " whatsapp_number

if [[ -n "$whatsapp_number" ]]; then
    echo "📝 Número WhatsApp: $whatsapp_number"
    echo "   Recorda actualitzar-lo a php/config.php"
fi

echo ""

# Test installation
echo "🧪 Provant instal·lació..."

# Check if we can start a local server
if command_exists php; then
    echo "Iniciant servidor de desenvolupament..."
    echo "📍 El servidor estarà disponible a: http://localhost:8000"
    echo "🛑 Prem Ctrl+C per aturar el servidor"
    echo ""
    
    # Start server in background for 5 seconds to test
    timeout 5s php -S localhost:8000 >/dev/null 2>&1 &
    sleep 2
    
    if curl -s http://localhost:8000 >/dev/null; then
        echo "✅ Servidor funcionant correctament"
    else
        echo "⚠️  No s'ha pogut verificar el servidor"
    fi
fi

echo ""
echo "🎉 Instal·lació completada!"
echo "=========================="
echo ""
echo "📋 Pròxims passos:"
echo "1. Revisa i actualitza php/config.php"
echo "2. Substitueix les imatges placeholder"
echo "3. Personalitza els textos del web"
echo "4. Configura el teu servidor web"
echo "5. Activa HTTPS"
echo ""
echo "📚 Documentació: README.md"
echo "🔧 Configuració: SETUP.md"
echo ""
echo "🚀 Per iniciar servidor de desenvolupament:"
echo "   php -S localhost:8000"
echo ""
echo "📞 Suport: support@math-advantage.com"