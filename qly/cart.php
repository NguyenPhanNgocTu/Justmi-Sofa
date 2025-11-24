<?php
session_start();
require("connect.php");

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xóa sản phẩm khỏi giỏ
if (isset($_GET['remove'])) {
    $key = $_GET['remove'];
    unset($_SESSION['cart'][$key]);
    header("Location: cart.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {

    // Cập nhật số lượng
    foreach ($_POST['quantity'] as $id => $qty) {
        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id]['quantity'] = intval($qty);
        }
    }

    // Cập nhật màu sắc
    if (isset($_POST['color'])) {
        foreach ($_POST['color'] as $id => $color) {
            $_SESSION['cart'][$id]['color'] = $color;
        }
    }

    // Cập nhật kích thước
    if (isset($_POST['size'])) {
        foreach ($_POST['size'] as $id => $size) {
            $_SESSION['cart'][$id]['size'] = $size;
        }
    }

    header("Location: cart.php");
    exit;
}


// Lấy thông tin sản phẩm trong giỏ từ DB
$cartItems = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $sofaIds = [];

    // Lấy danh sách mã Sofa thật (không gồm màu/size)
    foreach ($_SESSION['cart'] as $key => $item) {
        $parts = explode("_", $key);
        $sofaIds[] = "'" . $conn->real_escape_string($parts[0]) . "'";
    }

    $ids_list = implode(",", $sofaIds);

    $result = $conn->query("SELECT * FROM Sofa WHERE MaSofa IN ($ids_list)");

    while ($row = $result->fetch_assoc()) {
        foreach ($_SESSION['cart'] as $key => $item) {
            $parts = explode("_", $key);
            $maSofaThuc = $parts[0];

            if ($maSofaThuc == $row['MaSofa']) {

                $quantity = $item['quantity'];
                $subtotal = $row['GiaBan'] * $quantity;
                $total += $subtotal;
                // Truyền key vào để xóa đúng mục
                $cartItems[$key] = [
                    'key' => $key,
                    'MaSofa' => $row['MaSofa'],
                    'TenSofa' => $row['TenSofa'],
                    'HinhAnh' => $row['HinhAnh'],
                    'GiaBan' => $row['GiaBan'],
                    'quantity' => $quantity,
                    'color' => $item['color'] ?? '',
                    'size' => $item['size'] ?? '',
                    'subtotal' => $subtotal
                ];
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Giỏ hàng | Justmi Studio</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include("includes/header.html"); ?>

<div class="container my-4">

  <!-- Hiển thị thông báo thanh toán thành công -->
<?php 
if (!empty($_SESSION['success_msg'])) {
    echo '<div class="alert alert-success text-center">'.$_SESSION['success_msg'].'</div>';
    unset($_SESSION['success_msg']);
}
if (!empty($_SESSION['error_msg'])) {
    echo '<div class="alert alert-danger text-center">'.$_SESSION['error_msg'].'</div>';
    unset($_SESSION['error_msg']);
}
?>


  <h2 class="mb-4 text-center">🛒 Giỏ hàng của bạn</h2>

  <?php if (empty($cartItems)): ?>
      <div class="alert alert-info text-center">
        Giỏ hàng trống. <a href="product.php">Mua sắm ngay!</a>
      </div>
  <?php else: ?>
    <form method="post">
      <table class="table table-bordered align-middle text-center">
        <thead class="table-light">
          <tr>
            <th>Hình ảnh</th>
            <th>Tên sản phẩm</th>
            <th>Màu sắc</th>
            <th>Kích thước</th>
            <th>Đơn giá</th>
            <th>Số lượng</th>
            <th>Thành tiền</th>
            <th>Xóa</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cartItems as $item): ?>
            <tr>
              <td><img src="images/<?= htmlspecialchars($item['HinhAnh']) ?>" width="80"></td>
              <td><?= htmlspecialchars($item['TenSofa']) ?></td>
              <td>
                  <select name="color[<?= $item['key'] ?>]" class="form-select form-select-sm" required>
                      <option value="">-- Chọn màu --</option>
                      <?php
                      $maSofa = $item['MaSofa'];
                      $colors = $conn->query("
                          SELECT ms.MaMau, ms.TenMau
                          FROM Sofa_MauSac sms
                          JOIN MauSac ms ON sms.MaMau = ms.MaMau
                          WHERE sms.MaSofa = '$maSofa'
                      ");
                      while ($m = $colors->fetch_assoc()):
                          $selected = ($item['color'] ?? '') == $m['MaMau'] ? "selected" : "";
                      ?>
                          <option value="<?= $m['MaMau'] ?>" <?= $selected ?>>
                              <?= $m['TenMau'] ?>
                          </option>
                      <?php endwhile; ?>
                  </select>
              </td>
              <td>
                  <select name="size[<?= $item['key'] ?>]" class="form-select form-select-sm" required>
                      <option value="">-- Chọn kích thước --</option>

                      <?php
                      // Lấy kích thước theo đúng sofa hiện tại
                      $maSofa = $item['MaSofa'];
                      $sizes = $conn->query("SELECT * FROM KichThuoc WHERE MaSofa = '$maSofa'");

                      while ($s = $sizes->fetch_assoc()):
                          $sizeId = $s['MaKichThuoc'];
                          $selected = ($item['size'] ?? '') == $sizeId ? "selected" : "";
                      ?>
                          <option value="<?= $sizeId ?>" <?= $selected ?>>
                              <?= $s['ChieuDai'] . " x " . $s['ChieuRong'] . " x " . $s['ChieuCao'] . " " . $s['DonVi'] ?>
                          </option>
                      <?php endwhile; ?>
                  </select>
              </td>
              <td>₫<?= number_format($item['GiaBan'],0,',','.') ?></td>
              <td style="width:100px">
                <input type="number" name="quantity[<?= $item['key'] ?>]" 
                       value="<?= $item['quantity'] ?>" min="1" class="form-control form-control-sm text-center">
              </td>
              <td>₫<?= number_format($item['subtotal'],0,',','.') ?></td>
              <td>
                <a href="cart.php?remove=<?= $item['key'] ?>" class="btn btn-sm btn-danger">🗑</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
          <button type="submit" name="update_cart" class="btn btn-primary me-2">Cập nhật giỏ hàng</button>
          <a href="product.php" class="btn btn-outline-secondary">← Tiếp tục mua sắm</a>
        </div>
      </div>
    </form>
            <div class="text-end mt-3">
          <strong class="fs-5 me-3">Tổng cộng: ₫<?= number_format($total,0,',','.') ?></strong>
          <form action="checkout.php" method="post" class="d-inline">
            <button type="submit" class="btn btn-success">Thanh toán</button>
          </form>
        </div>

  <?php endif; ?>
</div>

<footer class="bg-light text-center py-3 mt-4 text-muted">
  Justmi Studio &copy; 2025
</footer>
</body>
</html>
<?php $conn->close(); ?>
