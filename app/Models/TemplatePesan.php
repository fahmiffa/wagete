<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplatePesan extends Model
{
    use HasFactory;

    protected $table = 'template_pesans';

    protected $fillable = [
        'user_id',
        'name',
        'pesan',
    ];

    /**
     * Relationship to the user that owns this template.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
