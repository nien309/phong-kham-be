<?php

namespace App\Http\Controllers;

use App\Models\ToaThuoc;
use Illuminate\Http\Request;
use App\Services\LogService;
use Illuminate\Support\Facades\Auth;

class ToaThuocController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->chucvu !== 'bacsi') return response()->json(['message' => 'Chỉ bác sĩ được tạo toa thuốc'], 403);

        $validated = $request->validate([
            'id_thongtinkhambenh' => 'required|exists:thongtinkhambenh,id_thongtinkhambenh',
            'chandoan' => 'nullable|string',
            'ngayketoa' => 'required|date',
            'trangthai' => 'nullable|string',
        ]);

        $toa = ToaThuoc::create($validated);
        LogService::log('Tạo toa thuốc ID: ' . $toa->id_toathuoc, 'toathuoc');
        return response()->json($toa, 201);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->chucvu !== 'bacsi') return response()->json(['message' => 'Chỉ bác sĩ được cập nhật'], 403);

        $toa = ToaThuoc::findOrFail($id);
        $toa->update($request->only(['chandoan', 'trangthai']));
        LogService::log('Cập nhật toa thuốc ID: ' . $id, 'toathuoc');
        return response()->json($toa);
    }

    public function show($id)
    {
        return response()->json(ToaThuoc::findOrFail($id));
    }
}
