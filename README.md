# Simple PHP Authentication

## Run with XAMPP

1. Start Apache and MySQL from the XAMPP control panel.
2. Open phpMyAdmin and import `schema.sql`. This creates the `auth_system` database and its `users` table.
3. Keep the project inside XAMPP's `htdocs` directory, then open `http://localhost/vicwiin-fc/register.php`.
4. The default database connection in `config.php` uses `localhost`, database `auth_system`, user `root`, and an empty password. If your MySQL credentials differ, set `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASS` in the PHP environment before starting Apache.

Registration saves a password hash (not the original password). After registering, log in to reach the protected dashboard; use the log-out button to end the session.
