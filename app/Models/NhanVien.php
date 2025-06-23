<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\zEloquent\Model;

class NhanVien extends Model
{
    use HasFactory;
    protected $fillable = ['chucvu', 'luong'];

}
