<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    protected $fillable = [
        'user_id', 'query', 'limit_count', 'lang',
        'num_results', 'num_sources', 'search_time', 'ip_address',
    ];

    protected $casts = [
        'search_time' => 'decimal:4',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
