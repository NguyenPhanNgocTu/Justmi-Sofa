<?php
    // Lấy danh sách loại sofa
    $sql = "
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
    $categories = $conn->query($sql);
?>

<!-- Danh mục Sofa -->
<div class="categories mb-3">
    <h4 class="text-center mb-3">Danh mục Sofa</h4>
    <ul class="list-unstyled">
        <?php while ($cate = mysqli_fetch_assoc($categories)): ?>
            <?php
                // Nếu loại chưa có ảnh, hiển thị ảnh mặc định
                $hinh = !empty($cate['HinhAnh']) ? $cate['HinhAnh'] : 'default.jpg';
            ?>
            <li class="mb-3 text-center">
                <a href="product.php?type[]=<?= urlencode($cate['MaLoai']) ?>" 
                   class="d-flex flex-column align-items-center text-decoration-none text-dark">
                   
                    <img src="images/<?= htmlspecialchars($hinh) ?>" 
                         alt="<?= htmlspecialchars($cate['TenLoai']) ?>" 
                         style="width:90px; height:90px; object-fit:cover; border-radius:8px; border:1px solid #ddd;">
                    <span class="mt-2 fw-semibold"><?= htmlspecialchars($cate['TenLoai']) ?></span>
                </a>
            </li>
        <?php endwhile; ?>
    </ul>
</div>