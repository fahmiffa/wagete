<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageScheduler extends Model
{
    use HasFactory;

    protected $table = 'message_schedulers';

    protected $fillable = [
        'user_id',
        'template_pesan_id',
        'contact_id',
        'waktu',
        'status',
        'catatan',
    ];

    protected $casts = [
        'waktu' => 'datetime',
    ];

    /**
     * Relationship to the user who owns this schedule.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to the template pesan.
     */
    public function templatePesan(): BelongsTo
    {
        return $this->belongsTo(TemplatePesan::class, 'template_pesan_id');
    }

    /**
     * Relationship to the contact.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
}
