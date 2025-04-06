<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'value'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'boolean', // Automatically cast `value` to boolean
    ];

    /**
     * Helper to get a setting by name.
     *
     * @param string $name
     * @return mixed
     */
    public static function get(string $name)
    {
        return self::where('name', $name)->value('value');
    }

    /**
     * Helper to toggle a setting value by name.
     *
     * @param string $name
     * @return void
     */
    public static function toggle(string $name): void
    {
        $setting = self::where('name', $name)->first();

        if ($setting) {
            $setting->update(['value' => !$setting->value]);
        }
    }
}
