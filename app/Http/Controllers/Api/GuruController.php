<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Model\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class GuruController extends Controller
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
        $gurus = Guru::all();

        $items = $gurus->map(function($g) {
            return [
                'href' => url('/api/guru/' . $g->id),
                'data' => [
                    ['name' => 'id', 'value' => $g->id],
                    ['name' => 'user_id', 'value' => $g->user_id],
                    ['name' => 'nip', 'value' => $g->nip],
                    ['name' => 'nama', 'value' => $g->nama],
                    ['name' => 'tempat_lahir', 'value' => $g->tempat_lahir],
                    ['name' => 'tgl_lahir', 'value' => $g->tgl_lahir],
                    ['name' => 'gender', 'value' => $g->gender],
                    ['name' => 'phone_number', 'value' => $g->phone_number],
                    ['name' => 'email', 'value' => $g->email],
                    ['name' => 'alamat', 'value' => $g->alamat],
                    ['name' => 'pendidikan', 'value' => $g->pendidikan]
                ],
                'links' => [
                    ['rel' => 'user_account', 'href' => url('/api/users/' . $g->user_id)]
                ]
            ];
        });

        return response()->json([
            'collection' => [
                'version' => '1.0',
                'href' => url('/api/guru'),
                'items' => $items,
                'template' => [
                    'data' => [
                        ['name' => 'user_id', 'value' => ''],
                        ['name' => 'nip', 'value' => ''],
                        ['name' => 'nama', 'value' => ''],
                        ['name' => 'tempat_lahir', 'value' => ''],
                        ['name' => 'tgl_lahir', 'value' => ''],
                        ['name' => 'gender', 'value' => ''],
                        ['name' => 'phone_number', 'value' => ''],
                        ['name' => 'email', 'value' => ''],
                        ['name' => 'alamat', 'value' => ''],
                        ['name' => 'pendidikan', 'value' => '']
                    ]
                ]
            ]
        ], 200, ['Content-Type' => 'application/vnd.collection+json']);
    }

    // Menambah Data
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'nip' => 'required|string|unique:guru,nip',
            'nama' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tgl_lahir' => 'required|date',
            'gender' => 'required|in:laki-laki,perempuan',
            'phone_number' => 'required|string',
            'email' => 'required|email|unique:guru,email',
            'alamat' => 'required|string',
            'pendidikan' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->failedResponse($validator->errors(), 422);
        }
    
        $guru = Guru::create($request->all());
    
        if ($guru) {
            return $this->success($guru, 201, 'Guru berhasil ditambahkan!');
        } else {
            return $this->failedResponse('Guru gagal ditambahkan!', 500);
        }
    }

    // Menampilkan Data Tunggal (Berdasarkan ID)
    public function show(Guru $guru)
    {
        return $this->success($guru, 200);
    }

    // Mengupdate Data
    public function update(Request $request, Guru $guru)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'nip' => 'required|string|unique:guru,nip,' . $guru->id,
            'nama' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tgl_lahir' => 'required|date',
            'gender' => 'required|in:laki-laki,perempuan',
            'phone_number' => 'required|string',
            'email' => 'required|email|unique:guru,email,' . $guru->id,
            'alamat' => 'required|string',
            'pendidikan' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->failedResponse($validator->errors(), 422);
        }

        $guru->update($request->all());
        return $this->success($guru, 200, 'Guru berhasil diupdate!');
    }

    // Menghapus Data
    public function destroy(Guru $guru)
    {
        $deleted = $guru->delete();
        if ($deleted) {
            return $this->success(null, 200, 'Guru berhasil dihapus!');
        } else {
            return $this->failedResponse('Guru gagal dihapus!', 500);
        }
    }
}