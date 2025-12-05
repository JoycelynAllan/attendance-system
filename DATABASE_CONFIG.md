# Database Configuration Guide

## For Remote Server Setup

Your application is hosted at: **http://169.239.251.102:341/~joycelyn.allan/attendance-v2/**

To fix database connection errors, you need to configure your database settings.

### Option 1: Create .env file (Recommended)

Create a file named `.env` in the root directory with the following content:

```
DB_HOST=localhost
DB_PORT=3306
DB_NAME=attendancemanagement
DB_USER=your_database_username
DB_PASS=your_database_password
APP_ENV=production
```

**Important Notes:**
- If your MySQL database is on the same server (169.239.251.102), use `DB_HOST=localhost`
- If your MySQL database is on a different server, use that server's IP address
- Update `DB_USER` and `DB_PASS` with your actual database credentials
- Make sure the database name matches your actual database name

### Option 2: Direct Configuration

If you cannot create a .env file, you can directly edit `db_connect.php` and update these lines:

```php
$host = 'localhost';  // or your database server IP
$port = '3306';       // your MySQL port
$dbname = 'attendancemanagement';
$username = 'your_username';
$password = 'your_password';
```

### Common Issues and Solutions

1. **"Access denied" error**: 
   - Check your database username and password
   - Make sure the database user has proper permissions

2. **"Can't connect to MySQL server"**:
   - Verify MySQL is running on your server
   - Check if MySQL is listening on the correct port
   - If database is remote, ensure firewall allows connections

3. **"Unknown database"**:
   - Make sure the database exists
   - Check the database name spelling

### Testing Connection

After configuration, test your connection by visiting:
`http://169.239.251.102:341/~joycelyn.allan/attendance-v2/test_connection.php`

This will show you if the database connection is working correctly.

## Server Information

- **Full URL:** http://169.239.251.102:341/~joycelyn.allan/attendance-v2/
- **User Directory:** ~joycelyn.allan/
- **Project Folder:** attendance-v2
- **Port:** 341

All AJAX calls use relative paths, so they should work correctly with this setup.

