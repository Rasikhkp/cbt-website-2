<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class QuestionImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'filename',
        'original_name',
        'path',
        'mime_type',
        'size',
        'order',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function getUrl()
    {
        return Storage::url($this->path);
    }

    public function getFormattedSize()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
