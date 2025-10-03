# Math Advantage - Plataforma Web Completa

## 📋 Descripció del Projecte

Math Advantage és una plataforma web completa per a un centre d'estudis especialitzat en matemàtiques. El projecte inclou un lloc web modern i responsive, un sistema de gestió digital, portal de famílies, automatitzacions i analítica.

## 🎯 Objectius del Projecte

- **Web moderna i responsive** que reflecteixi la qualitat del centre
- **Sistema de gestió digital** per automatitzar processos
- **Portal de famílies** per millorar la comunicació
- **Automatitzacions** per estalviar temps administratiu
- **Analítiques** per prendre millors decisions

## 🚀 Fases de Desenvolupament

### ✅ Fase 1: Web Renovada i Responsive (COMPLETADA)
- [x] Disseny modern i atractiu
- [x] Totalment responsive (mòbil, tauleta, escriptori)
- [x] Formulari d'inscripció digital
- [x] Integració amb WhatsApp
- [x] SEO optimitzat
- [x] Chatbot intel·ligent integrat

### ⏳ Fase 2: Gestió Digital i Automatització (EN PROGRÉS)
- [ ] Backend PHP complet
- [ ] Base de dades MySQL
- [ ] Sistema d'inscripcions automàtic
- [ ] Gestió d'estudiants i professors
- [ ] Automatització d'emails i notifications

### 📋 Fase 3: Àrea Privada i Portal de Famílies
- [ ] Sistema d'autenticació segur
- [ ] Portal d'estudiants
- [ ] Portal de famílies
- [ ] Portal de professors
- [ ] Dashboard d'administració

### 🔧 Fase 4: Millores Avançades
- [ ] Integració de pagaments online
- [ ] Sistema d'avaluacions digitals
- [ ] App mòbil bàsica
- [ ] Portal Erasmus+

### 📊 Fase 5: Analítica i Optimització
- [ ] Dashboard d'analítiques
- [ ] Sistema d'informes
- [ ] Optimització de rendiment
- [ ] Suport i manteniment

## 🛠️ Tecnologies Utilitzades

### Frontend
- **HTML5** - Estructura semàntica
- **CSS3** - Estils moderns amb variables CSS
- **JavaScript (ES6+)** - Interactivitat i funcionalitats
- **Bootstrap 5** - Framework CSS responsive
- **Font Awesome** - Icones
- **Google Fonts** - Tipografia (Inter)

### Backend
- **PHP 8+** - Llenguatge del servidor
- **MySQL/MariaDB** - Base de dades
- **PDO** - Accés a base de dades
- **JSON** - Intercanvi de dades
- **SMTP** - Enviament d'emails

### Eines de Desenvolupament
- **Git** - Control de versions
- **VS Code** - Editor de codi
- **phpMyAdmin** - Gestió de base de dades
- **Postman** - Testing d'APIs

## 📁 Estructura del Projecte

```
math_adv/
├── .github/
│   └── copilot-instructions.md    # Instruccions per Copilot
├── assets/
│   ├── css/
│   │   └── styles.css            # Estils principals
│   ├── js/
│   │   └── main.js               # JavaScript principal
│   └── images/                   # Imatges del projecte
├── database/
│   ├── schema.sql                # Esquema de la base de dades
│   └── migrations/               # Migracions de BD
├── php/
│   ├── config.php                # Configuració general
│   ├── inscripcio.php            # Processament inscripcions
│   ├── classes/                  # Classes PHP
│   ├── models/                   # Models de dades
│   ├── controllers/              # Controladors
│   └── utils/                    # Utilitats
├── templates/                    # Plantilles d'email
├── uploads/                      # Fitxers pujats
├── logs/                         # Logs del sistema
├── backups/                      # Còpies de seguretat
├── admin/                        # Panell d'administració
└── portal/                       # Portals d'usuaris
```

## ⚙️ Instal·lació i Configuració

### Requisits del Sistema
- **Servidor web** (Apache/Nginx)
- **PHP 8.0+** amb extensions:
  - PDO
  - MySQL
  - mbstring
  - openssl
  - curl
- **MySQL 8.0+** o **MariaDB 10.4+**
- **Composer** (opcional)

### Passos d'Instal·lació

1. **Clona el repositori**
   ```bash
   git clone https://github.com/your-repo/math-advantage.git
   cd math-advantage
   ```

2. **Configura la base de dades**
   ```bash
   # Crea la base de dades
   mysql -u root -p < database/schema.sql
   ```

3. **Configura PHP**
   ```php
   // Edita php/config.php amb les teves dades
   $config['database']['host'] = 'localhost';
   $config['database']['username'] = 'tu_usuari';
   $config['database']['password'] = 'tu_contrasenya';
   ```

4. **Configura permisos**
   ```bash
   chmod 755 uploads/
   chmod 755 logs/
   chmod 755 backups/
   ```

5. **Configura el servidor web**
   - Apunta el document root a la carpeta del projecte
   - Configura URL rewriting si és necessari

