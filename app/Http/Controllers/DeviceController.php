<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeviceResource;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DeviceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('admin', except: ['status', 'link', 'sync'])

        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        // search by device_name or device_code or user name or user email

        $devices = Device::where(function ($query) use ($request) {
            $search = $request->get('search');
            if ($search) {
                $query->where('device_name', 'like', "%$search%")
                    ->orWhere('device_code', 'like', "%$search%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%");
                    });
            }
        })
            ->with('user:id,name,email')
            ->orderBy('registered_at', 'desc')
            ->paginate($perPage);

        return DeviceResource::collection($devices);
    }

    public function linkedDevices(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        //  search by device_name or device_code or user name or user email
        $devices = Device::whereNotNull('user_id')
            ->where(function ($query) use ($request) {
                $search = $request->get('search');
                if ($search) {
                    $query->where('device_name', 'like', "%$search%")
                        ->orWhere('device_code', 'like', "%$search%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('name', 'like', "%$search%")
                                ->orWhere('email', 'like', "%$search%");
                        });
                }
            })
            ->with('user:id,name,email')
            ->orderBy('registered_at', 'desc')
            ->paginate($perPage);

        return DeviceResource::collection($devices);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'device_code' => 'required|string|unique:devices,device_code',
            'device_name' => 'nullable|string',
        ]);

        $device = Device::create($data);

        return new DeviceResource($device);
    }

    /**
     * Display the specified resource.
     */
    public function show(Device $device)
    {
        return new DeviceResource($device);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Device $device)
    {
        $data = $request->validate([
            'device_name' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $device->update($data);

        return new DeviceResource($device);
    }

    public function link(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'device_code' => 'required|string|exists:devices,device_code',
        ], [
            'user_id.exists' => 'User not found',
            'device_code.required' => 'Device code tidak boleh kosong',
            'device_code.string' => 'Device code harus berupa string',
            'device_code.exists' => 'Device tidak ditemukan',
        ]);

        // nonaktifkan device lama user (jika hanya boleh 1 aktif)
        Device::where('user_id', $data['user_id'])->update(['is_active' => false]);

        $device = Device::where('device_code', $data['device_code'])->firstOrFail();

        if ($device->user_id && $device->user_id != $data['user_id']) {
            return response()->json(['message' => 'Device sudah terdaftar pada user lain'], 400);
        }

        $device->update([
            'user_id' => $data['user_id'],
            'is_active' => true,
            'registered_at' => $device->registered_at ?? now(),
        ]);

        return new DeviceResource($device);
    }

    public function unlink(Request $request, Device $device)
    {
        if (!$device->user_id) {
            return response()->json([
                'message' => 'Device belum tertaut ke user manapun'
            ], 422);
        }

        $user = $request->user();

        // Jika bukan pemilik device dan bukan admin → forbidden
        if ($device->user_id != $user->id && !$user->is_admin) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk melepaskan device ini'
            ], 403);
        }

        // Validasi password jika ingin unlink
        $data = $request->validate([
            'password' => 'required|string',
        ]);

        if (!Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Password salah'
            ], 400);
        }


        // Putuskan relasi
        $device->update([
            'user_id'       => null,
            'is_active'     => false,
            'registered_at' => null,
            'last_sync_at'  => null,
        ]);

        return response()->json([
            'message' => 'Device berhasil dilepas dari user',
            'device'  => $device,
        ], 200);
    }


    public function status(Request $request)
    {
        $userId = $request->user()->id; // ambil dari auth
        $device = Device::where('user_id', $userId)
            ->where('is_active', true)
            ->first();

        return response()->json([
            'linked' => (bool) $device,
            'device' => $device ? new DeviceResource($device) : null,
        ]);
    }

    public function sync(Device $device)
    {
        $device->update(['last_sync_at' => now()]);
        return new DeviceResource($device);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Device $device)
    {
        $device->delete();

        return response()->json(['message' => 'Device deleted successfully']);
    }
}
