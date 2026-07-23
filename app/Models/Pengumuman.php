<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pengumuman';

    protected $fillable = [
        'judul',
        'kategori',
        'status',
        'prioritas',
        'target',
        'publish_at',
        'expire_at',
        'konten',
        'lampiran',
        'is_sticky',
        'is_popup',
        'kirim_notifikasi',
        'has_notified',
    ];

    protected function casts(): array
    {
        return [
            'publish_at' => 'datetime',
            'expire_at' => 'datetime',
            'lampiran' => 'array',
            'is_sticky' => 'boolean',
            'is_popup' => 'boolean',
            'kirim_notifikasi' => 'boolean',
            'has_notified' => 'boolean',
        ];
    }

    public function reads()
    {
        return $this->hasMany(PengumumanRead::class, 'pengumuman_id');
    }
}
