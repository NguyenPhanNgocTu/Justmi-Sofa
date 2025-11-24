<?php
require_once("process/product_process.php"); // file xử lý dữ liệu, chứa kết nối DB

// Truy vấn 5 sản phẩm mới nhất
$sql = "SELECT * FROM Sofa ORDER BY NgayTao DESC LIMIT 5";
$result = mysqli_query($conn, $sql);
?>
<h2>Sản phẩm mới nhất</h2>
<div class="grid">
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
<script>
$(document).on("click", ".add-to-cart", function(){
    console.log("CLICK"); // kiểm tra click 1 lần
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