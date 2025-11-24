<?php
require_once("connect.php");

// --- Lấy biến từ GET ---
$filterTypes = $_GET['type'] ?? [];
$minPrice = $_GET['min_price'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';
$sort = $_GET['sort'] ?? '';
$searchQuery = trim($_GET['q'] ?? '');

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

// --- Lấy danh sách loại sofa ---
$loaiResult = $conn->query("SELECT MaLoai, TenLoai FROM LoaiSofa");
$loaiArr = [];
while($row = $loaiResult->fetch_assoc()){
    $loaiArr[] = $row;
}

// --- Build điều kiện WHERE ---
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

if ($searchQuery !== '') {
    $safeSearch = $conn->real_escape_string($searchQuery);
    $where .= " AND TenSofa LIKE '%$safeSearch%'";
}

// --- Sắp xếp ---
$orderBy = "ORDER BY MaSofa DESC";
if ($sort === 'asc') $orderBy = "ORDER BY GiaBan ASC";
elseif ($sort === 'desc') $orderBy = "ORDER BY GiaBan DESC";

// --- Lấy tổng số sản phẩm để tính phân trang ---
$totalSql = "SELECT COUNT(*) as total FROM Sofa $where";
$totalResult = $conn->query($totalSql);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $limit);

// --- Lấy sản phẩm theo trang ---
$sql = "SELECT * FROM Sofa $where $orderBy LIMIT $offset, $limit";
$result = $conn->query($sql);

$products = [];
while ($row = $result->fetch_assoc()){
    $products[] = $row;
}
?>
