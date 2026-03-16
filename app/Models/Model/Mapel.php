<?php

namespace App\Models\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    use HasFactory;
    protected $table = 'mapel';
    protected $fillable = [ 'kode_mapel', 'nama_mapel' ];

    public function jadwals() {
        return $this->hasMany('App\Models\Model\Jadwal', 'mapel_id');
    }
}
