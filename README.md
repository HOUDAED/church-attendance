# Church Attendance System

A PHP-based system for managing church attendance and members.

## Prerequisites

- XAMPP (with PHP 7.4+ and MySQL 5.7+)
- Web browser
- Text editor (VS Code recommended)

## Installation

### 1. XAMPP Setup

1. Start XAMPP Control Panel
2. Start required services:
   ```bash
   # Start Apache
   Click [Start] next to Apache
   
   # Start MySQL
   Click [Start] next to MySQL
   ```

### 2. Project Setup

1. Clone or download project to XAMPP's htdocs:
   ```bash
   cd c:\xampp\htdocs
   git clone [your-repo-url]
   # OR manually create directory
   mkdir church-attendance
   ```

2. Create required folders:
   ```bash
   cd church-attendance
   mkdir configuration
   mkdir css
   mkdir pages
   ```

### 3. Database Setup

1. Open phpMyAdmin:
   ```
   http://localhost/phpmyadmin
   ```

2. Create database:
   - Click "New"
   - Database name: `church_attendance`
   - Collation: `utf8mb4_unicode_ci`
   - Click "Create"

3. Import database structure:
   - Select `church_attendance`
   - Click "Import"
   - Choose `churchdb.sql`
   - Click "Go"

### 4. Configuration

1. Create database configuration file:
   ```bash
   copy configuration\database.example.php configuration\database.php
   ```

2. Edit database settings in `configuration/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'church_attendance');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

### 5. Create Admin User

1. Navigate to create user page:
   ```
   http://localhost/church-attendance/create_user.php
   ```

2. Default admin credentials:
   - Username: `admin`
   - Password: `admin123`

3. **IMPORTANT**: Delete create_user.php after setup:
   ```bash
   del c:\xampp\htdocs\church-attendance\create_user.php
   ```

## Usage

1. Access the system:
   ```
   http://localhost/church-attendance/login.php
   ```

2. Login with admin credentials
3. Navigate dashboard to manage:
   - Members
   - Attendance
   - Reports
   - Settings

## Project Structure

```
church-attendance/
├── configuration/
│   ├── config.php
│   └── database.php
├── css/
│   └── style.css
├── pages/
│   ├── dashboard.php
│   └── [other pages]
├── churchdb.sql
├── login.php
└── README.md
```

## Troubleshooting

### Database Connection Issues
- Verify MySQL is running in XAMPP
- Check database name matches `church_attendance`
- Ensure database user credentials are correct

### Login Problems
- Confirm user exists in `users` table
- Verify password is correctly hashed
- Check session configuration

### Path Issues
- Ensure XAMPP is installed in default location
- Verify file permissions
- Check file paths in includes

## Security Notes

- Change default admin password immediately
- Delete `create_user.php` after initial setup
- Keep configuration files secure
- Regular database backups recommended
- Update XAMPP components regularly

## Contributing

1. Fork the repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Open pull request

## License

[Your License Here]

## Contact

[Your Contact Information]