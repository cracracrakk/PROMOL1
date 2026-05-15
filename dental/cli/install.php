<?php
// CLI de instalación: crea la BD, importa schema y seed.
// Uso:  php cli/install.php  (desde la raíz del proyecto)

if (PHP_SAPI !== 'cli') { exit("Solo desde CLI.\n"); }

$config = require __DIR__ . '/../app/config/config.php';
$GLOBALS['app_config'] = $config;

require __DIR__ . '/../app/core/helpers.php';

$db = $config['db'];

echo "DentalCore - Instalación\n";
echo "=========================\n";
echo "Host: {$db['host']}:{$db['port']}\n";
echo "DB:   {$db['database']}\n\n";

try {
    $pdo = new PDO(
        "mysql:host={$db['host']};port={$db['port']};charset=utf8mb4",
        $db['username'], $db['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (Throwable $e) {
    fwrite(STDERR, "ERROR: no se pudo conectar a MySQL.\n" . $e->getMessage() . "\n");
    exit(1);
}

echo "Importando schema.sql... ";
$sql = file_get_contents(__DIR__ . '/../database/schema.sql');
$pdo->exec($sql);
echo "OK\n";

echo "Importando seed.sql... ";
$sql = file_get_contents(__DIR__ . '/../database/seed.sql');
$pdo->exec($sql);
echo "OK\n";

echo "Importando schema_v2.sql (extensiones)... ";
$sql = file_get_contents(__DIR__ . '/../database/schema_v2.sql');
$pdo->exec($sql);
echo "OK\n\n";

echo "Listo. Accede al sistema con:\n";
echo "  Email:    admin@dental.local\n";
echo "  Password: admin123\n\n";
echo "Recuerda apuntar tu DocumentRoot a:\n";
echo "  " . realpath(__DIR__ . '/../public') . "\n";
