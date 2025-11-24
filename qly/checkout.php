<?php
session_start();
require("connect.php");

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    $_SESSION['error_msg'] = "Giỏ hàng trống. Không thể thanh toán!";
    header("Location: cart.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sinh mã hóa đơn tự động
    $res = $conn->query("SELECT MaHD FROM HoaDon ORDER BY MaHD DESC LIMIT 1");
    if ($res && $row = $res->fetch_assoc()) {
        $num = (int)substr($row['MaHD'], 2) + 1;
        $MaHD = "HD" . str_pad($num, 3, '0', STR_PAD_LEFT);
    } else {
        $MaHD = "HD001";
    }

    // Tính tổng tiền
    $tongTien = 0;
    foreach ($cart as $item) {
        $tongTien += $item['GiaBan'] * $item['quantity'];
    }

    // Lưu hóa đơn, bind luôn MaHD
    $stmt = $conn->prepare("INSERT INTO HoaDon(MaHD, MaKH, NgayLap, TongTien) VALUES (?, ?, NOW(), ?)");
    $stmt->bind_param("ssd", $MaHD, $MaKH, $tongTien);

    if (!$stmt->execute()) {
        $_SESSION['error_msg'] = "Thanh toán thất bại, vui lòng thử lại!";
        header("Location: cart.php");
        exit;
    }
    $stmt->close();

    // Lưu chi tiết hóa đơn
    $stmt2 = $conn->prepare("INSERT INTO CTHD(MaHD, MaSofa, MaMau, MaKichThuoc, SoLuong, DonGia, ThanhTien) 
                         VALUES (?,?,?,?,?,?,?)");

    foreach ($cart as $item) {

        if (empty($item['color'])) {
            $_SESSION['error_msg'] = "Vui lòng chọn màu sắc cho sản phẩm trước khi thanh toán!";
            header("Location: cart.php");
            exit;
        }

        if (empty($item['size'])) {
            $_SESSION['error_msg'] = "Vui lòng chọn kích thước trước khi thanh toán!";
            header("Location: cart.php");
            exit;
        }

        $MaSofa = $item['MaSofa'];
        $MaMau = $item['color'];
        $MaKichThuoc = $item['size'];
        $sl = (int)$item['quantity'];
        $dg = (float)$item['GiaBan'];
        $tt = $sl * $dg;

        $stmt2->bind_param("ssssidd", $MaHD, $MaSofa, $MaMau, $MaKichThuoc, $sl, $dg, $tt);
        $stmt2->execute();
    }

    $stmt2->close();

    unset($_SESSION['cart']);
    $_SESSION['success_msg'] = "Thanh toán thành công! Mã hóa đơn: $MaHD";
    header("Location: cart.php");
    exit;
}

header("Location: cart.php");
exit;
?>
