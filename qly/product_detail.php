<?php
    require_once("connect.php");
    $page_title = "Product detail";

    // Lấy ID sản phẩm từ GET, giữ nguyên kiểu chuỗi
    $id = $_GET['id'] ?? '';
    $id = $conn->real_escape_string($id);

    // Lấy thông tin sản phẩm
    $result = $conn->query("SELECT * FROM Sofa WHERE MaSofa = '$id'");
    if (!$result || $result->num_rows == 0) {
        echo "Không tìm thấy sản phẩm.";
        exit;
    }
    $product = $result->fetch_assoc();

    // Lấy danh mục
    $loaiResult = $conn->query("SELECT MaLoai, TenLoai FROM LoaiSofa");
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
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Header -->
    <header class="topbar">
        <?php include ('includes/header.html');?>
    </header>

    <!-- Main -->
    <main class="container my-4">
        <div class="row g-4">
            <!-- Sidebar -->
            <aside class="col-md-3">
                <?php include("module/categories.php");?>
            </aside>

            <!-- Product detail -->
            <section class="col-md-9">
                <div class="card p-4 shadow-sm">
                    <div class="row g-4">
                        <div class="col-md-5">
                            <img src="images/<?= htmlspecialchars($product['HinhAnh']) ?>" 
                                 alt="<?= htmlspecialchars($product['TenSofa']) ?>" 
                                 class="img-fluid rounded">
                        </div>

                        <div class="col-md-7">
                            <h2><?= htmlspecialchars($product['TenSofa']) ?></h2>
                            <div class="text-muted mb-2">Mã sản phẩm: <?= htmlspecialchars($product['MaSofa']) ?></div>
                            <h4 class="text-danger">₫<?= number_format($product['GiaBan'], 0, ',', '.') ?></h4>

                            <p class="mt-3"><strong>Loại:</strong> <?= htmlspecialchars($product['MaLoai']) ?></p>
                            <p><strong>Mô tả:</strong> <?= nl2br(htmlspecialchars($product['MoTa'] ?? 'Chưa có mô tả')) ?></p>

                            <div class="mt-4">
                                <form action="cart.php" method="post" class="d-flex flex-column gap-2">
                                    <input type="hidden" name="id" value="<?= $product['MaSofa'] ?>">

                                    <!-- Số lượng -->
                                    <div class="d-flex align-items-center gap-2">
                                        <label>Số lượng:</label>
                                        <input type="number" name="quantity" value="1" min="1" class="form-control w-25">
                                    </div>

                                    <!-- Màu sắc -->
                                    <?php if (!empty($colors)): ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <label>Màu sắc:</label>
                                        <select name="color" class="form-select w-50">
                                            <?php foreach ($colors as $c): ?>
                                                <option value="<?= $c['MaMau'] ?>"><?= htmlspecialchars($c['TenMau']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Kích thước -->
                                    <?php if (!empty($sizes)): ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <label>Kích thước:</label>
                                        <select name="size" class="form-select w-50">
                                            <?php foreach ($sizes as $s): ?>
                                                <option value="<?= $s['MaKichThuoc'] ?>">
                                                    <?= $s['ChieuDai'].'x'.$s['ChieuRong'].'x'.$s['ChieuCao'].' '.$s['DonVi'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <?php endif; ?>

                                    <button type="submit" class="btn btn-primary mt-2">🛒 Thêm vào giỏ</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <?php include ('includes/footer.html'); ?>
</body>
</html>
