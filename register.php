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

// LISTA PROVIDER CONSENTITI (non mostrata all'utente)
$allowed_domains = ['gmail.com','libero.it','outlook.com','virgilio.it'];

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    // VALIDAZIONI SERVER
    if(strlen($username) < 3) $errors[] = 'Username troppo corto';
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[] = 'Email non valida';
    else {
        $domain = strtolower(substr(strrchr($email,'@'),1));
        if(!in_array($domain,$allowed_domains)) $errors[] = 'Tipo di email non consentito';
    }
    if(strlen($password) < 8) $errors[] = 'Password troppo corta';
    if($password !== $confirm) $errors[] = 'Le password non coincidono';

    if(empty($errors)){
        $hash = password_hash($password,PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username,email,password) VALUES (?,?,?)");
        try {
            $stmt->execute([$username,$email,$hash]);
            $success = 'Registrazione completata! Reindirizzamento...';
            header("refresh:2; url=login.php");
        } catch(Exception $e){
            $errors[] = 'Username o email già esistenti';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Registrazione</title>
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
    <h2>Registrazione</h2>
    <?php
    if($errors) foreach($errors as $e) echo "<div class='message error'>$e</div>";
    if($success) echo "<div class='message success'>$success</div>";
    ?>
    <form id="registerForm" method="POST" novalidate>
        <input name="username" placeholder="Username" required>
        <input name="email" type="email" placeholder="Email" required>
        <input name="password" type="password" placeholder="Password" required>
        <input name="confirm" type="password" placeholder="Conferma Password" required>
        <button type="submit">Registrati</button>
    </form>
    <a href="login.php">Hai già un account? Accedi</a>
</div>

<script>
// VALIDAZIONE CLIENT-SIDE
const form = document.getElementById('registerForm');
const allowedDomains = ['gmail.com','libero.it','outlook.com','virgilio.it'];
form.addEventListener('submit',function(e){
    const u = form.username.value.trim();
    const em = form.email.value.trim();
    const p = form.password.value;
    const c = form.confirm.value;
    let errs = [];
    if(u.length<3) errs.push('Username minimo 3 caratteri');
    if(!/\S+@\S+\.\S+/.test(em)) errs.push('Email non valida');
    else{
        const dom = em.split('@')[1]?.toLowerCase();
        if(!allowedDomains.includes(dom)) errs.push('Tipo di email non consentito');
    }
    if(p.length<8) errs.push('Password minimo 8 caratteri');
    if(p!==c) errs.push('Le password non coincidono');
    if(errs.length>0){ alert(errs.join("\n")); e.preventDefault(); }
});
</script>
</body>
</html>
