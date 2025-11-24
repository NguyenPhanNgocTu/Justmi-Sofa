<?php
session_start();
require("connect.php");

// Nếu chưa đăng nhập, redirect về login
if (!isset($_SESSION['MaKH'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header("Location: login.php");
    exit;
}

$MaKH = $_SESSION['MaKH'];
$cart = $_SESSION['cart'] ?? [];

// Nếu giỏ hàng trống
if (empty($cart)) {
    $_SESSION['error_msg'] = "Giỏ hàng trống. Không thể thanh toán!";
    header("Location: cart.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Tính tổng tiền
    $tongTien = 0;
    foreach ($cart as $item) {
        if (isset($item['GiaBan'], $item['quantity'])) {
            $tongTien += $item['GiaBan'] * $item['quantity'];
        }
    }

    // Tạo MaHD tự sinh
    $res = $conn->query("SELECT MaHD FROM HoaDon ORDER BY MaHD DESC LIMIT 1");
    if ($res && $row = $res->fetch_assoc()) {
        $num = (int)substr($row['MaHD'], 2) + 1;
        $MaHD = "HD" . str_pad($num, 3, "0", STR_PAD_LEFT);
    } else {
        $MaHD = "HD001";
    }

    // Lưu hóa đơn
    $stmt = $conn->prepare("INSERT INTO HoaDon(MaHD, MaKH, NgayLap, TongTien) VALUES (?, ?, NOW(), ?)");
    $stmt->bind_param("ssd", $MaHD, $MaKH, $tongTien);
    if (!$stmt->execute()) {
        $_SESSION['error_msg'] = "Thanh toán thất bại, vui lòng thử lại!";
        header("Location: cart.php");
        exit;
    }
    $stmt->close();

    // Lưu chi tiết hóa đơn
    $stmt2 = $conn->prepare("INSERT INTO CTHD(MaHD, MaSofa, MaMau, MaKichThuoc, SoLuong, DonGia, ThanhTien) VALUES (?,?,?,?,?,?,?)");
    foreach ($cart as $item) {
        if (!isset($item['MaSofa'], $item['MaMau'], $item['MaKichThuoc'], $item['quantity'], $item['GiaBan'])) continue;

        $sl = $item['quantity'];
        $dg = $item['GiaBan'];
        $tt = $sl * $dg;

        $MaSofa = $item['MaSofa'];
        $MaMau = $item['MaMau'];
        $MaKichThuoc = $item['MaKichThuoc'];

        $stmt2->bind_param("ssssidd", $MaHD, $MaSofa, $MaMau, $MaKichThuoc, $sl, $dg, $tt);
        $stmt2->execute();
    }
    $stmt2->close();

    // Xóa giỏ hàng
    unset($_SESSION['cart']);

    $_SESSION['success_msg'] = "✅ Thanh toán thành công! Mã hóa đơn: $MaHD";
    header("Location: cart.php");
    exit;
}

// Nếu GET, quay về cart
header("Location: cart.php");
exit;
?>
