<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit;
}

// CONFIG DATABASE ()
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

// ESEMPIO: recupera info utente
$stmt = $pdo->prepare("SELECT username,email,created_at FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<style>
* { box-sizing:border-box; margin:0; padding:0; font-family:'Segoe UI',sans-serif;}
body {
    background:#f0f2f5;
    color:#333;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:flex-start;
    padding:20px;
}
.container {
    width:100%; max-width:900px;
    background:#fff;
    padding:40px;
    border-radius:12px;
    box-shadow:0 8px 25px rgba(0,0,0,0.15);
}
h1 { font-weight:600; color:#2c3e50; margin-bottom:20px; text-align:center; }
.section { margin-bottom:30px; }
.section h2 { font-size:1.3em; margin-bottom:10px; color:#2980b9; }
.card {
    background:#f7f9fc;
    padding:15px 20px;
    border-radius:8px;
    margin-bottom:10px;
    box-shadow:0 2px 6px rgba(0,0,0,0.1);
}
.card p { margin:5px 0; }
.logout-btn {
    display:inline-block;
    padding:12px 20px;
    border-radius:8px;
    background:#2980b9;
    color:#fff;
    font-weight:600;
    text-decoration:none;
    transition:0.3s;
}
.logout-btn:hover { background:#1c5980; }
/* Grid di statistiche */
.stats {
    display:grid;
    grid-template-columns: repeat(auto-fit,minmax(150px,1fr));
    gap:15px;
}
.stats .card {
    text-align:center;
    background:#f0f4f8;
}
/* Responsive */
@media(max-width:600px){
    .container { padding:30px 20px; }
}
</style>
</head>
<body>
<div class="container">
    <h1>Ciao <?php echo htmlspecialchars($user['username']); ?>, Benvenuto!</h1>

    <div class="section">
        <h2>Profilo</h2>
        <div class="card"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></div>
        <div class="card"><strong>Registrato il:</strong> <?php echo date('d/m/Y', strtotime($user['created_at'])); ?></div>
    </div>

    <div class="section">
        <h2>Statistiche Account</h2>
        <div class="stats">
            <div class="card"><strong>Esempio</strong><p>25</p></div>
            <div class="card"><strong>Articoli pubblicati</strong><p>non ti interessa</p></div>
            <div class="card"><strong>Messaggi</strong><p>0</p></div>
            <div class="card"><strong>Notifiche</strong><p>attaccalo al database</p></div>
        </div>
    </div>

    <div class="section" style="text-align:center;">
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>
</body>
</html>