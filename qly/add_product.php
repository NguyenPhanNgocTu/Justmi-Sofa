<?php
require("connect.php");

$ma_sofa = "";
$ten_sofa = "";
$error = $success = "";
$added_product = null;

// Nếu người dùng đã chọn hãng và loại sữa
if (isset($_REQUEST['hang_sofa']) && isset($_REQUEST['loai_sofa']) && $_REQUEST['hang_sua'] != "" && $_REQUEST['loai_sua'] != "") {
    $ma_hang_sua = $_REQUEST['hang_sofa'];
    $ma_loai_sua = $_REQUEST['loai_sofa'];

    // Sinh mã sữa tự động
    $prefix = $ma_hang_sua;
    $result = $conn->query("
        SELECT MaHang 
        FROM sua 
        WHERE MaHang='{$ma_hang}' 
          AND MaLoai='{$ma_loai}' 
        ORDER BY MaSofa DESC 
        LIMIT 1
    ");
    $last_ma = $result->fetch_assoc()['MaSofa'] ?? '';
    $stt = $last_ma ? (int)substr($last_ma, strlen($prefix)) + 1 : 1;
    $num_digits = strlen($prefix) == 2 ? 4 : 3;
    $ma_sofa = $prefix . str_pad($stt, $num_digits, "0", STR_PAD_LEFT);

    // Kiểm tra trùng mã
    while ($conn->query("SELECT 1 FROM Sofa WHERE MaSofa='$ma_sofa'")->num_rows > 0) {
        $stt++;
        $ma_sofa = $prefix . str_pad($stt, $num_digits, "0", STR_PAD_LEFT);
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submit'])) {
    $errors = array();
    $ten_sofa = trim($_POST['ten_sofa'] ?? "");
    $ma_hang = $_POST['hang_sofa'] ?? "";
    $ma_loai = $_POST['loai_sofa'] ?? "";
    $trong_luong = trim($_POST['trong_luong'] ?? "");
    $don_gia = trim($_POST['don_gia'] ?? "");
    $tp_dinh_duong = $_POST['tp_dinh_duong'] ?? "";
    $loi_ich = $_POST['loi_ich'] ?? "";

    if ($ten_sua == "") $errors[] = "Bạn chưa nhập tên sữa.";
    if ($ma_hang_sua == "" || $ma_loai_sua == "") $errors[] = "Bạn phải chọn hãng sữa và loại sữa.";
    if ($trong_luong == "" || !is_numeric($trong_luong)) $errors[] = "Trọng lượng phải là số.";
    if ($don_gia == "" || !is_numeric($don_gia)) $errors[] = "Đơn giá phải là số.";

    // Xử lý hình ảnh
    $ten_hinh = "";
    if (!empty($_FILES['hinh_sua']['name'])) {
        $extension = pathinfo($_FILES['hinh_sua']['name'], PATHINFO_EXTENSION);
        $ten_hinh = $ma_sua . '.' . $extension; // ảnh đặt tên theo mã sữa
        move_uploaded_file($_FILES['hinh_sua']['tmp_name'], __DIR__ . "/Hinh_sua/" . $ten_hinh);
    }

    if (empty($errors)) {
        $query = "INSERT INTO sua 
                  (Ma_sua, Ten_sua, Ma_hang_sua, Ma_loai_sua, Trong_luong, Don_gia, TP_Dinh_Duong, Loi_ich, Hinh)
                  VALUES 
                  ('$ma_sua', '$ten_sua', '$ma_hang_sua', '$ma_loai_sua', '$trong_luong', '$don_gia', '$tp_dinh_duong', '$loi_ich', '$ten_hinh')";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_affected_rows($conn) == 1) {
            $success = "Thêm sữa thành công!";
            $added_product = [
                'Ma_sua' => $ma_sua,
                'Ten_sua' => $ten_sua,
                'Ten_hang_sua' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT Ten_hang_sua FROM hang_sua WHERE Ma_hang_sua='$ma_hang_sua'"))['Ten_hang_sua'],
                'Ten_loai' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT Ten_loai FROM loai_sua WHERE Ma_loai_sua='$ma_loai_sua'"))['Ten_loai'],
                'Trong_luong' => $trong_luong,
                'Don_gia' => $don_gia,
                'Hinh' => $ten_hinh
            ];
        } else {
            $error = "Lỗi khi thêm sữa: " . mysqli_error($conn);
        }
    } else {
        $error = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <link rel="stylesheet" href="includes/style_admin.css" type="text/css" media="screen" />
    </head>
    <body>
        <form action="" method="post" enctype="multipart/form-data">
            <h2>Thêm sản phẩm</h2>

            <?php if($error): ?><p style="color:red"><?= $error ?></p><?php endif; ?>
            <?php if($success): ?><p style="color:green"><?= $success ?></p><?php endif; ?>

            <div class="form-group">
                <label>Mã sữa:</label>
                <input type="text" name="ma_sua" readonly value="<?= $ma_sua ?>">
            </div>

            <div class="form-group">
                <label>Tên sữa:</label>
                <input type="text" name="ten_sua" value="<?= $ten_sua ?>">
            </div>

            <div class="form-group">
                <label>Hãng sữa:</label>
                <select name="hang_sua" onchange="this.form.submit()">
                    <option value="">---Chọn hãng sữa---</option>
                    <?php
                    $query = "SELECT * FROM hang_sua";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_array($result)) {
                        $selected = (isset($_REQUEST['hang_sua']) && $_REQUEST['hang_sua'] == $row['Ma_hang_sua']) ? "selected" : "";
                        echo "<option value='{$row['Ma_hang_sua']}' $selected>{$row['Ten_hang_sua']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Loại sữa:</label>
                <select name="loai_sua" onchange="this.form.submit()">
                    <option value="">---Chọn loại sữa---</option>
                    <?php
                    $query = "SELECT * FROM loai_sua";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_array($result)) {
                        $selected = (isset($_REQUEST['loai_sua']) && $_REQUEST['loai_sua'] == $row['Ma_loai_sua']) ? "selected" : "";
                        echo "<option value='{$row['Ma_loai_sua']}' $selected>{$row['Ten_loai']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Trọng lượng:</label>
                <input type="text" name="trong_luong" value="<?= $trong_luong ?>"> (gr/ml)
            </div>

            <div class="form-group">
                <label>Đơn giá:</label>
                <input type="text" name="don_gia" value="<?= $don_gia ?>">
            </div>

            <div class="form-group">
                <label>Thành phần dinh dưỡng:</label>
                <textarea name="tp_dinh_duong"><?= $tp_dinh_duong ?></textarea>
            </div>

            <div class="form-group">
                <label>Lợi ích:</label>
                <textarea name="loi_ich"><?= $loi_ich ?></textarea>
            </div>

            <div class="form-group">
                <label>Hình ảnh:</label>
                <input type="file" name="hinh_sua" accept="image/*">
            </div>

            <div class="form-group">
                <input type="submit" name="submit" value="Thêm sữa">
            </div>
        </form>

        <?php if ($added_product): ?>
        <div class="product-detail">
            <h3>Chi tiết sản phẩm vừa thêm</h3>
            <?php if($added_product['Hinh']): ?>
                <img src="Hinh_sua/<?= $added_product['Hinh'] ?>" alt="<?= $added_product['Ten_sua'] ?>">
            <?php endif; ?>
            <p><b>Mã sữa:</b> <?= $added_product['Ma_sua']?></p>
            <p><b>Tên sữa:</b> <?= $added_product['Ten_sua'] ?></p>
            <p><b>Hãng sữa:</b> <?= $added_product['Ten_hang_sua'] ?></p>
            <p><b>Loại sữa:</b> <?= $added_product['Ten_loai'] ?></p>
            <p><b>Trọng lượng:</b> <?= $added_product['Trong_luong']?> gr/ml</p>
            <p><b>Đơn giá:</b> <?= number_format($added_product['Don_gia']) ?> VNĐ</p>
        </div>
        <?php endif; ?>
    </body>
</html>
