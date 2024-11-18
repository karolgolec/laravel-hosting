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

## Works with hosting providers
- [SeoHost.pl](https://seohost.pl/?ref=49482)
