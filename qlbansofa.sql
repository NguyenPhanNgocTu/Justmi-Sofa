-- tao csdl
CREATE DATABASE IF NOT EXISTS QL_BAN_SOFA CHARACTER SET utf8 COLLATE utf8_general_ci;
USE QL_BAN_SOFA;

-- Bảng loại Sofa
CREATE TABLE LoaiSofa (
	MaLoai CHAR(5) PRIMARY KEY,
	TenLoai NVARCHAR(100),
	MoTa NVARCHAR(300)
);

-- Bảng hãng Sofa
CREATE TABLE HangSofa (
	MaHang CHAR(5) PRIMARY KEY,
	TenHang NVARCHAR(100),
	DiaChi NVARCHAR(300),
	DienThoai VARCHAR(15),
	Email NVARCHAR(100)
);


-- Bảng Sofa
CREATE TABLE Sofa (
	MaSofa CHAR(5) PRIMARY KEY,
	TenSofa NVARCHAR(100),
	MaHang CHAR(5),
	MaLoai CHAR(5),
	GiaBan DECIMAL(12,2),
	DonViTinh NVARCHAR(50),
	SoLuongTon INT,
	HinhAnh VARCHAR(255),
	MoTa NVARCHAR(700),
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP(),
	FOREIGN KEY (MaHang) REFERENCES HangSofa(MaHang),
	FOREIGN KEY (MaLoai) REFERENCES LoaiSofa(MaLoai)
);

-- Bảng kích thước sofa
CREATE TABLE KichThuoc (
    MaKichThuoc CHAR(5) PRIMARY KEY,
    MaSofa CHAR(5),
    ChieuDai DECIMAL(6,2),   -- cm hoặc m, tùy bạn
    ChieuRong DECIMAL(6,2),
    ChieuCao DECIMAL(6,2),
    DonVi NVARCHAR(10) DEFAULT 'cm',
    FOREIGN KEY (MaSofa) REFERENCES Sofa(MaSofa)
);

-- Bảng màu sắc
CREATE TABLE MauSac (
    MaMau CHAR(5) PRIMARY KEY,
    TenMau NVARCHAR(50)
);

-- Bảng gán màu cho sofa
CREATE TABLE Sofa_MauSac (
    MaSofa CHAR(5),
    MaMau CHAR(5),
    PRIMARY KEY (MaSofa, MaMau),
    FOREIGN KEY (MaSofa) REFERENCES Sofa(MaSofa),
    FOREIGN KEY (MaMau) REFERENCES MauSac(MaMau)
);

-- Bảng khachhang
CREATE TABLE KhachHang (
    MaKH CHAR(5) PRIMARY KEY,
    HoKH NVARCHAR(20),
    TenKH NVARCHAR(50),
    GioiTinh NVARCHAR(5),
    DiaChi NVARCHAR(300),
    DienThoai VARCHAR(15),
    Email VARCHAR(100),
    MatKhau VARCHAR(20),
    NgayDangKy DATETIME DEFAULT CURRENT_TIMESTAMP(),
    role VARCHAR(20) DEFAULT 'customer',
    CONSTRAINT CHK_Role CHECK (role IN ('admin', 'customer'))
);

-- Bảng hoadon
CREATE TABLE HoaDon (
	MaHD CHAR(5) PRIMARY KEY,
	NgayLap DATETIME,
	MaKH CHAR(5),
	TongTien DECIMAL(12,2),
	FOREIGN KEY (MaKH) REFERENCES KhachHang(MaKH)
);

-- Bảng cthd
CREATE TABLE CTHD (
	MaHD CHAR(5),
	MaSofa CHAR(5),
	MaMau CHAR(5),
	MaKichThuoc CHAR(5),
	SoLuong INT,
	DonGia DECIMAL(12,2),
	ThanhTien DECIMAL(12,2),
	PRIMARY KEY (MaHD, MaSofa),
	FOREIGN KEY (MaHD) REFERENCES HoaDon(MaHD),
	FOREIGN KEY (MaSofa) REFERENCES Sofa(MaSofa)
);

