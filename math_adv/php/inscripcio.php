<?php
// Math Advantage - Inscription Form Handler
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Enable error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$config = [
    'database' => [
        'host' => 'localhost',
        'dbname' => 'math_advantage',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ],
    'email' => [
        'smtp_host' => 'smtp.gmail.com',
        'smtp_port' => 587,
        'smtp_username' => 'info@math-advantage.com',
        'smtp_password' => 'your_email_password',
        'from_email' => 'info@math-advantage.com',
        'from_name' => 'Math Advantage',
        'to_email' => 'admissions@math-advantage.com'
    ],
    'whatsapp' => [
        'phone' => '34644789012',
        'enabled' => true
    ]
];

class InscriptionHandler {
    private $db;
    private $config;
    
    public function __construct($config) {
        $this->config = $config;
        $this->connectDatabase();
    }
    
    private function connectDatabase() {
        try {
            $dsn = "mysql:host={$this->config['database']['host']};dbname={$this->config['database']['dbname']};charset={$this->config['database']['charset']}";
            $this->db = new PDO($dsn, $this->config['database']['username'], $this->config['database']['password']);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            // Continue without database - will only send email
            $this->db = null;
        }
    }
    
    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(false, 'Només s\'accepten sol·licituds POST');
        }
        
        try {
            // Validate and sanitize input
            $data = $this->validateInput();
            
            if (!$data) {
                return $this->jsonResponse(false, 'Dades invàlides. Revisa tots els camps obligatoris.');
            }
            
            // Save to database
            $inscription_id = $this->saveToDatabase($data);
            
            // Send confirmation email
            $emailSent = $this->sendConfirmationEmail($data, $inscription_id);
            
            // Send admin notification
            $this->sendAdminNotification($data, $inscription_id);
            
            // Generate WhatsApp link if enabled
            $whatsapp_link = null;
            if ($this->config['whatsapp']['enabled']) {
                $whatsapp_link = $this->generateWhatsAppLink($data);
            }
            
            return $this->jsonResponse(true, 'Sol·licitud rebuda correctament. Et contactarem aviat!', [
                'inscription_id' => $inscription_id,
                'email_sent' => $emailSent,
                'whatsapp_link' => $whatsapp_link
            ]);
            
        } catch (Exception $e) {
            error_log("Inscription error: " . $e->getMessage());
            return $this->jsonResponse(false, 'Error intern del servidor. Torna-ho a provar més tard.');
        }
    }
    
    private function validateInput() {
        $required_fields = ['nom', 'email', 'telefon', 'nivell', 'modalitat'];
        $data = [];
        
        // Check required fields
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                return false;
            }
            $data[$field] = $this->sanitizeInput($_POST[$field]);
        }
        
        // Validate email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        // Validate phone (basic check)
        if (!preg_match('/^[+]?[\d\s\-\(\)]{9,}$/', $data['telefon'])) {
            return false;
        }
        
        // Optional fields
        $optional_fields = ['horari', 'comentaris', 'newsletter'];
        foreach ($optional_fields as $field) {
            $data[$field] = isset($_POST[$field]) ? $this->sanitizeInput($_POST[$field]) : '';
        }
        
        // Convert checkbox values
        $data['newsletter'] = isset($_POST['newsletter']) ? 1 : 0;
        $data['politica'] = isset($_POST['politica']) ? 1 : 0;
        
        if (!$data['politica']) {
            return false; // Privacy policy must be accepted
        }
        
        $data['data_inscripcio'] = date('Y-m-d H:i:s');
        $data['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        return $data;
    }
    
    private function sanitizeInput($input) {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
    
    private function saveToDatabase($data) {
        if (!$this->db) {
            return null; // Database not available, but continue with email
        }
        
        try {
            $sql = "INSERT INTO inscripcions (
                nom, email, telefon, nivell, modalitat, horari, comentaris,
                newsletter, politica, data_inscripcio, ip_address, user_agent, estat
            ) VALUES (
                :nom, :email, :telefon, :nivell, :modalitat, :horari, :comentaris,
                :newsletter, :politica, :data_inscripcio, :ip_address, :user_agent, 'pendent'
            )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nom' => $data['nom'],
                ':email' => $data['email'],
                ':telefon' => $data['telefon'],
                ':nivell' => $data['nivell'],
                ':modalitat' => $data['modalitat'],
                ':horari' => $data['horari'],
                ':comentaris' => $data['comentaris'],
                ':newsletter' => $data['newsletter'],
                ':politica' => $data['politica'],
                ':data_inscripcio' => $data['data_inscripcio'],
                ':ip_address' => $data['ip_address'],
                ':user_agent' => $data['user_agent']
            ]);
            
            return $this->db->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("Database insert failed: " . $e->getMessage());
            return null;
        }
    }
    
    private function sendConfirmationEmail($data, $inscription_id) {
        $subject = 'Confirmació d\'inscripció - Math Advantage';
        
        $html_body = $this->getConfirmationEmailTemplate($data, $inscription_id);
        
        $headers = [
            'From: ' . $this->config['email']['from_name'] . ' <' . $this->config['email']['from_email'] . '>',
            'Reply-To: ' . $this->config['email']['from_email'],
            'Content-Type: text/html; charset=UTF-8',
            'X-Mailer: Math Advantage System'
        ];
        
        return mail($data['email'], $subject, $html_body, implode("\r\n", $headers));
    }
    
    private function sendAdminNotification($data, $inscription_id) {
        $subject = 'Nova inscripció rebuda - ' . $data['nom'];
        
        $html_body = $this->getAdminNotificationTemplate($data, $inscription_id);
        
        $headers = [
            'From: Sistema Math Advantage <noreply@math-advantage.com>',
            'Reply-To: ' . $data['email'],
            'Content-Type: text/html; charset=UTF-8'
        ];
        
        return mail($this->config['email']['to_email'], $subject, $html_body, implode("\r\n", $headers));
    }
    
    private function getConfirmationEmailTemplate($data, $inscription_id) {
        $nivells = [
            '1eso' => '1r ESO',
            '2eso' => '2n ESO',
            '3eso' => '3r ESO',
            '4eso' => '4t ESO',
            '1bat' => '1r Batxillerat',
            '2bat' => '2n Batxillerat',
            'universitari' => 'Universitari'
        ];
        
        $modalitats = [
            'presencial' => 'Presencial',
            'online' => 'Online',
            'mixta' => 'Mixta'
        ];
        
        $horaris = [
            'mati' => 'Matí (9:00-13:00)',
            'tarda' => 'Tarda (16:00-20:00)',
            'vespre' => 'Vespre (20:00-22:00)'
        ];
        
        return "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
        .info-box { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #2563eb; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>Math Advantage</h1>
            <p>Confirmació d'Inscripció</p>
        </div>
        
        <div class='content'>
            <h2>Hola {$data['nom']},</h2>
            
            <p>Gràcies per la teva sol·licitud d'inscripció a Math Advantage. Hem rebut la teva informació correctament.</p>
            
            <div class='info-box'>
                <h3>Detalls de la teva sol·licitud:</h3>
                <ul>
                    <li><strong>Nom:</strong> {$data['nom']}</li>
                    <li><strong>Email:</strong> {$data['email']}</li>
                    <li><strong>Telèfon:</strong> {$data['telefon']}</li>
                    <li><strong>Nivell:</strong> " . ($nivells[$data['nivell']] ?? $data['nivell']) . "</li>
                    <li><strong>Modalitat:</strong> " . ($modalitats[$data['modalitat']] ?? $data['modalitat']) . "</li>
                    " . ($data['horari'] ? "<li><strong>Horari preferit:</strong> " . ($horaris[$data['horari']] ?? $data['horari']) . "</li>" : "") . "
                    " . ($data['comentaris'] ? "<li><strong>Comentaris:</strong> {$data['comentaris']}</li>" : "") . "
                </ul>
                " . ($inscription_id ? "<p><strong>Número de referència:</strong> #" . str_pad($inscription_id, 6, '0', STR_PAD_LEFT) . "</p>" : "") . "
            </div>
            
            <p><strong>Pròxims passos:</strong></p>
            <ol>
                <li>Revisarem la teva sol·licitud en un termini de 24 hores</li>
                <li>Et contactarem per telèfon o email per concretar els detalls</li>
                <li>Programarem una reunió informativa gratuïta</li>
                <li>Et proporcionarem tota la informació sobre horaris i preus</li>
            </ol>
            
            <p>Si tens alguna pregunta urgent, no dubtis en contactar-nos:</p>
            <ul>
                <li>Telèfon: 933 123 456</li>
                <li>Email: info@math-advantage.com</li>
                <li>WhatsApp: 644 789 012</li>
            </ul>
            
            <p>Gràcies per confiar en Math Advantage!</p>
        </div>
        
        <div class='footer'>
            <p>&copy; 2024 Math Advantage. Tots els drets reservats.</p>
            <p>Carrer de les Matemàtiques, 123 - 08001 Barcelona</p>
        </div>
    </div>
</body>
</html>";
    }
    
    private function getAdminNotificationTemplate($data, $inscription_id) {
        return "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #dc2626; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; }
        .info-box { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #dc2626; }
        .urgent { background: #fef2f2; border: 1px solid #fecaca; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>Nova Inscripció Rebuda</h1>
            <p>Math Advantage - Sistema d'Inscripcions</p>
        </div>
        
        <div class='content'>
            <div class='urgent'>
                <strong>⚠️ ACCIÓ REQUERIDA:</strong> Nova sol·licitud d'inscripció rebuda el " . date('d/m/Y H:i') . "
            </div>
            
            <div class='info-box'>
                <h3>Informació de l'Estudiant:</h3>
                <ul>
                    <li><strong>Nom:</strong> {$data['nom']}</li>
                    <li><strong>Email:</strong> <a href='mailto:{$data['email']}'>{$data['email']}</a></li>
                    <li><strong>Telèfon:</strong> <a href='tel:{$data['telefon']}'>{$data['telefon']}</a></li>
                    <li><strong>Nivell:</strong> {$data['nivell']}</li>
                    <li><strong>Modalitat:</strong> {$data['modalitat']}</li>
                    <li><strong>Horari preferit:</strong> {$data['horari']}</li>
                    <li><strong>Newsletter:</strong> " . ($data['newsletter'] ? 'Sí' : 'No') . "</li>
                </ul>
                
                " . ($data['comentaris'] ? "
                <h4>Comentaris de l'estudiant:</h4>
                <p style='background: #f0f0f0; padding: 10px; border-radius: 5px;'>{$data['comentaris']}</p>
                " : "") . "
                
                <h4>Informació tècnica:</h4>
                <ul>
                    <li><strong>Data/Hora:</strong> {$data['data_inscripcio']}</li>
                    " . ($inscription_id ? "<li><strong>ID Inscripció:</strong> #{$inscription_id}</li>" : "") . "
                    <li><strong>IP:</strong> {$data['ip_address']}</li>
                </ul>
            </div>
            
            <div class='info-box'>
                <h3>Accions Recomanades:</h3>
                <ol>
                    <li>Contactar l'estudiant en un termini de 24 hores</li>
                    <li>Verificar disponibilitat d'horaris per al nivell sol·licitat</li>
                    <li>Preparar informació sobre preus i metodologia</li>
                    <li>Programar reunió informativa gratuïta</li>
                </ol>
            </div>
            
            <p style='text-align: center;'>
                <a href='mailto:{$data['email']}' style='background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Respondre per Email</a>
                <a href='tel:{$data['telefon']}' style='background: #10b981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;'>Trucar Ara</a>
            </p>
        </div>
    </div>
</body>
</html>";
    }
    
    private function generateWhatsAppLink($data) {
        $phone = $this->config['whatsapp']['phone'];
        $message = "Hola! Sóc {$data['nom']} i acabde omplir el formulari d'inscripció a la vostra web. M'agradaria obtenir més informació sobre els cursos de {$data['nivell']}.";
        
        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }
    
    private function jsonResponse($success, $message, $data = null) {
        $response = [
            'success' => $success,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        if ($data) {
            $response = array_merge($response, $data);
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Handle the request
try {
    $handler = new InscriptionHandler($config);
    $handler->handleRequest();
} catch (Exception $e) {
    error_log("Fatal error in inscription handler: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error intern del servidor',
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE);
}
?>