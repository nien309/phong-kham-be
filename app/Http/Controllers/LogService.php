<?php

namespace App\Http\Controllers;
use App\Models\Log;
use Illuminate\Http\Request;

class LogService extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
{
    $perPage = $request->query('per_page', 10);

  
    $tenBangThucHien = $request->query('tenbangthuchien');
    $idTaiKhoan = $request->query('id_taikhoan');
    $fromDate = $request->query('from_date'); // định dạng YYYY-MM-DD
    $toDate = $request->query('to_date');     // định dạng YYYY-MM-DD

    $query = Log::query();

   

    if ($tenBangThucHien) {
        $query->where('tenbangthuchien', $tenBangThucHien);
    }

    if ($idTaiKhoan) {
        $query->where('id_taikhoan', $idTaiKhoan);
    }

    if ($fromDate && $toDate) {
        $query->whereBetween('thoigianthuchien', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
    } elseif ($fromDate) {
        $query->whereDate('thoigianthuchien', '>=', $fromDate);
    } elseif ($toDate) {
        $query->whereDate('thoigianthuchien', '<=', $toDate);
    }

    return response()->json(
        $query->orderByDesc('thoigianthuchien')->paginate($perPage)
    );
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