-- Dl loai sofa
INSERT INTO LoaiSofa VALUES
('LS001', N'Sofa Bọc', N'Sofa bọc các loại chất liệu cao cấp, thoáng khí'),
('LS002', N'Sofa Góc', N'Sofa dạng chữ L hiện đại, thoải mái'),
('LS003', N'Sofa Giường', N'Sofa có thể mở ra thành giường'),
('LS004', N'Sofa Đơn', N'Sofa một chổ ngồi nhỏ gọn'),
('LS005', N'Sofa Văn Phòng', N'Dùng trong văn phòng, tiếp khách'),
('LS006', N'Sofa Ngoài Trời', N'Sofa chống nước, dùng sân vườn');

-- Dl hãng sofa
INSERT INTO HangSofa VALUES
('HS001', N'Zero Furniture', N'123 Nguyễn Văn Cừ, Q.5, TP.HCM', '0909123456', 'info@zerofurniture.vn'),
('HS002', N'Kenli', N'45 Lê Lợi, Q.1, TP.HCM', '0909345678', 'contact@kenli.vn'),
('HS003', N'IDesign', N'27 Trần Hưng Đạo, Q.3, TP.HCM', '0909888777', 'hello@idesign.vn'),
('HS004', N'Nội Thất Hòa Phát', N'200 Cộng Hòa, Tân Bình, TP.HCM', '0909999991', 'sales@hoaphat.vn'),
('HS005', N'Nội Thất Xinh', N'75 Hai Bà Trưng, Hà Nội', '0987123123', 'contact@noithatxinh.vn'),
('HS006', N'Nội Thất Nhà Xinh', N'88 Lý Thường Kiệt, TP.HCM', '0908112233', 'support@nhaxinh.vn'),
('HS007', N'Minh Khôi Furniture', N'12 Nguyễn Oanh, Gò Vấp', '0911223344', 'info@minhkhoi.vn'),
('HS008', N'FurniHome', N'456 Lạc Long Quân, Tân Bình', '0977888999', 'furni@furnihome.vn'),
('HS009', N'WoodLand', N'21 Cách Mạng Tháng 8, Q.10', '0908333444', 'wood@woodland.vn'),
('HS010', N'Mộc Decor', N'92 Trường Chinh, Đà Nẵng', '0933445566', 'mocdecor@gmail.com'),
('HS011', N'LuxHome', N'16 Hoàng Văn Thụ, Hà Nội', '0911456789', 'lux@luxhome.vn'),
('HS012', N'FurnitureOne', N'10 Pasteur, Q.1, TP.HCM', '0904567890', 'sale@furnitureone.vn'),
('HS013', N'BellaHome', N'17 Lý Thường Kiệt, TP.HCM', '0909789123', 'hello@bellahome.vn');