## 🔧 Configuració Avançada

### Email (SMTP)
Edita `php/config.php`:
```php
$config['email'] = [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_username' => 'info@math-advantage.com',
    'smtp_password' => 'your_app_password',
    // ...
];
```

### WhatsApp
```php
$config['whatsapp'] = [
    'phone' => '34644789012',
    'enabled' => true
];
```

### Chatbot
El chatbot està integrat i funciona amb respostes predefinides. Per millores futures es pot integrar amb:
- OpenAI GPT
- DialogFlow
- Rasa
- IBM Watson

## 📝 Funcionalitats Principals

### 1. Web Pública
- **Pàgina principal** amb informació del centre
- **Seccions de cursos** per cada nivell
- **Informació de metodologia** i equip
- **Formulari d'inscripció** integrat
- **Sistema de contacte** multicanal
- **Chatbot intel·ligent** 24/7

### 2. Sistema d'Inscripcions
- **Formulari validat** amb tots els camps necessaris
- **Emails de confirmació** automàtics
- **Notificacions a l'equip** d'administració
- **Integració WhatsApp** per seguiment
- **Base de dades** per gestionar leads

### 3. Gestió d'Estudiants
- **Perfils complets** d'estudiants
- **Historial acadèmic**
- **Seguiment de pagaments**
- **Comunicació** amb famílies
- **Assistència** a classes

### 4. Portal de Famílies
- **Accés segur** amb credencials
- **Informació del fill/a**
- **Horaris i classes**
- **Comunicació** amb professors
- **Pagaments** i facturació

### 5. Panel d'Administració
- **Dashboard principal** amb mètriques
- **Gestió d'estudiants** i professors
- **Programació de classes**
- **Facturació i pagaments**
- **Informes i estadístiques**

## 🔒 Seguretat

### Mesures Implementades
- **Validació d'entrada** en tots els formularis
- **Prepared statements** per evitar SQL injection
- **Sanitització** de dades d'entrada
- **Headers de seguretat** HTTP
- **Logging** d'activitat sospitosa
- **Rate limiting** per evitar spam

### Recomanacions Addicionals
- Usar **HTTPS** en producció
- Configurar **CSP** (Content Security Policy)
- Implementar **2FA** per administradors
- **Backups** regulars automàtics
- **Monitorització** del sistema

## 📊 Analítiques i Mètriques

### Mètriques Clau
- **Visites** a la web
- **Conversions** d'inscripcions
- **Engagement** dels usuaris
- **Satisfacció** de famílies
- **Rendiment** acadèmic

### Eines Integrades
- **Google Analytics** per web
- **Dashboard intern** per gestió
- **Informes** personalitzats
- **Alertes** automàtiques

## 🚀 Desplegament

### Entorn de Desenvolupament
```bash
# Servidor local amb PHP
php -S localhost:8000

# O amb XAMPP/MAMP/WAMP
# Copia els fitxers a htdocs/
```

### Entorn de Producció
1. **Servidor** (VPS/Hosting compartit)
2. **Domini** configurat
3. **SSL/TLS** activat
4. **Base de dades** en servidor separat (recomanat)
5. **Backups** automàtics
6. **Monitorització** activa

## 🧪 Testing

### Tests Manuals
- [ ] Formulari d'inscripció
- [ ] Enviament d'emails
- [ ] Integració WhatsApp
- [ ] Chatbot
- [ ] Responsive design

### Tests Automàtics (Futur)
- Unit tests per funcions PHP
- Integration tests per APIs
- E2E tests per workflows crítics

## 📞 Suport i Manteniment

### Inclòs el Primer Any
- **Hosting i domini** (opcional)
- **Backups** diaris automàtics
- **Actualitzacions** de seguretat
- **Suport tècnic** per email
- **Formació** per a l'equip

### Posterior al Primer Any
- **Manteniment** mensual: 50€/mes
- **Suport** extès: 30€/hora
- **Noves funcionalitats** a pressupost

## 🎨 Personalització

### Canvis de Disseny
- Edita `assets/css/styles.css`
- Canvia colors a les variables CSS
- Afegeix/modifica seccions HTML

### Contingut
- Edita textos a `index.html`
- Afegeix imatges a `assets/images/`
- Modifica configuració a `php/config.php`

## 🔄 Actualitzacions i Versions

### Versió 1.0 (Actual)
- Web responsive completa
- Sistema d'inscripcions
- Chatbot integrat

### Versions Futures
- **1.1**: Portal de famílies
- **1.2**: Sistema de pagaments
- **1.3**: App mòbil
- **2.0**: Plataforma completa

## 📧 Contacte

Per a suport tècnic o consultes:
- **Email**: dev@math-advantage.com
- **Telèfon**: 933 123 456
- **WhatsApp**: 644 789 012

## 📄 Llicència

Aquest projecte és propietat de Math Advantage. Tots els drets reservats.

---

**Math Advantage** - Excel·lència en l'educació matemàtica des de 2009