<?php
	/*session_start();
	ob_start();

	$conn = mysqli_connect('localhost','root','Nachiyar*1984','ntr_new');*/
 ?>

<?php
	if (session_status() === PHP_SESSION_NONE) {
	    session_start();
	}
	ob_start();

	$envPath = dirname(__DIR__) . '/.env';
	if (!is_readable($envPath)) {
	    throw new RuntimeException('Missing or unreadable root .env file');
	}

	$env = parse_ini_file($envPath, false, INI_SCANNER_RAW);
	if ($env === false) {
	    throw new RuntimeException('Unable to parse root .env file');
	}

	$conn = mysqli_connect(
	    $env['DB_HOST'] ?? 'localhost',
	    $env['DB_USERNAME'] ?? '',
	    $env['DB_PASSWORD'] ?? '',
	    $env['DB_DATABASE'] ?? '',
	    (int) ($env['DB_PORT'] ?? 3306)
	);
	if (!$conn) {
	    throw new RuntimeException('Database connection failed');
	}
?>