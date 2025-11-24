<?php
session_start();
require("connect.php");

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xóa sản phẩm khỏi giỏ
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit;
}

// Cập nhật số lượng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $id => $qty) {
        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id]['quantity'] = intval($qty);
        }
    }
    header("Location: cart.php");
    exit;
}

// Lấy thông tin sản phẩm trong giỏ từ DB
$cartItems = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    $ids = array_map(fn($id) => "'" . $conn->real_escape_string($id) . "'", array_keys($_SESSION['cart']));
    $ids_list = implode(",", $ids);
    $result = $conn->query("SELECT * FROM Sofa WHERE MaSofa IN ($ids_list)");

    while ($row = $result->fetch_assoc()) {
        $id = $row['MaSofa'];
        if (isset($_SESSION['cart'][$id])) {
            $quantity = $_SESSION['cart'][$id]['quantity'];
            $subtotal = $row['GiaBan'] * $quantity;
            $total += $subtotal;
            $cartItems[$id] = [
                'MaSofa' => $id,
                'TenSofa' => $row['TenSofa'],
                'HinhAnh' => $row['HinhAnh'],
                'GiaBan' => $row['GiaBan'],
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];

            // Đồng bộ session cart với giá hiện tại
            $_SESSION['cart'][$id]['GiaBan'] = $row['GiaBan'];
        }
    }
}
?>