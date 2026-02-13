<?php
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiSecret = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pin = $_POST['pin'] ?? '';
    if ($pin === $_ENV['GENERATOR_PIN']) {
        $apiSecret = $_ENV['API_SECRET'];
    } else {
        $error = "Incorrect PIN.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Key</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f0f0; }
        .container { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center; }
        input { padding: 10px; font-size: 16px; margin-right: 10px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px 20px; font-size: 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .key-display { margin-top: 20px; padding: 15px; background: #e8f5e9; border: 1px solid #c8e6c9; border-radius: 4px; color: #2e7d32; word-break: break-all; }
        .error { color: red; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>App Security</h2>
        <?php if ($apiSecret): ?>
            <div class="key-display">
                <strong>Your API Key:</strong><br>
                <code><?php echo htmlspecialchars($apiSecret); ?></code>
            </div>
            <p>Copy this key into your HTML script.</p>
        <?php else: ?>
            <form method="POST">
                <input type="password" name="pin" placeholder="Enter PIN (5014)" required>
                <button type="submit">Reveal Key</button>
            </form>
            <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
