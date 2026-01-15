<?php
session_start();

// CONFIG DATABASE
$host = 'localhost';
$db   = '';
$user = 'TUO_USERNAME';
$pass = 'TUO_PASSWORD';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    die('Connessione fallita: '.$e->getMessage());
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $error = 'Email non valida';
    } else if(strlen($password) < 1){
        $error = 'Inserisci la password';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if($user && password_verify($password,$user['password'])){
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Credenziali errate';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Login</title>
<style>
* { box-sizing:border-box; margin:0; padding:0; font-family:'Segoe UI',sans-serif;}
body {
    display:flex; justify-content:center; align-items:center;
    min-height:100vh; background:#f0f2f5; color:#333;
}
.container {
    width:100%; max-width:400px; background:#fff; padding:40px;
    border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.15);
}
h2 { text-align:center; margin-bottom:30px; font-weight:600; color:#2c3e50; }
input {
    width:100%; padding:12px; margin:10px 0; border-radius:6px;
    border:1px solid #ccc; outline:none; font-size:14px; transition:0.3s;
}
input:focus { border-color:#2980b9; box-shadow:0 0 5px rgba(41,128,185,0.4); }
button {
    width:100%; padding:12px; margin-top:15px; border:none; border-radius:6px;
    background:#2980b9; color:#fff; font-weight:600; cursor:pointer; transition:0.3s;
}
button:hover { background:#1c5980; }
.message { padding:10px; margin:8px 0; border-radius:6px; font-size:0.95em; }
.error { background:#e74c3c; color:#fff; }
.success { background:#27ae60; color:#fff; }
a { display:block; text-align:center; margin-top:15px; color:#2980b9; text-decoration:none; font-size:0.9em; }
a:hover { text-decoration:underline; }
/* Responsive */
@media(max-width:450px){ .container{padding:30px 20px;} }
</style>
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <?php if($error) echo "<div class='message error'>$error</div>"; ?>
    <form id="loginForm" method="POST" novalidate>
        <input name="email" type="email" placeholder="Email" required>
        <input name="password" type="password" placeholder="Password" required>
        <button type="submit">Accedi</button>
    </form>
    <a href="register.php">Non hai un account? Registrati</a>
</div>

<script>
// VALIDAZIONE CLIENT-SIDE
const loginForm = document.getElementById('loginForm');
loginForm.addEventListener('submit', function(e){
    const em = loginForm.email.value.trim();
    const pw = loginForm.password.value;
    let errs = [];
    if(!/\S+@\S+\.\S+/.test(em)) errs.push('Email non valida');
    if(pw.length<1) errs.push('Inserisci la password');
    if(errs.length>0){ alert(errs.join("\n")); e.preventDefault(); }
});
</script>
</body>
</html>
