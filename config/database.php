<?php
// Database configuration
return [
    'host' => 'localhost',
    'username' => 'websocket_user',
    'password' => 'password',
    'database' => 'websocket_db',
    'port' => 3306,
    
    // Generate DSN string for React/MySQL
    'getDsn' => function() {
        $config = include __DIR__ . '/database.php';
        return "mysql://{$config['username']}:{$config['password']}@{$config['host']}:{$config['port']}/{$config['database']}";
    }
]; 