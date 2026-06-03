<?php
/** PDO connection (MySQL on Hostinger; SQLite supported for local testing). */

function cfg($section = null)
{
    static $config = null;
    if ($config === null) {
        $config = require __DIR__ . '/../config.php';
    }
    if ($section === null) return $config;
    return $config[$section] ?? null;
}

function db()
{
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $db = cfg('db');
    $driver = $db['driver'] ?? 'mysql';
    // Local-testing override (does not affect production config): set MOC_SQLITE_PATH
    $envSqlite = getenv('MOC_SQLITE_PATH');
    if ($envSqlite) { $driver = 'sqlite'; $db['sqlite_path'] = $envSqlite; }
    $opts = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    if ($driver === 'sqlite') {
        $path = $db['sqlite_path'];
        if (!is_dir(dirname($path))) @mkdir(dirname($path), 0775, true);
        $pdo = new PDO('sqlite:' . $path, null, null, $opts);
        $pdo->exec('PRAGMA foreign_keys = ON;');
    } else {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $db['host'], $db['name'], $db['charset'] ?? 'utf8mb4');
        $pdo = new PDO($dsn, $db['user'], $db['pass'], $opts);
    }
    return $pdo;
}

function db_driver()
{
    if (getenv('MOC_SQLITE_PATH')) return 'sqlite';
    return cfg('db')['driver'] ?? 'mysql';
}

/** Small query helpers */
function q($sql, $params = [])
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st;
}
function q_all($sql, $params = []) { return q($sql, $params)->fetchAll(); }
function q_one($sql, $params = []) { $r = q($sql, $params)->fetch(); return $r === false ? null : $r; }
function q_val($sql, $params = []) { $r = q($sql, $params)->fetch(PDO::FETCH_NUM); return $r ? $r[0] : null; }
function last_id() { return (int) db()->lastInsertId(); }
