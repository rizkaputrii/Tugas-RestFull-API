<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Model\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class SiswaController extends Controller
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
        $siswas = Siswa::all();

        $items = $siswas->map(function($s) {
            return [
                'href' => url('/api/siswa/' . $s->id),
                'data' => [
                    ['name' => 'id', 'value' => $s->id],
                    ['name' => 'nis', 'value' => $s->nis],
                    ['name' => 'nama', 'value' => $s->nama],
                    ['name' => 'gender', 'value' => $s->gender],
                    ['name' => 'tempat_lahir', 'value' => $s->tempat_lahir],
                    ['name' => 'tgl_lahir', 'value' => $s->tgl_lahir],
                    ['name' => 'email', 'value' => $s->email],
                    ['name' => 'nama_ortu', 'value' => $s->nama_ortu],
                    ['name' => 'alamat', 'value' => $s->alamat],
                    ['name' => 'phone_number', 'value' => $s->phone_number],
                    ['name' => 'kelas_id', 'value' => $s->kelas_id]
                ],
                'links' => [
                    ['rel' => 'kelas', 'href' => url('/api/kelas/' . $s->kelas_id)]
                ]
            ];
        });

        return response()->json([
            'collection' => [
                'version' => '1.0',
                'href' => url('/api/siswa'),
                'items' => $items,
                'template' => [
                    'data' => [
                        ['name' => 'nis', 'value' => ''],
                        ['name' => 'nama', 'value' => ''],
                        ['name' => 'gender', 'value' => ''],
                        ['name' => 'tempat_lahir', 'value' => ''],
                        ['name' => 'tgl_lahir', 'value' => ''],
                        ['name' => 'email', 'value' => ''],
                        ['name' => 'nama_ortu', 'value' => ''],
                        ['name' => 'alamat', 'value' => ''],
                        ['name' => 'phone_number', 'value' => ''],
                        ['name' => 'kelas_id', 'value' => '']
                    ]
                ]
            ]
        ], 200, ['Content-Type' => 'application/vnd.collection+json']);
    }

    // Menambah Data
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|unique:siswa,nis',
            'nama' => 'required|string',
            'gender' => 'required|in:laki-laki,perempuan',
            'tempat_lahir' => 'required|string',
            'tgl_lahir' => 'required|date',
            'email' => 'required|email|unique:siswa,email', // <-- Tambahkan baris ini
            'nama_ortu' => 'required|string',
            'alamat' => 'required|string',
            'phone_number' => 'required|string',
            'kelas_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->failedResponse($validator->errors(), 422);
        }

        $siswa = Siswa::create($request->all());

        if ($siswa) {
            return $this->success($siswa, 201, 'Data Siswa berhasil ditambahkan!');
        } else {
            return $this->failedResponse('Data Siswa gagal ditambahkan!', 500);
        }
    }

    // Menampilkan Data Tunggal (Berdasarkan ID)
    public function show(Siswa $siswa)
    {
        return $this->success($siswa, 200);
    }

    // Mengupdate Data
    public function update(Request $request, Siswa $siswa)
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|unique:siswa,nis,' . $siswa->id,
            'nama' => 'required|string',
            'gender' => 'required|in:laki-laki,perempuan',
            'tempat_lahir' => 'required|string',
            'tgl_lahir' => 'required|date',
            'email' => 'required|email|unique:siswa,email,' . $siswa->id, // <-- Tambahkan baris ini
            'nama_ortu' => 'required|string',
            'alamat' => 'required|string',
            'phone_number' => 'required|string',
            'kelas_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->failedResponse($validator->errors(), 422);
        }

        $siswa->update($request->all());
        return $this->success($siswa, 200, 'Data Siswa berhasil diupdate!');
    }

    // Menghapus Data
    public function destroy(Siswa $siswa)
    {
        $deleted = $siswa->delete();
        if ($deleted) {
            return $this->success(null, 200, 'Siswa berhasil dihapus!');
        } else {
            return $this->failedResponse('Siswa gagal dihapus!', 500);
        }
    }
}