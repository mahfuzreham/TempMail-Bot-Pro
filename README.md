# TempMail Bot Pro

Production-oriented Telegram temporary email platform built with PHP 8.2+, MySQL/MariaDB, cPanel UAPI and IMAP.

## Features

- Telegram bot mailbox lifecycle
- cPanel mailbox creation/deletion
- IMAP inbox synchronization
- Multi-domain support
- Mailbox expiration and cleanup
- REST API for mailbox operations
- Admin authentication/dashboard
- PDO prepared statements
- Environment-based secrets
- GitHub Actions PHP validation

## Requirements

- PHP 8.2+
- MySQL 8 / MariaDB 10.6+
- PHP extensions: `pdo`, `pdo_mysql`, `json`, `imap`
- Composer 2
- A cPanel account with UAPI access
- IMAP-enabled mail server
- HTTPS for Telegram webhook deployments

## Installation

1. Clone the repository.
2. Run `composer install`.
3. Copy `.env.example` to `.env` and fill in the database, Telegram, cPanel and IMAP settings.
4. Create the database and import `install/database.sql`.
5. Configure the web server document root according to your deployment layout.
6. Configure the Telegram webhook using the bot's webhook endpoint and the optional webhook secret.
7. Configure cron jobs for mailbox cleanup and IMAP synchronization.

## Cron example

```bash
* * * * * /usr/bin/php /path/to/TempMail-Bot-Pro/cron/email_fetch.php
*/5 * * * * /usr/bin/php /path/to/TempMail-Bot-Pro/cron/mailbox_cleanup.php
0 * * * * /usr/bin/php /path/to/TempMail-Bot-Pro/cron/expire_mailbox.php
```

## Security

Never commit `.env`, Telegram bot tokens, cPanel API tokens, mailbox passwords, or production database credentials. The repository ignores `.env` and runtime storage by default.

For production, use HTTPS, least-privilege cPanel credentials, a dedicated database user, strong admin passwords, webhook secret validation, rate limiting, and regular database backups.

## Development

Run:

```bash
composer validate --no-check-publish
composer install
```

CI performs Composer validation and PHP syntax checks on pushes and pull requests targeting `main`.

## License

MIT License. See `LICENSE`.
