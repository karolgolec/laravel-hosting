## Package Laravel Hosting

Laravel package to install Laravel on your hosting. If you have SSH access,
you can use queues using SCREEN. The worker maintenance feature is built into the package.

## Installation

- Use following command to install:

```
composer require karolgolec/laravel-hosting
```

## Setup

On the local application, run the `php artisan laravel-hosting:install` command.

Run the `dep deploy` command to upload the application to the hosting.

On the hosting server in the `.env` file, set the value of `LARAVEL_HOSTING_QUEUE_ENABLED` to `true`.

Add to the task of running the Laravel scheduler to CRON `php artisan schedule:run`.

## Works with hosting providers

### SeoHost.pl

Link partnerski: [SeoHost.pl](https://seohost.pl/?ref=49482)

CRON command. Complete the command parameters.

```
/opt/alt/php82/usr/bin/php -c ~/php-cli-laravel-seohost.ini /home/{{HOST_USER}}/domains/{{DOMAIN}}/public_html/current/artisan schedule:run >> /dev/null 2>&1
```
