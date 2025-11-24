<?php
    session_start();
    require("connect.php");

    $keyword = trim($_GET['q'] ?? '');
    $products = [];

    if ($keyword !== '') {
        $stmt = $conn->prepare("SELECT * FROM Sofa WHERE TenSofa LIKE ?");
        $like = '%' . $keyword . '%';
        $stmt->bind_param("s", $like);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        $stmt->close();
    }
    $conn->close();
?>
<?php include("header.php"); ?>
<main class="container mt-4">
    <h2>Kết quả tìm kiếm: <?= htmlspecialchars($keyword) ?></h2>

    <?php if(count($products) > 0): ?>
        <div class="row">
            <?php foreach($products as $p): ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <img src="images/<?= htmlspecialchars($p['HinhAnh']) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['TenSofa']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($p['TenSofa']) ?></h5>
                            <p class="card-text">₫<?= number_format($p['GiaBan'],0,',','.') ?></p>
                            <a href="product_detail.php?id=<?= $p['MaSofa'] ?>" class="btn btn-primary btn-sm">Chi tiết</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Không tìm thấy sản phẩm phù hợp.</p>
    <?php endif; ?>
</main>
<?php include("footer.php"); ?>
