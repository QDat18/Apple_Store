-- sample_products_data.sql

-- 2. Giá trị thuộc tính
INSERT INTO variant_attribute_values (id, attribute_id, value, hex_code) VALUES
(1, 1, 'Natural Titanium', '#b5b5b5'),
(2, 1, 'Black', '#222'),
(3, 1, 'Blue', '#3b5998'),
(4, 1, 'Pink', '#f8b6c1'),
(5, 1, 'Green', '#4caf50'),
(6, 1, 'Silver', '#e0e0e0'),
(7, 2, '128GB', NULL),
(8, 2, '256GB', NULL),
(9, 2, '512GB', NULL),
(10, 2, '1TB', NULL);

-- 3. Sản phẩm
INSERT INTO products (id, product_name, price, description, product_image, category_id, status) VALUES
(1, 'iPhone 16 Pro Max', 34990000, 'iPhone 16 Pro Max với chip A18 Bionic, camera cải tiến và pin vượt trội.', 'iphone16promax.png', 1, 1),
(2, 'iPad Pro M4', 24990000, 'iPad Pro M4 với chip M4 siêu mạnh mẽ, màn hình Liquid Retina XDR.', 'ipadpro_m4.png', 3, 1),
(3, 'MacBook Air M3', 27990000, 'MacBook Air M3 mang lại hiệu năng vượt trội.', 'macbookairm3.png', 2, 1),
(4, 'Apple Watch Series 9', 11990000, 'Apple Watch Series 9 sở hữu chip S9 SiP mới, màn hình sáng hơn.', 'applewatch_s9.png', 4, 1),
(5, 'AirPods Pro 2', 5990000, 'AirPods Pro 2 mang đến chất lượng âm thanh vượt trội.', 'airpodspro2.png', 5, 1),
(6, 'Adapter sạc USB-C', 690000, 'Adapter sạc nhanh USB-C chính hãng Apple.', 'adapter_usbc.png', 6, 1);

-- 4. Biến thể sản phẩm
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES
(1, 1, 'IPH-256GB-NATURAL', 34990000, 10, 'iphone16promax_natural.png', 1),
(2, 1, 'IPH-256GB-BLACK', 34990000, 8, 'iphone16promax_black.png', 1),
(3, 1, 'IPH-512GB-BLUE', 37990000, 5, 'iphone16promax_blue.png', 1),
(4, 2, 'IPA-128GB-SILVER', 24990000, 7, 'ipadpro_m4_silver.png', 1),
(5, 2, 'IPA-256GB-BLACK', 27990000, 4, 'ipadpro_m4_black.png', 1),
(6, 3, 'MAC-256GB-SILVER', 27990000, 6, 'macbookairm3_silver.png', 1),
(7, 3, 'MAC-512GB-BLACK', 31990000, 3, 'macbookairm3_black.png', 1),
(8, 4, 'WAT-STD-PINK', 11990000, 10, 'applewatch_s9_pink.png', 1),
(9, 4, 'WAT-STD-BLACK', 11990000, 8, 'applewatch_s9_black.png', 1),
(10, 5, 'AIR-STD-WHITE', 5990000, 15, 'airpodspro2_white.png', 1),
(11, 6, 'ADA-STD-WHITE', 690000, 30, 'adapter_usbc_white.png', 1);

-- 5. Liên kết biến thể với giá trị thuộc tính
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES
(1, 1), (1, 8),
(2, 2), (2, 8),
(3, 3), (3, 9),
(4, 6), (4, 7),
(5, 2), (5, 8),
(6, 6), (6, 8),
(7, 2), (7, 9),
(8, 4),
(9, 2),
(10, 6),
(11, 6);