-- Dl sp sofa
INSERT INTO Sofa VALUES
('SF001', N'Sofa Vải Hàn Quốc', 'HS001', 'LS001', 25000000, N'Bộ', 10, 'sofa_boc_vai.jpg', N'Sofa vải mềm mại, dễ vệ sinh, nhập khẩu Hàn Quốc', NOW()),
('SF002', N'Sofa Góc L Cao Cấp', 'HS002', 'LS002', 18000000, N'Bộ', 15, 'sofa_chu_L.jpg', N'Sofa chữ L sang trọng cho phòng khách', NOW()),
('SF003', N'Sofa Giường Đa Năng', 'HS003', 'LS003', 40000000, N'Bộ', 8, 'sofa_giuong.jpg', N'Sofa gấp gọn, mở ra trở thành giường tiện lợi', NOW()),
('SF004', N'Sofa Đơn Gọn Gàng', 'HS004', 'LS004', 10000000, N'Cái', 5, 'sofa_don.jpg', N'Sofa một chỗ ngồi nhỏ gọn, phù hợp để đọc sách, học tập', NOW()),
('SF005', N'Sofa Văn Phòng Sang Trọng', 'HS005', 'LS005', 30000000, N'Bộ', 12, 'sofa_van_phong.jpg', N'Sofa văn phòng sang trọng, tinh tế, giúp căn phòng trở nên cao cấp', NOW()),
('SF006', N'Sofa Ngoài Trời', 'HS006', 'LS006', 75000000, N'Bộ', 20, 'sofa_ngoai_troi.jpg', N'Sofa chống nước, dùng sân vườn', NOW()),
('SF007', N'Sofa Băng', 'HS007', 'LS002', 35000000, N'Bộ', 13, 'sofa_bang.jpg', N'Sofa nhung mềm mịn, màu sắc sang trọng, rộng rãi, thoáng khí', NOW()),
('SF008', N'Sofa Da', 'HS008', 'LS001', 16000000, N'Bộ', 9, 'sofa_da.jpg', N'Sofa da sang trọng, đẳng cấp châu Âu', NOW()),
('SF009', N'Sofa Linen Breeze', 'HS009', 'LS001', 45000000, N'Bộ', 6, 'sofa_vai_linen.jpg', N'Sofa vải linen thoáng mát, tự nhiên, êm ái', NOW()),
('SF010', N'Sofa Thư Giản', 'HS010', 'LS004', 10000000, N'Bộ', 7, 'sofa_thu_gian.jpg', N'Sofa có thể ngả lưng ra nghỉ ngơi, mềm mịn, thư thái', NOW()),
('SF011', N'Sofa Cổ Điển Royalty', 'HS011', 'LS005', 16500000, N'Bộ', 9, 'sofa_co_dien.jpg', N'Sofa phong cách cổ điển châu Âu', NOW()),
('SF012', N'Sofa Hiện Đại', 'HS012', 'LS005', 30000000, N'Bộ', 11, 'sofa_hien_dai.jpg', N'Sofa phong cách tối giản, hiện đại, tinh tế', NOW()),
('SF013', N'Sofa Góc Chữ U Max', 'HS013', 'LS002', 50000000, N'Bộ', 5, 'sofa_chu_u.jpg', N'Sofa chữ U lớn, cho phòng khách rộng', NOW()),
('SF014', N'Sofa Góc L Cao Cấp', 'HS010', 'LS002', 20000000, N'Bộ', 15, 'sofa_chu_L2.jpg', N'Sofa chữ L sang trọng cho phòng khách', NOW()),
('SF015', N'Sofa Góc L Hiện Đại', 'HS013', 'LS002', 19500000, N'Bộ', 15, 'sofa_chu_L3.jpg', N'Sofa chữ L sang trọng cho phòng khách', NOW()),
('SF016', N'Sofa Giường Đa Năng Êm Ái', 'HS006', 'LS003', 45000000, N'Bộ', 15, 'sofa_giuong_2.jpg', N'Sofa gấp gọn, mở ra trở thành giường tiện lợi dành cho ngôi nhà tương lai', NOW()),
('SF017', N'Sofa Góc Chữ U Sang Trọng', 'HS008', 'LS002', 40000000, N'Bộ', 5, 'sofa_goc_u.jpg', N'Sofa góc U lớn, cho phòng khách rộng rãi, thoáng đãng', NOW()),
('SF018', N'Sofa Đơn Nhỏ Nhắn', 'HS011', 'LS004', 7000000, N'Cái', 5, 'sofa_don_2.jpg', N'Sofa một chỗ ngồi nhỏ gọn, phù hợp để đọc sách, học tập', NOW()),
('SF019', N'Sofa Ngoài Trời', 'HS001', 'LS006', 90000000, N'Bộ', 15, 'sofa_ngoai_troi_2.jpg', N'Sofa chống nước, dùng sân vườn', NOW()),
('SF020', N'Sofa Ngoài Trời', 'HS006', 'LS006', 80500000, N'Bộ', 20, 'sofa_ngoai_troi_3.jpg', N'Sofa chống nước, dùng sân vườn', NOW());

