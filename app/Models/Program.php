<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Program extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name'];

    /**
     * Relationships
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
