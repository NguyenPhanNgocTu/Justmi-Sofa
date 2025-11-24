<?php
  session_start();
  require("connect.php");
  error_reporting(E_ALL);
  ini_set('display_errors', 1);

  // Tìm kiếm sản phẩm
  if (isset($_GET['q']) && trim($_GET['q']) !== '') {
    $keyword = urlencode(trim($_GET['q']));
    header("Location: index.php?q=$keyword");
    exit;
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = isset($_POST['dk']) ? 'dk' : (isset($_POST['dn']) ? 'dn' : '');
    $username = trim($_POST['name']);
    $password = trim($_POST['pw']);

    if ($action == 'dn') {
      // ---- ĐĂNG NHẬP ----
      $sql = "SELECT * FROM KhachHang WHERE TenKH = ? AND MatKhau = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("ss", $username, $password);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['MaKH'] = $row['MaKH'];
        $_SESSION['TenKH'] = $row['TenKH'];
        header("Location: index.php");
        exit;
      } else {
        echo "<div class='msg error'>Sai tên đăng nhập hoặc mật khẩu.</div>";
      }
      $stmt->close();
    } elseif ($action == 'dk') {
      // ---- ĐĂNG KÝ ----
      $tenkh = trim($_POST['name']);
      $gioitinh = trim($_POST['gioitinh']);
      $diachi = trim($_POST['diachi']);
      $sdt = trim($_POST['sdt']);
      $email = trim($_POST['email']);
      $matkhau = trim($_POST['pw']);
      $confirm = trim($_POST['confirm']);

      if ($matkhau != $confirm) {
        echo "<div class='msg error'>Mật khẩu xác nhận không trùng khớp.</div>";
      } else {
        $check = $conn->prepare("SELECT * FROM KhachHang WHERE Email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
          echo "<div class='msg error'>Email đã tồn tại, vui lòng chọn email khác.</div>";
        } else {
          // Tạo mã KH tự động tăng
          $res = $conn->query("SELECT MaKH FROM KhachHang ORDER BY MaKH DESC LIMIT 1");
          if ($res && $row = $res->fetch_assoc()) {
              $num = (int)substr($row['MaKH'], 2);
              $num++;
              $makh = "KH" . str_pad($num, 3, '0', STR_PAD_LEFT);
          } else {
              $makh = "KH001";
          }

          $insert = $conn->prepare("INSERT INTO KhachHang (MaKH, TenKH, GioiTinh, DiaChi, DienThoai, Email, MatKhau) VALUES (?, ?, ?, ?, ?, ?, ?)");
          $insert->bind_param("sssssss", $makh, $tenkh, $gioitinh, $diachi, $sdt, $email, $matkhau);

          if ($insert->execute()) {
            echo "<div class='msg success'>Đăng ký thành công! Hãy đăng nhập ngay.</div>";
          } else {
            echo "<div class='msg error'>Lỗi khi đăng ký: " . $insert->error . "</div>";
          }
          $insert->close();
        }
        $check->close();
      }
    }
  }
?>
<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8">
    <title>Đăng nhập / Đăng ký Khách Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
  </head>
  <body class="login-page">
    <!-- HEADER -->
    <header class="topbar">
      <?php include("layout/header.php"); ?>
    </header>

    <!-- FORM ĐĂNG NHẬP / ĐĂNG KÝ -->
    <div class="container mt-4">
      <form action="" method="post" class="mx-auto" style="max-width:500px;">
        <table class='login-table w-100'>
          <thead>
            <tr>
              <th colspan="2" class="text-center"><h3 id="formTitle">Đăng nhập / Đăng ký</h3></th>
            </tr>
          </thead>

          <tr>
            <td><label for="name">Tên đăng nhập:</label></td>
            <td><input type="text" name="name" id="name" required class="form-control"></td>
          </tr>

          <tr>
            <td><label for="pw">Mật khẩu:</label></td>
            <td><input type="password" name="pw" id="pw" required class="form-control"></td>
          </tr>

          <!-- Các trường cho phần Đăng ký -->
          <tbody id="registerFields" style="display:none;">
            <tr>
              <td><label for="confirm">Xác nhận mật khẩu:</label></td>
              <td><input type="password" name="confirm" id="confirm" class="form-control"></td>
            </tr>

            <tr>
              <td><label for="gioitinh">Giới tính:</label></td>
              <td>
                <select name="gioitinh" id="gioitinh" class="form-select">
                  <option value="Nam">Nam</option>
                  <option value="Nữ">Nữ</option>
                </select>
              </td>
            </tr>

            <tr>
              <td><label for="diachi">Địa chỉ:</label></td>
              <td><input type="text" name="diachi" id="diachi" class="form-control"></td>
            </tr>

            <tr>
              <td><label for="sdt">Điện thoại:</label></td>
              <td><input type="text" name="sdt" id="sdt" class="form-control"></td>
            </tr>

            <tr>
              <td><label for="email">Email:</label></td>
              <td><input type="email" name="email" id="email" class="form-control"></td>
            </tr>
          </tbody>

          <tr>
            <td colspan="2" class="text-center">
              <div class="button-group mt-3">
                <input type="submit" name="dn" value="Đăng nhập" id="loginBtn" class="btn btn-primary">
                <input type="submit" name="dk" value="Đăng ký" id="registerBtn" class="btn btn-success" style="display:none;">
              </div>
              <div class="toggle-text mt-2">
                <span id="toggleBtn">Chưa có tài khoản? <a href="#" id="toggleLink">Đăng ký</a></span>
              </div>
            </td>
          </tr>
        </table>
      </form>
    </div>

    <script>
      const toggleLink = document.getElementById('toggleLink');
      const formTitle = document.getElementById('formTitle');
      const registerFields = document.getElementById('registerFields');
      const loginBtn = document.getElementById('loginBtn');
      const registerBtn = document.getElementById('registerBtn');
      const toggleBtn = document.getElementById('toggleBtn');

      let isRegister = false;

      toggleLink.addEventListener('click', (e) => {
        e.preventDefault();
        isRegister = !isRegister;
        if (isRegister) {
          formTitle.textContent = 'Đăng ký tài khoản mới';
          registerFields.style.display = 'table-row-group';
          loginBtn.style.display = 'none';
          registerBtn.style.display = 'inline-block';
          toggleLink.textContent = 'Đăng nhập';
          toggleBtn.firstChild.textContent = 'Đã có tài khoản? ';
        } else {
          formTitle.textContent = 'Đăng nhập';
          registerFields.style.display = 'none';
          loginBtn.style.display = 'inline-block';
          registerBtn.style.display = 'none';
          toggleLink.textContent = 'Đăng ký';
          toggleBtn.firstChild.textContent = 'Chưa có tài khoản? ';
        }
      });
    </script>

      <?php include("layout/footer.php");
      ?>
  </body>
</html>