INSERT INTO MauSac VALUES
('MS001', 'Đỏ đô'),
('MS002', 'Xanh lá'),
('MS003', 'Vàng mustard'),
('MS004', 'Đen'),
('MS005', 'Trắng'),
('MS006', 'Xám'),
('MS007', 'Nâu đất'),
('MS008', 'Xanh dương navy'),
('MS009', 'Be'),
('MS010', 'Hồng pastel'),
('MS011', 'Xanh dương pastel'),
('MS012', 'Kem');

INSERT INTO Sofa_MauSac VALUES
('SF001', 'MS001'),
('SF001', 'MS005'),
('SF001', 'MS006'),
('SF001', 'MS012'),
('SF002', 'MS002'),
('SF002', 'MS004'),
('SF002', 'MS006'),
('SF002', 'MS007'),
('SF003', 'MS001'),
('SF003', 'MS002'),
('SF003', 'MS005'),
('SF003', 'MS006'),
('SF004', 'MS001'),
('SF004', 'MS003'),
('SF004', 'MS007'),
('SF004', 'MS009'),
('SF005', 'MS001'),
('SF005', 'MS004'),
('SF005', 'MS006'),
('SF005', 'MS010'),
('SF006', 'MS002'),
('SF006', 'MS004'),
('SF006', 'MS007'),
('SF006', 'MS012'),
('SF007', 'MS001'),
('SF007', 'MS003'),
('SF007', 'MS006'),
('SF007', 'MS009'),
('SF008', 'MS004'),
('SF008', 'MS005'),
('SF008', 'MS006'),
('SF008', 'MS009'),
('SF009', 'MS001'),
('SF009', 'MS005'),
('SF009', 'MS006'),
('SF009', 'MS012'),
('SF010', 'MS002'),
('SF010', 'MS006'),
('SF010', 'MS007'),
('SF010', 'MS009'),
('SF011', 'MS002'),
('SF011', 'MS004'),
('SF011', 'MS005'),
('SF011', 'MS009'),
('SF012', 'MS001'),
('SF012', 'MS004'),
('SF012', 'MS006'),
('SF012', 'MS009'),
('SF013', 'MS001'),
('SF013', 'MS003'),
('SF013', 'MS005'),
('SF013', 'MS012'),
('SF014', 'MS001'),
('SF014', 'MS002'),
('SF014', 'MS007'),
('SF014', 'MS010'),
('SF015', 'MS001'),
('SF015', 'MS004'),
('SF015', 'MS005'),
('SF015', 'MS009'),
('SF016', 'MS001'),
('SF016', 'MS003'),
('SF016', 'MS006'),
('SF016', 'MS010'),
('SF017', 'MS002'),
('SF017', 'MS005'),
('SF017', 'MS006'),
('SF017', 'MS009'),
('SF018', 'MS001'),
('SF018', 'MS005'),
('SF018', 'MS007'),
('SF018', 'MS011'),
('SF019', 'MS001'),
('SF019', 'MS004'),
('SF019', 'MS006'),
('SF019', 'MS009'),
('SF020', 'MS001'),
('SF020', 'MS002'),
('SF020', 'MS005'),
('SF020', 'MS009');

