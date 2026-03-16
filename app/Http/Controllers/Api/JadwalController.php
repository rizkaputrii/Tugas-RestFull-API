<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Model\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class JadwalController extends Controller
{
    // Fungsi Custom Response
    private function success($data, $statusCode, $message='success')
    {
        return response()->json([
            'status' => true, 
            'message' => $message, 
            'data' => $data, 
            'status_code' => $statusCode
        ], $statusCode);
    }
    
    private function failedResponse($message, $statusCode)
    {
        return response()->json([
            'status' => false, 
            'message' => $message, 
            'data' => null, 
            'status_code' => $statusCode
        ], $statusCode);
    }

    // Display All Data
    public function index(): JsonResponse
    {
        $jadwals = Jadwal::all();

        $items = $jadwals->map(function($j) {
            return [
                'href' => url('/api/jadwal/' . $j->id),
                'data' => [
                    ['name' => 'id', 'value' => $j->id],
                    ['name' => 'kelas_id', 'value' => $j->kelas_id],
                    ['name' => 'mapel_id', 'value' => $j->mapel_id],
                    ['name' => 'guru_id', 'value' => $j->guru_id],
                    ['name' => 'hari', 'value' => $j->hari],
                    ['name' => 'jam_pelajaran', 'value' => $j->jam_pelajaran]
                ],
                'links' => [
                    ['rel' => 'kelas', 'href' => url('/api/kelas/' . $j->kelas_id)],
                    ['rel' => 'mapel', 'href' => url('/api/mapel/' . $j->mapel_id)],
                    ['rel' => 'guru', 'href' => url('/api/guru/' . $j->guru_id)]
                ]
            ];
        });

        return response()->json([
            'collection' => [
                'version' => '1.0',
                'href' => url('/api/jadwal'),
                'items' => $items,
                'template' => [
                    'data' => [
                        ['name' => 'kelas_id', 'value' => ''],
                        ['name' => 'mapel_id', 'value' => ''],
                        ['name' => 'guru_id', 'value' => ''],
                        ['name' => 'hari', 'value' => ''],
                        ['name' => 'jam_pelajaran', 'value' => '']
                    ]
                ]
            ]
        ], 200, ['Content-Type' => 'application/vnd.collection+json']);
    }

    // Menambah Data
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kelas_id' => 'required|integer',
            'mapel_id' => 'required|integer',
            'guru_id' => 'required|integer',
            'hari' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu',
            'jam_pelajaran' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->failedResponse($validator->errors(), 422);
        }
    
        $jadwal = Jadwal::create($request->all());
    
        if ($jadwal) {
            return $this->success($jadwal, 201, 'Jadwal berhasil ditambahkan!');
        } else {
            return $this->failedResponse('Jadwal gagal ditambahkan!', 500);
        }
    }

    // Menampilkan Data Tunggal (Berdasarkan ID)
    public function show(Jadwal $jadwal)
    {
        return $this->success($jadwal, 200);
    }

    // Mengupdate Data
    public function update(Request $request, Jadwal $jadwal)
    {
        $validator = Validator::make($request->all(), [
            'kelas_id' => 'required|integer',
            'mapel_id' => 'required|integer',
            'guru_id' => 'required|integer',
            'hari' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu',
            'jam_pelajaran' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->failedResponse($validator->errors(), 422);
        }

        $jadwal->update($request->all());
        return $this->success($jadwal, 200, 'Jadwal berhasil diupdate!');
    }

    // Menghapus Data
    public function destroy(Jadwal $jadwal)
    {
        $deleted = $jadwal->delete();
        if ($deleted) {
            return $this->success(null, 200, 'Jadwal berhasil dihapus!');
        } else {
            return $this->failedResponse('Jadwal gagal dihapus!', 500);
        }
    }
}