<?php

namespace App\Http\Controllers;

use App\Models\CaKham;
use Illuminate\Http\Request;

class CaKhamController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'khunggio' => 'required|in:sáng,chiều',
            'trangthai' => 'required|in:đang hoạt động,đã tắt',
        ]);

        return CaKham::create($validated);
    }

    public function update(Request $request, $id)
    {
        $cakham = CaKham::findOrFail($id);

        $validated = $request->validate([
            'khunggio' => 'in:sáng,chiều',
            'trangthai' => 'in:đang hoạt động,đã tắt',
        ]);

        $cakham->update($validated);
        return $cakham;
    }
}