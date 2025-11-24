<?php
    $page_title = "Justmi Sofa";
    // --- Gọi file xử lý dữ liệu ---
    require_once("process/product_process.php");

    // --- Include header ---
    if (!isset($noHeader) || !$noHeader) {
        include("includes/header.html");
    }

?>

<main class="mx-5">
    <div class="d-flex gap-3">
        <!-- Sidebar filter -->
        <aside class="sidebar" style="flex:1; max-width:400px;">
            <?php include("categories.php"); ?>
            <?php include("process/filters.php"); ?>
        </aside>

        <!-- Container sản phẩm -->
        <div style="flex:3;">
            <!-- Thanh sắp xếp và số lượng -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="muted"> <?= count($products) ?> sản phẩm</div>
                <div>
                    <form method="get" id="sortForm">
                        <!-- hidden inputs giữ filter -->
                        <?php 
                        foreach ($_GET as $k => $v) {
                            if ($k !== 'sort') {
                                if (is_array($v)) {
                                    foreach ($v as $item) {
                                        echo "<input type='hidden' name='{$k}[]' value='" . htmlspecialchars($item) . "'>";
                                    }
                                } else {
                                    echo "<input type='hidden' name='{$k}' value='" . htmlspecialchars($v) . "'>";
                                }
                            }
                        }
                        ?>
                        <select name="sort" onchange="this.form.submit()" class="form-select form-select-sm">
                            <option value="">Phổ biến</option>
                            <option value="asc" <?= $sort==='asc'?'selected':'' ?>>Giá: Thấp → Cao</option>
                            <option value="desc" <?= $sort==='desc'?'selected':'' ?>>Giá: Cao → Thấp</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Grid sản phẩm -->
            <div class="grid" id="productGrid">
                <?php if(count($products) > 0): ?>
                    <?php foreach ($products as $p): ?>
                        <article class="card">
                            <div class="img">
                                <img src="images/<?= htmlspecialchars($p['HinhAnh']) ?>" 
                                    class="d-block w-100" 
                                    alt="<?= htmlspecialchars($p['TenSofa']) ?>" 
                                    style="height:160px; object-fit:cover;">
                            </div>
                            <h3><?= htmlspecialchars($p["TenSofa"]) ?></h3>
                            <div class="muted">Mã: <?= htmlspecialchars($p["MaSofa"]) ?></div>
                            <div class="muted">Loại: <?= htmlspecialchars($p["MaLoai"]) ?></div>
                            <div class="price">₫<?= number_format($p["GiaBan"],0,',','.') ?></div>
                            <div class="actions d-flex gap-2 mt-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm" 
                                        onclick="window.location.href='product_detail.php?id=<?= $p['MaSofa'] ?>'">
                                    Chi tiết
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm add-to-cart" 
                                        data-id="<?= htmlspecialchars($p['MaSofa']) ?>">
                                    Thêm vào giỏ
                                </button>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Không có sản phẩm phù hợp.</p>
                <?php endif; ?>
            </div>
            <!-- Phân trang -->
            <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mt-3">
                    <?php for($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page'=>$i])) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
$(document).on("click", ".add-to-cart", function(){
    var id = $(this).data("id");
    $.post("add_to_cart.php", {id: id}, function(res){
        try {
            var data = JSON.parse(res);
            if(data.status == 1){
                var alertHtml = `<div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                                    ${data.msg}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                 </div>`;
                $("main").prepend(alertHtml);
                $("#cart-count").text(data.totalItems);
            } else {
                alert(data.msg);
            }
        } catch(e) {
            console.error("Response không hợp lệ:", res);
        }
    });
});
</script>

<?php include("includes/footer.html"); ?>
