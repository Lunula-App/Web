# Lunula — Website

Marketing and support website for [Lunula](https://github.com/Lunula-App/Lunula), the privacy-first cycle tracking app.

Live at **[lunula.me](https://lunula.me)**

## Pages

- **/** — Landing page with features, phases, and privacy overview
- **/support.html** — FAQ accordion and contact form
- **/privacy.html** — Full privacy policy

## Stack

Plain HTML, CSS, and JavaScript — no build step. The contact form is handled by a small PHP backend with [hCaptcha](https://hcaptcha.com) protection.

```
lunula-web/
├── index.html
├── support.html
├── privacy.html
├── form-handler.php      # Contact form backend
├── config.example.php    # Secrets template (copy to config.php)
├── css/
│   └── style.css
├── js/
│   └── main.js
└── assets/
```

## Setup

1. Copy `config.example.php` to `config.php` and fill in your values:

```php
define('HCAPTCHA_SECRET', 'your_hcaptcha_secret');
define('SUPPORT_EMAIL',   'your@email.com');
```

2. Deploy the files to any PHP-capable web host. `config.php` is gitignored and must be created on the server manually.

## License

See [LICENSE](LICENSE).
