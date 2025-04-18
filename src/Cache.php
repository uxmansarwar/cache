<?php
/**
 * ----------------------------------------------------------------------
 * ----------------------------------------------------------------------
 *
 * @author   UxmanSarwar
 * @github   https://github.com/uxmansarwar
 * @linkedin https://www.linkedin.com/in/uxmansarwar
 * @email    uxmansrwr@gmail.com
 * @since    Uxman is full-stack(PHP, Laravel, Tailwind, JavaScript, VueJs, More...) developer since 2013
 * @version  1.0.1
 *
 * ----------------------------------------------------------------------
 * 
 * ----------------------------------------------------------------------
 */
namespace UxmanSarwar;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * 🔒 Laravel-Style Cache Wrapper for Core PHP
 * ============================================================================
 * 📦 Purpose:
 * This class mimics Laravel's Cache system using the Symfony Cache component.
 * It is designed to be modular, extensible, and Composer-package-ready,
 * offering Laravel-like cache functions such as get, put, remember, forget, etc.
 * 
 * 🛠 Features:
 * - Full support for TTL-based and forever cache storage.
 * - "Remember" pattern for lazy data fetching.
 * - In-memory cache object reuse (singleton pattern).
 * - Developer-friendly, structured, and clean API.
 * 
 * 📂 Customizable:
 * - Namespace prefixing for logical cache separation.
 * - Custom cache directory path support.
 * - Fully PSR-compliant underneath (via Symfony).
 * 
 * 🧑‍💻 Ideal For:
 * - Developers transitioning from Laravel to Core PHP.
 * - Projects requiring modular and Laravel-consistent caching.
 * - Building a foundation for a Composer package or framework.
 * 
 * 💡 Dependencies:
 * - symfony/cache (install via `composer require symfony/cache`)
 * 
 * @package App\Cache
 * @author  
 * @version 1.0.0
 */

final class Cache
{
    /**
     * @var FilesystemAdapter|null The singleton cache store instance.
     */
    protected static ?FilesystemAdapter $cache = null;

    /**
     * 🚀 Initialize the cache instance.
     *
     * @param string       $namespace        Namespace/prefix for cache keys (optional).
     * @param int          $defaultLifetime  Default lifetime in seconds (optional).
     * @param string|null  $directory        Custom path for cache storage (optional).
     * 
     * @return void
     */
    public static function init(string $namespace = '', int $defaultLifetime = 0, ?string $directory = null): void
    {
        if (!self::$cache) {
            $directory = $directory ?? __DIR__ . '/../../storage/cache';
            self::$cache = new FilesystemAdapter($namespace, $defaultLifetime, $directory);
        }
    }

    /**
     * 🔍 Get a cached value by key.
     *
     * @param string $key      The cache key.
     * @param mixed  $default  The fallback value if cache key doesn't exist.
     * 
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::init();
        $item = self::$cache->getItem($key);
        return $item->isHit() ? $item->get() : $default;
    }

    /**
     * 📝 Put a value into the cache.
     *
     * @param string $key      The cache key.
     * @param mixed  $value    The value to store.
     * @param int    $seconds  Time to live in seconds.
     * 
     * @return void
     */
    public static function put(string $key, mixed $value, int $seconds = 3600): void
    {
        self::init();
        $item = self::$cache->getItem($key);
        $item->set($value);
        $item->expiresAfter($seconds);
        self::$cache->save($item);
    }

    /**
     * 💡 Get a value or store it using a callback if not exists (with TTL).
     *
     * @param string   $key      The cache key.
     * @param int      $seconds  Time to live in seconds.
     * @param callable $callback Callback to fetch and cache the value.
     * 
     * @return mixed
     */
    public static function remember(string $key, int $seconds, callable $callback): mixed
    {
        self::init();
        return self::$cache->get($key, function (ItemInterface $item) use ($seconds, $callback) {
            $item->expiresAfter($seconds);
            return $callback();
        });
    }

    /**
     * 🔁 Get a value or store it permanently using a callback.
     *
     * @param string   $key      The cache key.
     * @param callable $callback Callback to fetch and cache the value.
     * 
     * @return mixed
     */
    public static function rememberForever(string $key, callable $callback): mixed
    {
        self::init();
        return self::$cache->get($key, function (ItemInterface $item) use ($callback) {
            return $callback();
        });
    }

    /**
     * ♾️ Store an item in cache permanently (no TTL).
     *
     * @param string $key   The cache key.
     * @param mixed  $value The value to store.
     * 
     * @return void
     */
    public static function forever(string $key, mixed $value): void
    {
        self::init();
        $item = self::$cache->getItem($key);
        $item->set($value);
        self::$cache->save($item);
    }

    /**
     * ❓ Check if a cache key exists.
     *
     * @param string $key The cache key.
     * 
     * @return bool
     */
    public static function has(string $key): bool
    {
        self::init();
        return self::$cache->hasItem($key);
    }

    /**
     * ❌ Remove a cache item by key.
     *
     * @param string $key The cache key.
     * 
     * @return bool True if deleted, false if not.
     */
    public static function forget(string $key): bool
    {
        self::init();
        return self::$cache->deleteItem($key);
    }

    /**
     * 🧹 Clear the entire cache store.
     *
     * @return bool True on success.
     */
    public static function flush(): bool
    {
        self::init();
        return self::$cache->clear();
    }

    /**
     * 🪪 Get and delete a cache item (pull).
     *
     * @param string $key      The cache key.
     * @param mixed  $default  Fallback value if not found.
     * 
     * @return mixed
     */
    public static function pull(string $key, mixed $default = null): mixed
    {
        self::init();
        $item = self::$cache->getItem($key);
        if ($item->isHit()) {
            $value = $item->get();
            self::$cache->deleteItem($key);
            return $value;
        }
        return $default;
    }

    /**
     * 🧪 Add item only if it does not already exist in cache.
     *
     * @param string $key      The cache key.
     * @param mixed  $value    The value to store.
     * @param int    $seconds  Time to live in seconds.
     * 
     * @return bool True if added, false if already exists.
     */
    public static function add(string $key, mixed $value, int $seconds = 3600): bool
    {
        self::init();
        if (!self::$cache->hasItem($key)) {
            self::put($key, $value, $seconds);
            return true;
        }
        return false;
    }

    /**
     * 🔼 Increment a numeric value in the cache.
     *
     * @param string $key     The cache key.
     * @param int    $amount  Amount to increment by.
     * 
     * @return int The updated value.
     */
    public static function increment(string $key, int $amount = 1): int
    {
        self::init();
        $value = (int) self::get($key, 0) + $amount;
        self::put($key, $value);
        return $value;
    }

    /**
     * 🔽 Decrement a numeric value in the cache.
     *
     * @param string $key     The cache key.
     * @param int    $amount  Amount to decrement by.
     * 
     * @return int The updated value.
     */
    public static function decrement(string $key, int $amount = 1): int
    {
        self::init();
        $value = (int) self::get($key, 0) - $amount;
        self::put($key, $value);
        return $value;
    }
}
