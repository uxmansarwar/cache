# 🧰 Laravel-Like Cache Wrapper for Core PHP

A **fully-featured Laravel-style Cache Wrapper** built in **Core PHP** using the power of Symfony's Cache component. This package provides an elegant and fluent caching API that mirrors Laravel's `Cache` facade, making it easy for developers to manage caching in procedural or object-oriented PHP applications outside of Laravel.

---

## 📌 Why This Package?

Laravel provides a very expressive and flexible API for caching, but it's deeply coupled with the Laravel framework. This package allows you to bring that **elegant Laravel cache experience** into any Core PHP project without requiring Laravel at all.

**Built for:**
- Developers transitioning from Laravel to raw PHP
- Lightweight or micro PHP apps that need efficient caching
- Custom frameworks and standalone apps

---

## 🧠 Why a Wrapper?

Wrapping an existing library like Symfony's Cache component helps abstract complexity and create a **clean, readable, Laravel-style API** without reinventing the wheel. Symfony's cache is PSR-6/16 compliant and battle-tested in production, making it an excellent backend for this wrapper.

---

## ⚙️ Requirements

- PHP 8.0 or later
- Composer
- Symfony Cache Component

Install Symfony Cache:
```bash
composer require symfony/cache
```

---

## 📦 Installation

### 1. Clone or Require
If you're using Composer for your project:

```bash
composer require uxmansarwar/cache
```

If you're manually integrating:

```bash
git clone https://github.com/uxmansarwar/cache.git
```

Place `src/Cache.php` in your preferred directory and include it with Composer autoload.

---

## 🧪 Initialization

Before using the cache, initialize it:

```php
use UxmanSarwar\Cache;

Cache::init(
    namespace: 'my_app', 
    defaultLifetime: 3600, 
    directory: __DIR__ . '/storage/cache'
);
```

---

## 🛠 Full API Usage Examples

### 🔍 Get Value from Cache
```php
$value = Cache::get('user_1');
```

### 📝 Put Value into Cache
```php
Cache::put('user_1', ['name' => 'John'], 600); // 10 mins
```

### ♾️ Store Permanently
```php
Cache::forever('site_name', 'My Awesome App');
```

### ❓ Check if Key Exists
```php
if (Cache::has('user_1')) {
    echo "User found!";
}
```

### ❌ Forget a Cache Key
```php
Cache::forget('user_1');
```

### 🧹 Flush Entire Cache
```php
Cache::flush();
```

### 🦁 Remember (Lazy Load + Cache)
```php
$data = Cache::remember('config_data', 3600, function () {
    return expensiveFetch();
});
```

### 🦁 Remember Forever
```php
$data = Cache::rememberForever('constants', function () {
    return loadConstants();
});
```

### 🧪 Pull and Delete
```php
$data = Cache::pull('temp_code'); // returns and deletes it
```

### 🪮 Add if Not Exists
```php
$wasAdded = Cache::add('otp_123', 456789, 120);
```

### 🕾️ Increment a Value
```php
$newValue = Cache::increment('views', 1);
```

### 🔾 Decrement a Value
```php
$newValue = Cache::decrement('downloads', 2);
```

---

## 🧱 Tests & Development Setup

### Install Dev Dependencies

```bash
composer require --dev pestphp/pest phpstan/phpstan
```

### PestPHP Setup

```bash
./vendor/bin/pest --init
```
This will create the `tests/` directory and `Pest.php` bootstrap file.

### PHPStan Setup

Create `phpstan.neon`:
```neon
includes:
    - vendor/phpstan/phpstan/conf/bleedingEdge.neon

parameters:
    level: 8
    paths:
        - src
        - tests
```

### Add Test Scripts to `composer.json`
```json
"scripts": {
    "test": "pest",
    "stan": "phpstan analyse"
}
```

---

## 📚 Example Pest Tests

File: `tests/CacheTest.php`
```php
<?php

declare(strict_types=1);

use UxmanSarwar\Cache;

beforeAll(function () {
    Cache::init(
        namespace: 'testing',
        defaultLifetime: 3600,
        directory: __DIR__ . '/../storage/cache'
    );
});

test('put and get works', function () {
    Cache::put('test_key', 'Hello', 10);
    expect(Cache::get('test_key'))->toBe('Hello');
});

test('forget removes a key', function () {
    Cache::put('temp_key', 'Temp', 10);
    Cache::forget('temp_key');
    expect(Cache::has('temp_key'))->toBeFalse();
});

test('remember lazily loads and stores value', function () {
    $result = Cache::remember('lazy_key', 60, fn () => 'LazyValue');
    expect($result)->toBe('LazyValue');
});

test('pull fetches and deletes value', function () {
    Cache::put('pull_key', 'Once', 60);
    $value = Cache::pull('pull_key');
    expect($value)->toBe('Once');
    expect(Cache::has('pull_key'))->toBeFalse();
});
```

---

## 🧱 Directory Structure

```
cache/
├── src/
│   └── Cache.php
├── tests/
│   └── CacheTest.php
├── storage/
│   └── cache/ (default)
├── composer.json
├── phpstan.neon
└── README.md
```

---

## 🎯 Planned Features
- File-based tagging (like Laravel's tagged cache)
- Redis/Memcached drivers
- PSR-16 compatibility layer
- Cache events and logging

---

## 🧑‍💻 Contribution
Pull requests and issues are welcome! Feel free to fork and improve. Please make sure to write tests and maintain existing code style.

```bash
git clone https://github.com/uxmansarwar/cache.git
cd cache
composer install
```

---

## 📜 License
This project is open-sourced under the [MIT License](LICENSE).

---

## ❤️ Credits
This wrapper is built using the power of [Symfony Cache](https://symfony.com/doc/current/components/cache.html) and inspired by Laravel's elegant `Cache` facade.

---

## 🙌 Support
If this package helped you, consider ⭐ starring the repo and sharing it with your fellow PHP developers!

