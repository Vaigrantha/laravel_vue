<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AppSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('app_settings.kv'));
        static::deleted(fn () => Cache::forget('app_settings.kv'));
    }

    /**
     * @return array<string, string>
     */
    public static function keyValue(): array
    {
        try {
            $hasTable = Schema::hasTable('app_settings');
        } catch (Throwable) {
            return [];
        }

        if (! $hasTable) {
            return [];
        }

        return Cache::remember('app_settings.kv', now()->addMinutes(10), function (): array {
            return static::query()->pluck('value', 'key')->toArray();
        });
    }

    public static function getValue(string $key, ?string $default = null): ?string
    {
        $settings = static::keyValue();

        return $settings[$key] ?? $default;
    }
}
