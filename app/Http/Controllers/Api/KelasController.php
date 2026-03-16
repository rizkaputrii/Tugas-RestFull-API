<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Model\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class KelasController extends Controller
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
        $kelas = Kelas::all();

        $items = $kelas->map(function($k) {
            return [
                'href' => url('/api/kelas/' . $k->id),
                'data' => [
                    ['name' => 'id', 'value' => $k->id],
                    ['name' => 'kode_kelas', 'value' => $k->kode_kelas],
                    ['name' => 'nama_kelas', 'value' => $k->nama_kelas]
                ],
                'links' => []
            ];
        });

        return response()->json([
            'collection' => [
                'version' => '1.0',
                'href' => url('/api/kelas'),
                'items' => $items,
                'template' => [
                    'data' => [
                        ['name' => 'kode_kelas', 'value' => ''],
                        ['name' => 'nama_kelas', 'value' => '']
                    ]
                ]
            ]
        ], 200, ['Content-Type' => 'application/vnd.collection+json']);
    }

    // Menambah Data
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_kelas' => 'required|string|unique:kelas,kode_kelas',
            'nama_kelas' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->failedResponse($validator->errors(), 422);
        }
    
        $kelas = Kelas::create($request->all());
    
        if ($kelas) {
            return $this->success($kelas, 201, 'Kelas berhasil ditambahkan!');
        } else {
            return $this->failedResponse('Kelas gagal ditambahkan!', 500);
        }
    }

    // Menampilkan Data Tunggal (Berdasarkan ID)
    public function show(Kelas $kelas)
    {
        return $this->success($kelas, 200);
    }

    // Mengupdate Data
    public function update(Request $request, Kelas $kelas)
    {
        $validator = Validator::make($request->all(), [
            'kode_kelas' => 'required|string|unique:kelas,kode_kelas,' . $kelas->id,
            'nama_kelas' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->failedResponse($validator->errors(), 422);
        }

        $kelas->update($request->all());
        return $this->success($kelas, 200, 'Kelas berhasil diupdate!');
    }

    // Menghapus Data
    public function destroy(Kelas $kelas)
    {
        $deleted = $kelas->delete();
        if ($deleted) {
            return $this->success(null, 200, 'Kelas berhasil dihapus!');
        } else {
            return $this->failedResponse('Kelas gagal dihapus!', 500);
        }
    }
}