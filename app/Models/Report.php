<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Report extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'program_id',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'jumlah_penerima',
        'tanggal_penyaluran',
        'bukti_penyaluran',
        'catatan_tambahan',
        'status',
        'alasan_penolakan',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
