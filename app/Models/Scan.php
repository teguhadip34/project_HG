<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Scan extends Model
{
    protected $fillable = [
        'user_id',
        'waste_type_id',
        'detected_label',
        'detection_summary',
        'image_path',
        'image_data',
        'annotated_image_data',
        'confidence',
        'xp_earned',
        'image_hash',
    ];

    protected function casts(): array
    {
        return [
            'detection_summary' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wasteType(): BelongsTo
    {
        return $this->belongsTo(WasteType::class);
    }
}

