<?php # Script 3.4 - index.php
$page_title = 'Welcome to this Site!';
include ('includes/header.html');
?>
<!-- <div class="mt-3">
    <form method="get" action="product.php" class="d-flex">
        <input type="text" name="q" placeholder="Tìm kiếm sản phẩm" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        <button type="submit" class="btn btn-primary btn-sm ms-1">Tìm</button>
    </form>
</div> -->
    <section class="w-100 text-center my-3">
        <div id="slide" class="carousel slide " data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-indicators ">
                <button type="button" data-bs-target="#slide" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#slide" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#slide" data-bs-slide-to="2"></button>
            </div>
            <div class="carousel-inner">       
                <div class="carousel-item active">
                    <div class="d-flex justify-content-center align-items-center ">
                        <img src="images/sofa_bang.jpg" class="d-block" alt="slide1">
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="d-flex justify-content-center align-items-center ">
                        <img src="images/sofa_boc_vai.jpg" class="d-block " alt="slide2">
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="d-flex justify-content-center align-items-center ">
                        <img src="images/sofa_chu_L.jpg" class="d-block " alt="slide3">
                    </div>
                </div>
            </div>
        </div>
    </section>


<main class="container my-4">
    <div style="flex:3">
        <?php include("best.php");?>
    </div>

    <section class="layout d-flex gap-3">
        <aside class="sidebar" style="flex:1; min-width:200px;">
            <?php include("module/categories.php"); ?>
        </aside>
    </section>
    
</main>
<!-- <h2>Subheader</h2>
<p>This is where you'll put the main page content. This content will differ for each page.</p>
<p>This is where you'll put the main page content. This content will differ for each page.</p>
<p>This is where you'll put the main page content. This content will differ for each page.</p>
<p>This is where you'll put the main page content. This content will differ for each page.</p> -->
<?php
include ('includes/footer.html');
?>