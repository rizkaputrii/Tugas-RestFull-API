<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
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
    public function index()
    {
        $data = User::all();
        return $this->success($data, 200);
    }

    // Menambah Data
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:admin,guru',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            $msg = $validator->errors();
            return $this->failedResponse($msg, 422);
        }

        $user = new User();
        $user->type = $request->type;
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $saveUser = $user->save();

        if ($saveUser) {
            return $this->success($user, 201);
        } else {
            return $this->failedResponse('User gagal ditambahkan!', 500);
        }
    }

    // Menampilkan Data Tunggal
    public function show(User $user)
    {
        return $this->success($user, 200);
    }

    // Mengubah Data
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:admin,guru',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'password' => 'nullable|min:6'
        ]);

        if ($validator->fails()) {
            $msg = $validator->errors();
            return $this->failedResponse($msg, 422);
        }

        $user->type = $request->type;
        $user->username = $request->username;

        if ($request->has('password')) {
            $user->password = $request->password ? Hash::make($request->password) : $user->password;
        }

        $saved = $user->save();

        if ($saved) {
            return $this->success($user, 200);
        } else {
            return $this->failedResponse('User gagal diupdate!', 500);
        }
    }

    // Menghapus Data
    public function destroy(User $user)
    {
        $deleteData = $user->delete();

        if ($deleteData) {
            return $this->success(null, 200);
        } else {
            return $this->failedResponse('User gagal dihapus!', 500);
        }
    }
}