<?php
    require('connect.php');
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

<div class="categories mb-3">
    <h4 class="text-center">Danh mục</h4>
    <ul>
        <?php while($cate = $categories->fetch_assoc()): ?>
            <li>
                <a href="product.php?maLoai[]=<?= urlencode($cate['MaLoai']) ?>" class="d-flex flex-column align-items-center text-decoration-none text-dark">
                    <img src="images/<?= htmlspecialchars($cate['HinhAnh']) ?>" 
                         alt="<?= htmlspecialchars($cate['TenLoai']) ?>" 
                         style="width:80px; height:80px; object-fit:cover; border-radius:6px;">
                    <span class="mt-1"><?= htmlspecialchars($cate['TenLoai']) ?></span>
                </a>
            </li>
        <?php endwhile; ?>
    </ul>
</div>