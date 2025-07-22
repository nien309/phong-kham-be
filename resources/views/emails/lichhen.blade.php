<h2>Xin chào {{ $lichhen->khachhang->taikhoan->hoten }}</h2>
@if ($lichhen->nhanvien)
    <p>Bạn đã đặt lịch khám thành công với bác sĩ: {{ $lichhen->nhanvien->taikhoan->hoten }}</p>
@endif
<p>Ngày hẹn: {{ $lichhen->ngayhen }}</p>
<p>Ca khám: {{ $lichhen->cakham->khunggio }}</p>
<p>Trạng thái: {{ $lichhen->trangthai }}</p>
