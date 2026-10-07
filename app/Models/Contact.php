<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'contacts';

    protected $fillable = [
        'app_id',
        'nama',
        'phone',
    ];

    /**
     * Relationship to the App that owns this contact
     */
    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }
}
