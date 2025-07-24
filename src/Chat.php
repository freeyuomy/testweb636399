<?php
namespace MyApp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use React\MySQL\Factory;

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $mysql;
    
    public function __construct() {
        $this->clients = new \SplObjectStorage;
        
        // Create MySQL connection using config
        $config = include dirname(__DIR__) . '/config/database.php';
        $dsn = $config['getDsn']();
        
        $factory = new Factory();
        $this->mysql = $factory->createLazyConnection($dsn);
        
        echo "WebSocket server started!\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection
        $this->clients->attach($conn);
        
        $clientId = $conn->resourceId;
        echo "New connection! ({$clientId})\n";
        
        // Log connection to database
        $this->mysql->query(
            'INSERT INTO connections (client_id, connected_at) VALUES (?, NOW())',
            [$clientId]
        );
        
        // Notify all clients about new connection
        foreach ($this->clients as $client) {
            if ($client !== $conn) {
                $client->send(json_encode([
                    'type' => 'user_connected',
                    'client_id' => $clientId
                ]));
            }
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $clientId = $from->resourceId;
        $data = json_decode($msg, true);
        
        echo "Client {$clientId} sent message: {$msg}\n";
        
        // Store message in database
        if (isset($data['message'])) {
            $this->mysql->query(
                'INSERT INTO messages (client_id, message, sent_at) VALUES (?, ?, NOW())',
                [$clientId, $data['message']]
            );
        }
        
        // Broadcast message to all clients
        foreach ($this->clients as $client) {
            $client->send(json_encode([
                'type' => 'message',
                'client_id' => $clientId,
                'message' => $data['message'] ?? '',
                'timestamp' => date('Y-m-d H:i:s')
            ]));
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // Detach client from storage
        $this->clients->detach($conn);
        
        $clientId = $conn->resourceId;
        echo "Connection {$clientId} has disconnected\n";
        
        // Log disconnection to database
        $this->mysql->query(
            'UPDATE connections SET disconnected_at = NOW() WHERE client_id = ? AND disconnected_at IS NULL',
            [$clientId]
        );
        
        // Notify remaining clients
        foreach ($this->clients as $client) {
            $client->send(json_encode([
                'type' => 'user_disconnected',
                'client_id' => $clientId
            ]));
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        
        $conn->close();
    }
} 