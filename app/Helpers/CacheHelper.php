<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    protected static string $keysKey = 'webseite:cache_keys';

    /**
     * Speichert einen Wert im Cache und trackt den Key.
     */
    public static function remember(string $key, \DateTimeInterface|\DateInterval|int $ttl, \Closure $callback)
    {
        $value = Cache::remember($key, $ttl, $callback);
        self::trackKey($key);
        return $value;
    }

    /**
     * Trackt einen Cache-Key für spätere Invalidation.
     */
    protected static function trackKey(string $key)
    {
        $keys = Cache::get(self::$keysKey, []);
        if (!in_array($key, $keys)) {
            $keys[] = $key;
            // Cache die Keys selbst für eine lange Zeit (24 Stunden)
            Cache::put(self::$keysKey, $keys, now()->addHours(24));
        }
    }

    /**
     * Leert alle getrackten Cache-Keys.
     */
    public static function flushAll()
    {
        $keys = Cache::get(self::$keysKey, []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        Cache::forget(self::$keysKey);
    }

    /**
     * Leert nur die Guide-Cache-Keys.
     */
    public static function flushGuides()
    {
        self::flushByPrefix('webseite:guides');
    }

    /**
     * Leert nur die Search-Cache-Keys.
     */
    public static function flushSearch()
    {
        self::flushByPrefix('webseite:search');
    }

    /**
     * Leert alle Keys mit einem bestimmten Prefix.
     */
    protected static function flushByPrefix(string $prefix)
    {
        $keys = Cache::get(self::$keysKey, []);
        foreach ($keys as $key) {
            if (str_starts_with($key, $prefix)) {
                Cache::forget($key);
            }
        }
        // Aktualisiere die Key-Liste
        $remainingKeys = array_values(array_filter($keys, function ($key) use ($prefix) {
            return !str_starts_with($key, $prefix);
        }));
        if (count($remainingKeys) < count($keys)) {
            Cache::put(self::$keysKey, $remainingKeys, now()->addHours(24));
        }
    }
}
