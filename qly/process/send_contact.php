<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // Giả lập lưu hoặc gửi email
    echo "<script>alert('Cảm ơn $name! Tin nhắn của bạn đã được gửi.'); window.location='contact.php';</script>";
}
?>
    