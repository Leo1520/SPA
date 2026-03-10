<?php
/**
 * ════════════════════════════════════════
 * CONFIGURACIÓN DE BASE DE DATOS
 * ════════════════════════════════════════
 * Conexión PDO a MySQL/MariaDB (Laragon)
 * Base de datos: spa_america
 */

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'spa_america');
define('DB_USER', 'root');
define('DB_PASS', '');

/**
 * Retorna una instancia PDO singleton para la conexión a la base de datos
 * @return PDO
 */
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . 
               ";dbname=" . DB_NAME . ";charset=utf8mb4";
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }
    return $pdo;
}
