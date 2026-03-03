<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        return static::query()
            ->where('key', $key)
            ->value('value') ?? $default;
    }

    public static function setValue(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );
    }

    public static function defaultContactNumber(): ?string
    {
        return static::getValue('default_contact_number');
    }

    public static function defaultContactWhatsAppUrl(?string $message = null): ?string
    {
        $number = preg_replace('/\D+/', '', (string) static::defaultContactNumber());

        if (blank($number)) {
            return null;
        }

        $url = "https://wa.me/{$number}";

        if (filled($message)) {
            $url .= '?text='.urlencode($message);
        }

        return $url;
    }
}