INSERT INTO KichThuoc VALUES
('KT001','SF001',200,80,90,'cm'),
('KT002','SF001',220,85,95,'cm'),
('KT003','SF002',250,150,95,'cm'),
('KT004','SF002',270,160,100,'cm'),
('KT005','SF003',200,90,80,'cm'),
('KT006','SF003',220,100,85,'cm'),
('KT007','SF004',120,70,80,'cm'),
('KT008','SF004',140,75,85,'cm'),
('KT009','SF005',220,100,90,'cm'),
('KT010','SF005',240,110,95,'cm'),
('KT011','SF006',250,120,100,'cm'),
('KT012','SF006',270,130,105,'cm'),
('KT013','SF007',200,90,85,'cm'),
('KT014','SF007',220,95,90,'cm'),
('KT015','SF008',180,80,85,'cm'),
('KT016','SF008',200,85,90,'cm'),
('KT017','SF009',210,90,88,'cm'),
('KT018','SF009',230,95,92,'cm'),
('KT019','SF010',180,80,85,'cm'),
('KT020','SF010',200,85,90,'cm'),
('KT021','SF011',210,100,95,'cm'),
('KT022','SF011',230,105,100,'cm'),
('KT023','SF012',220,100,95,'cm'),
('KT024','SF012',240,105,100,'cm'),
('KT025','SF013',300,200,100,'cm'),
('KT026','SF013',320,220,105,'cm'),
('KT027','SF014',250,150,95,'cm'),
('KT028','SF014',270,160,100,'cm'),
('KT029','SF015',250,150,95,'cm'),
('KT030','SF015',270,160,100,'cm'),
('KT031','SF016',200,90,80,'cm'),
('KT032','SF016',220,100,85,'cm'),
('KT033','SF017',300,200,100,'cm'),
('KT034','SF017',320,220,105,'cm'),
('KT035','SF018',120,70,80,'cm'),
('KT036','SF018',140,75,85,'cm'),
('KT037','SF019',250,120,100,'cm'),
('KT038','SF019',270,130,105,'cm'),
('KT039','SF020',250,120,100,'cm'),
('KT040','SF020',270,130,105,'cm');


-- -- Dl khachhang
-- INSERT INTO KhachHang VALUES
-- -- ('KH001', N'Lê', N'Thị Kim Ngân', N'Nữ', N'212 Nguyễn Hồng Sơn, Phú Yên', '0915034355', 'ngan@gmail.com', '123456', NOW(), 'admin'),
-- -- ('KH002', N'Nguyễn', N'Phan Ngọc Tú', N'Nữ', N'15 Lê Lợi, Diên Khánh, Khánh Hòa', '0916253242', 'ngoctu@gmail.com', '123456', NOW(), 'admin'),
-- -- ('KH003', N'Huỳnh', N'Ngọc Trí', N'Nam', N'121 Mai Xuân Thưởng, Khánh Hòa', '0909876543', 'ngoctri@gmail.com', '123456', NOW(), 'admin'),
-- -- ('KH004', N'Nguyễn', N'Tâm Như', N'Nữ', N'220 Phan Đình Phùng, Phú Yên', '0385261423', 'tamnhu@gmail.com', '123456', NOW(), 'customer'),
-- -- ('KH005', N'Võ', N'Thị Tuyết', N'Nữ', N'210 Nguyễn Hồng Sơn, TP.HCM', '0903344556', 'tuyet@gmail.com', '123456', NOW(), 'customer');

-- -- Dl hd
-- INSERT INTO HoaDon VALUES
-- -- ('HD001', '2025-10-01', 'KH001', 7000000),
-- -- ('HD002', '2025-10-02', 'KH002', 16000000),
-- -- ('HD003', '2025-10-03', 'KH003', 36000000),
-- -- ('HD004', '2025-10-04', 'KH004', 10000000),
-- -- ('HD005', '2025-10-05', 'KH005', 90000000);

-- -- Dl cthd
-- INSERT INTO CTHD VALUES
-- -- ('HD001', 'SF004', 1, 7000000, 7000000),
-- -- ('HD002', 'SF008', 1, 16000000, 16000000),
-- -- ('HD003', 'SF002', 2, 18000000, 36000000),
-- -- ('HD004', 'SF010', 1, 10000000, 10000000),
-- -- ('HD005', 'SF009', 2, 45000000, 90000000);
