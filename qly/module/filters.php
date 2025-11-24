<?php
    // Lấy filter từ GET
    $filterTypes = $_GET['maLoai'] ?? [];
    if (!is_array($filterTypes)) {
        $filterTypes = [$filterTypes]; // nếu chỉ 1 giá trị, chuyển thành array
    }
    $minPrice = $_GET['min_price'] ?? '';
    $maxPrice = $_GET['max_price'] ?? '';
    
    // Lấy danh sách loại sofa
    $loaiResult = $conn->query("SELECT MaLoai, TenLoai FROM LoaiSofa");
    $loaiArr = [];
    while($row = $loaiResult->fetch_assoc()){
        $loaiArr[] = $row;
    }

    $where = "WHERE 1";
    if ($filterTypes) {
        $types = array_map(fn($t) => "'" . $conn->real_escape_string($t) . "'", $filterTypes);
        $where .= " AND MaLoai IN (" . implode(",", $types) . ")";
    }
    if ($minPrice !== '' && is_numeric($minPrice)) {
        $where .= " AND GiaBan >= " . floatval($minPrice);
    }
    if ($maxPrice !== '' && is_numeric($maxPrice)) {
        $where .= " AND GiaBan <= " . floatval($maxPrice);
    }
?>

<div class="filters">
    <h4>Bộ lọc</h4>
    <form method="get">
        <!-- Lọc theo loại sofa -->
        <div>
            <?php foreach($loaiArr as $loai): ?>
                <label>
                    <input type="checkbox" name="maLoai[]" value="<?= $loai['MaLoai'] ?>" 
                           <?= in_array($loai['MaLoai'], $filterTypes) ? 'checked' : '' ?>>
                    <?= htmlspecialchars($loai['TenLoai']) ?>
                </label><br>
            <?php endforeach; ?>
        </div>

        <!-- Lọc theo khoảng giá -->
        <label class="muted mt-2 d-block">Khoảng giá</label>
        <div class="d-flex gap-2 mt-1">
            <input type="number" name="min_price" placeholder="Min" class="form-control form-control-sm"
                   value="<?= htmlspecialchars($minPrice) ?>">
            <input type="number" name="max_price" placeholder="Max" class="form-control form-control-sm"
                   value="<?= htmlspecialchars($maxPrice) ?>">
        </div>

        <!-- Nút Lọc và Thoát -->
        <div class="d-flex justify-content-between align-items-center mt-2">
            <button type="submit" class="btn btn-primary btn-sm">Lọc</button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="location.href='product.php'">Thoát</button>
        </div>
    </form>
</div>
