<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Model\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class MapelController extends Controller
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
        $mapels = Mapel::all();

        $items = $mapels->map(function($mapel) {
            return [
                'href' => url('/api/mapel/' . $mapel->id),
                'data' => [
                    ['name' => 'id', 'value' => $mapel->id],
                    ['name' => 'kode_mapel', 'value' => $mapel->kode_mapel],
                    ['name' => 'nama_mapel', 'value' => $mapel->nama_mapel]
                ],
                'links' => [] 
            ];
        });

        return response()->json([
            'collection' => [
                'version' => '1.0',
                'href' => url('/api/mapel'),
                'items' => $items,
                'template' => [
                    'data' => [
                        ['name' => 'kode_mapel', 'value' => ''],
                        ['name' => 'nama_mapel', 'value' => '']
                    ]
                ]
            ]
        ], 200, ['Content-Type' => 'application/vnd.collection+json']);
    }
    
    // Menambah Data
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_mapel' => 'required|string|unique:mapel,kode_mapel',
            'nama_mapel' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->failedResponse($validator->errors(), 422);
        }
    
        $mapel = Mapel::create($request->all());
    
        if ($mapel) {
            return $this->success($mapel, 201, 'Mata Pelajaran berhasil ditambahkan!');
        } else {
            return $this->failedResponse('Mata Pelajaran gagal ditambahkan!', 500);
        }
    }

    // Menampilkan Data Tunggal (Berdasarkan ID)
    public function show(Mapel $mapel)
    {
        return $this->success($mapel, 200);
    }

    // Mengupdate Data
    public function update(Request $request, Mapel $mapel)
    {
        $validator = Validator::make($request->all(), [
            'kode_mapel' => 'required|string|unique:mapel,kode_mapel,' . $mapel->id,
            'nama_mapel' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->failedResponse($validator->errors(), 422);
        }

        $mapel->update($request->all());
        return $this->success($mapel, 200, 'Mapel berhasil diupdate!');
    }

    // Menghapus Data
    public function destroy(Mapel $mapel)
    {
        $deleted = $mapel->delete();
        if ($deleted) {
            return $this->success(null, 200, 'Mapel berhasil dihapus!');
        } else {
            return $this->failedResponse('Mapel gagal dihapus!', 500);
        }
    }
}