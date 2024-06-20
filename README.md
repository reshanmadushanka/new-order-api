# Order Management System

This project is a Laravel 11-based Order Management System.

## Features

- Create new orders via a REST API.
- Queue system to handle high API request load.
- Client-side storage using IndexedDB.

## Requirements

- PHP >= 8.1
- Composer
- Laravel >= 11.0
- MySQL
- Guzzle HTTP Client

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/reshanmadushanka/new-order-api.git
cd new-order-api
```
### Install dependencies
```bash
composer install
php artisan key:generate
php artisan migrate
```
### Set up the environment file
```bash
cp .env.example .env
```

### Generate application key
```bash
php artisan key:generate
```
### Set up Sanctum
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate

```

### Set up Horizon
```bash
php artisan vendor:publish --provider="Laravel\Horizon\HorizonServiceProvider"
php artisan migrate

QUEUE_CONNECTION=redis
php artisan horizon
php artisan queue:work
```

## API Endpoints
```bash
php artisan l5-swagger:generate
```
Use the following link to access the Swagger UI

- https://your-domain.com/api/documentation
