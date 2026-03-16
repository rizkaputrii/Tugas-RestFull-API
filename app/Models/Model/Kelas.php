<?php

namespace App\Models\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;
    protected $table = 'kelas';
    protected $fillable = [ 'kode_kelas', 'nama_kelas' ];

    public function siswas() {
        return $this->hasMany('App\Models\Model\Siswa','kelas_id');
    }
    public function jadwals() {
        return $this->hasMany('App\Models\Model\Jadwal', 'kelas_id');
    }
}
