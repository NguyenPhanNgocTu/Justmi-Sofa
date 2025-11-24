<?php
session_start();
require_once("connect.php");

// Lấy tham số id (MaSofa) và loai (MaLoai) — KHÔNG ép kiểu số
$id = isset($_GET['id']) ? trim($_GET['id']) : '';
$loai = isset($_GET['loai']) ? trim($_GET['loai']) : '';

// Nếu có id (xem chi tiết), thì bỏ qua biến loại để tránh xung đột
if ($id !== '') {
    $loai = '';
}

// Escape để dùng trong query khi không dùng prepared (an toàn hơn)
$id_esc = $conn->real_escape_string($id);
$loai_esc = $conn->real_escape_string($loai);

// Lấy danh mục sofa + 1 ảnh minh họa từ bảng Sofa
$sqlLoai = "
    SELECT 
        l.MaLoai, 
        l.TenLoai,
        (
            SELECT s.HinhAnh 
            FROM Sofa s 
            WHERE s.MaLoai = l.MaLoai 
            LIMIT 1
        ) AS HinhAnh
    FROM LoaiSofa l
";
$loaiResult = $conn->query($sqlLoai);
$loaiArr = [];
while ($row = $loaiResult->fetch_assoc()) {
    $loaiArr[] = $row;
}
// Lấy màu sắc
    $colorResult = $conn->query("
        SELECT ms.MaMau, ms.TenMau 
        FROM MauSac ms
        INNER JOIN Sofa_MauSac sm ON ms.MaMau = sm.MaMau
        WHERE sm.MaSofa = '$id'
    ");
    $colors = [];
    while ($row = $colorResult->fetch_assoc()) {
        $colors[] = $row;
    }

    // Lấy kích thước
    $sizeResult = $conn->query("
        SELECT MaKichThuoc, ChieuDai, ChieuRong, ChieuCao, DonVi 
        FROM KichThuoc
        WHERE MaSofa = '$id'
    ");
    $sizes = [];
    while ($row = $sizeResult->fetch_assoc()) {
        $sizes[] = $row;
    }
?>