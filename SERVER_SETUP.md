# Server Setup Guide

## Problem: Not seeing data on server but works on localhost

This happens when the server is not detecting correctly and using the wrong database.

## Quick Fix Steps

### Step 1: Check Current Status
Visit `server_debug.php` on your server to see what's detected:
```
http://169.239.251.102:341/attendance-system/server_debug.php
```

### Step 2: Fix .env File on Server

On your server, edit the `.env` file and make sure it has:

```env
# Localhost Database Configuration
DB_HOST=localhost
DB_PORT=3306
DB_NAME=attendancemanagement
DB_USER=root
DB_PASS=
APP_ENV=server

# Server Database Configuration
DB_HOST_SERVER=localhost
DB_PORT_SERVER=3306
DB_NAME_SERVER=webtech_2025A_joycelyn_allan
DB_USER_SERVER=joycelyn.allan
DB_PASS_SERVER=Jalla@123
```

**IMPORTANT:** Set `APP_ENV=server` (not `development`) to force server mode.

### Step 3: Use Automatic Fix Script

Or run the automatic fix script on your server:
```
http://169.239.251.102:341/attendance-system/fix_server_config.php
```

This will automatically update your `.env` file to set `APP_ENV=server`.

### Step 4: Verify Connection

After fixing, visit:
- `db_diagnostic.php` - Check database connection
- `server_debug.php` - Verify server detection

## Why This Happens

1. **APP_ENV is set to 'development'** - This makes the system think it's localhost
2. **Server detection fails** - HTTP_HOST or SERVER_NAME might not match expected values
3. **Missing server config** - DB_HOST_SERVER not set in .env file

## Solution

The most reliable fix is to set `APP_ENV=server` in your `.env` file on the server. This forces the system to use server database credentials.

## Files to Check

- `.env` - Must have `APP_ENV=server` and all `DB_*_SERVER` variables
- `server_debug.php` - Shows what's being detected
- `db_diagnostic.php` - Shows database connection status
- `fix_server_config.php` - Automatically fixes .env file

