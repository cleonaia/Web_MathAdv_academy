<?php
session_start();
require_once '../php/config.php';

$error = '';

if ($_POST) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($email) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT id, nom, email, password_hash, rol FROM users WHERE email = ? AND active = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nom'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['rol'];
                
                // Redirect to portal
                header('Location: index.php');
                exit;
            } else {
                $error = 'Email o contrasenya incorrectes';
            }
        } catch (PDOException $e) {
            $error = 'Error de connexió. Torneu-ho a provar.';
        }
    } else {
        $error = 'Tots els camps són obligatoris';
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accés Portal - Math Advantage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #8b5cf6;
            --primary-dark: #7c3aed;
            --gradient-primary: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .login-form {
            padding: 3rem;
        }
        
        .login-image {
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: white;
            padding: 3rem;
            text-align: center;
        }
        
        .math-symbol-login {
            font-size: 4rem;
            margin-bottom: 1rem;
            font-family: 'Times New Roman', serif;
        }
        
        .form-control {
            border-radius: 15px;
            border: 2px solid #e2e8f0;
            padding: 0.8rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
        }
        
        .btn-login {
            background: var(--gradient-primary);
            border: none;
            border-radius: 15px;
            padding: 0.8rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(139, 92, 246, 0.3);
        }
        
        .role-selector {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .role-card {
            flex: 1;
            padding: 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .role-card:hover, .role-card.active {
            border-color: var(--primary-color);
            background: rgba(139, 92, 246, 0.1);
        }
        
        .alert {
            border-radius: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="login-container">
                    <div class="row g-0">
                        <div class="col-md-6">
                            <div class="login-image">
                                <div class="math-symbol-login">∑</div>
                                <h3 class="fw-bold mb-3">Math Advantage</h3>
                                <p class="mb-4">Portal Digital Educatiu</p>
                                <div class="features-list text-start">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <span>Accés 24/7 als materials</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <span>Seguiment personalitzat</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <span>Comunicació directa</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <span>Informes detallats</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="login-form">
                                <h4 class="fw-bold mb-4 text-center">Accedir al Portal</h4>
                                
                                <?php if ($error): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <?php echo htmlspecialchars($error); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope me-2"></i>Correu Electrònic
                                        </label>
                                        <input type="email" class="form-control" id="email" name="email" required 
                                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="password" class="form-label">
                                            <i class="fas fa-lock me-2"></i>Contrasenya
                                        </label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                    
                                    <div class="d-grid mb-3">
                                        <button type="submit" class="btn btn-primary btn-login">
                                            <i class="fas fa-sign-in-alt me-2"></i>Entrar al Portal
                                        </button>
                                    </div>
                                </form>
                                
                                <div class="text-center">
                                    <a href="#" class="text-decoration-none">Has oblidat la contrasenya?</a>
                                </div>
                                
                                <hr class="my-4">
                                
                                <div class="text-center">
                                    <h6 class="fw-bold mb-3">Accés Demostració</h6>
                                    <div class="role-selector">
                                        <div class="role-card" onclick="setDemoCredentials('alumne@math-advantage.com', 'demo123')">
                                            <i class="fas fa-user-graduate d-block mb-1"></i>
                                            <small>Alumne</small>
                                        </div>
                                        <div class="role-card" onclick="setDemoCredentials('familia@math-advantage.com', 'demo123')">
                                            <i class="fas fa-users d-block mb-1"></i>
                                            <small>Família</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setDemoCredentials(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
            
            // Highlight active role
            document.querySelectorAll('.role-card').forEach(card => {
                card.classList.remove('active');
            });
            event.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>