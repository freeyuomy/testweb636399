# PHP WebSocket Server with MySQL

A simple WebSocket server implementation using PHP, Ratchet, and MySQL for real-time communication.

## Requirements

- PHP 7.4 or higher with required extensions (mbstring, sockets)
- Composer
- MySQL/MariaDB
- Nginx with aaPanel
- Node.js (optional, for testing)

## Installation

1. **Clone the repository**

```bash
git clone https://github.com/yourusername/websocket-demo.git
cd websocket-demo
```

2. **Install dependencies**

```bash
composer install
```

3. **Set up the database**

Run the SQL script to create the database and tables:

```bash
mysql -u root -p < database/setup.sql
```

4. **Configure the database connection**

Edit `config/database.php` and update the database credentials:

```php
'host' => 'localhost',
'username' => 'websocket_user',
'password' => 'your_secure_password',
'database' => 'websocket_db',
```

5. **Configure Nginx**

Copy the Nginx configuration file to your Nginx configuration directory:

```bash
# For aaPanel
cp nginx/websocket.conf /www/server/panel/vhost/nginx/

# Update the configuration with your actual paths
nano /www/server/panel/vhost/nginx/websocket.conf
```

Update the following in the configuration:
- `server_name` with your domain
- `root` with the path to your project's public directory
- `fastcgi_pass` with your PHP-FPM socket path (may vary depending on your PHP version)

6. **Restart Nginx**

```bash
# For aaPanel
/etc/init.d/nginx restart
```

## Running the WebSocket Server

Start the WebSocket server:

```bash
php bin/server.php
```

For production use, consider using a process manager like Supervisor to keep the WebSocket server running:

```bash
# Install Supervisor
apt-get install supervisor

# Create a configuration file
nano /etc/supervisor/conf.d/websocket.conf
```

Add the following configuration:

```
[program:websocket]
command=php /path/to/your/project/bin/server.php
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/path/to/your/project/logs/websocket.log
```

Then start the Supervisor service:

```bash
supervisorctl reread
supervisorctl update
supervisorctl start websocket
```

## Testing

1. Open your browser and navigate to `http://your-domain.com`
2. Open the browser console to see WebSocket connection status
3. Send messages using the form and see them broadcast to all connected clients

## License

MIT 