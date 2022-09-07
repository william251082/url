<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id', 'long_name', 'short_name'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [];

    protected $table = 'url';

    protected function longName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => htmlspecialchars($value),
            set: fn ($value) => htmlspecialchars($value),
        );
    }
}
