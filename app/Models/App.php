<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class App extends Model
{
    use HasFactory;

    protected $table = 'apps';

    protected $fillable = [
        'user_id',
        'name',
        'key',
        'alamat',
        'nomor_hp',
    ];

    /**
     * Relationship to the user who owns this app
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Contacts belonging to this app
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }
}
