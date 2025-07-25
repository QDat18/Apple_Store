
INSERT IGNORE INTO variant_attributes (id, name, display_name, type) VALUES (1, 'Color', 'Màu sắc', 'color_picker'), (2, 'Storage', 'Dung lượng', 'text');
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1001, 'iPhone 16 Pro Max', 34990000, 'iPhone 16 Pro Max là chiếc điện thoại mạnh mẽ nhất của Apple, sở hữu màn hình lớn nhất, hiệu năng đỉnh cao với chip A18 Pro và hệ thống camera tiên tiến nhất. Đây là lựa chọn hoàn hảo cho những người dùng chuyên nghiệp và những ai muốn trải nghiệm công nghệ di động tốt nhất.
Màn hình LTPO Super Retina XDR 6.9 inch với công nghệ ProMotion và Always-On Display mang lại không gian hiển thị rộng lớn, mượt mà và sắc nét tuyệt đối. Độ sáng tối đa 2000 nits cùng công nghệ HDR10 và Dolby Vision cho phép bạn thưởng thức nội dung với chất lượng hình ảnh không thể tin được.
Hệ thống camera Pro Max được nâng cấp với camera chính 48MP, camera Ultra Wide 48MP và camera Telephoto 12MP (zoom quang học 5x). Khả năng chụp ảnh thiếu sáng vượt trội, quay video 4K Pro Res lên đến 120fps và tính năng Spatial Video mang đến khả năng sáng tạo không giới hạn.
iPhone 16 Pro Max hỗ trợ Wi-Fi 7 tiên tiến nhất và Bluetooth 5.3, đảm bảo tốc độ kết nối siêu nhanh và ổn định. Viên pin dung lượng lớn nhất trong dòng iPhone cùng công nghệ sạc nhanh và sạc MagSafe cung cấp thời lượng sử dụng bền bỉ cho cả ngày dài làm việc và giải trí.
Thiết bị còn được trang bị nút Tác vụ (Action Button) tùy chỉnh, Face ID, và các tính năng kết nối vệ tinh nâng cao, biến iPhone 16 Pro Max thành một công cụ không thể thiếu cho mọi nhu cầu.', 'assets/products/iphone/iphone16prm_titanden.png', 1, 'IPHONE16PROMAX', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (1001, 2, '256GB');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (1002, 2, '512GB');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (1003, 2, '1TB');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2001, 1, 'Titan Tự nhiên');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2002, 1, 'Titan Đen');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2003, 1, 'Titan Trắng');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2004, 1, 'Titan Sa mạc');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10001, 1001, '1001-256GB-TITANTNHIN', 34990000, 20, 'assets/products/iphone/iphone16prm_titantunhien.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10001, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10001, 2001);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10002, 1001, '1001-256GB-TITANEN', 34990000, 20, 'assets/products/iphone/iphone16prm_titanden.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10002, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10002, 2002);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10003, 1001, '1001-256GB-TITANTRNG', 34990000, 20, 'assets/products/iphone/iphone16prm_titantrang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10003, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10003, 2003);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10004, 1001, '1001-256GB-TITANSAMC', 34990000, 20, 'assets/products/iphone/iphone16prm_titansamac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10004, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10004, 2004);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10005, 1001, '1001-512GB-TITANTNHIN', 38990000, 20, 'assets/products/iphone/iphone16prm_titantunhien.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10005, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10005, 2001);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10006, 1001, '1001-512GB-TITANEN', 38990000, 20, 'assets/products/iphone/iphone16prm_titanden.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10006, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10006, 2002);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10007, 1001, '1001-512GB-TITANTRNG', 38990000, 20, 'assets/products/iphone/iphone16prm_titantrang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10007, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10007, 2003);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10008, 1001, '1001-512GB-TITANSAMC', 38990000, 20, 'assets/products/iphone/iphone16prm_titansamac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10008, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10008, 2004);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10009, 1001, '1001-1TB-TITANTNHIN', 42990000, 20, 'assets/products/iphone/iphone16prm_titantunhien.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10009, 1003);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10009, 2001);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10010, 1001, '1001-1TB-TITANEN', 42990000, 20, 'assets/products/iphone/iphone16prm_titanden.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10010, 1003);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10010, 2002);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10011, 1001, '1001-1TB-TITANTRNG', 42990000, 20, 'assets/products/iphone/iphone16prm_titantrang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10011, 1003);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10011, 2003);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10012, 1001, '1001-1TB-TITANSAMC', 42990000, 20, 'assets/products/iphone/iphone16prm_titansamac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10012, 1003);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10012, 2004);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1002, 'iPhone 16 Pro', 28990000, 'iPhone 16 Pro là đỉnh cao của công nghệ di động, mang đến hiệu năng vượt trội với chip A18 Pro mạnh mẽ và hệ thống camera chuyên nghiệp. Thiết kế sang trọng với khung viền Titanium và màn hình ProMotion là những điểm nhấn ấn tượng của chiếc điện thoại này.
Màn hình LTPO Super Retina XDR 6.3 inch với công nghệ ProMotion và Always-On Display mang lại trải nghiệm hình ảnh mượt mà, sống động và chi tiết. Độ sáng tối đa lên đến 2000 nits đảm bảo hiển thị rõ ràng ngay cả trong môi trường sáng nhất.
Hệ thống camera Pro được nâng cấp đáng kể với camera chính 48MP, camera Ultra Wide 48MP và camera Telephoto 12MP (zoom quang học 5x). Khả năng quay video 4K Pro Res và tính năng Spatial Video cho phép bạn ghi lại những thước phim chất lượng điện ảnh.
iPhone 16 Pro hỗ trợ Wi-Fi 7 và Bluetooth 5.3, mang lại tốc độ kết nối không dây nhanh chóng và ổn định. Dung lượng pin tối ưu cùng công nghệ sạc nhanh giúp bạn luôn sẵn sàng cho mọi tác vụ.
Ngoài ra, thiết bị còn có nút Tác vụ (Action Button) tùy chỉnh, Face ID, và khả năng kết nối vệ tinh để liên lạc trong trường hợp khẩn cấp. iPhone 16 Pro là lựa chọn hoàn hảo cho những người dùng đòi hỏi hiệu năng cao và khả năng chụp ảnh chuyên nghiệp.', 'assets/products/iphone/iphone16pro_titantunhien.png', 1, 'IPHONE16PRO', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (1004, 2, '128GB');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10013, 1002, '1002-128GB-TITANTNHIN', 28990000, 20, 'assets/products/iphone/iphone16pro_titantunhien.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10013, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10013, 2001);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10014, 1002, '1002-128GB-TITANEN', 28990000, 20, 'assets/products/iphone/iphone16pro_titanden.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10014, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10014, 2002);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10015, 1002, '1002-128GB-TITANTRNG', 28990000, 20, 'assets/products/iphone/iphone16pro_titantrang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10015, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10015, 2003);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10016, 1002, '1002-128GB-TITANSAMC', 28990000, 20, 'assets/products/iphone/iphone16pro_titansamac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10016, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10016, 2004);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10017, 1002, '1002-256GB-TITANTNHIN', 31990000, 20, 'assets/products/iphone/iphone16pro_titantunhien.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10017, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10017, 2001);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10018, 1002, '1002-256GB-TITANEN', 31990000, 20, 'assets/products/iphone/iphone16pro_titanden.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10018, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10018, 2002);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10019, 1002, '1002-256GB-TITANTRNG', 31990000, 20, 'assets/products/iphone/iphone16pro_titantrang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10019, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10019, 2003);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10020, 1002, '1002-256GB-TITANSAMC', 31990000, 20, 'assets/products/iphone/iphone16pro_titansamac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10020, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10020, 2004);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10021, 1002, '1002-512GB-TITANTNHIN', 35990000, 20, 'assets/products/iphone/iphone16pro_titantunhien.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10021, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10021, 2001);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10022, 1002, '1002-512GB-TITANEN', 35990000, 20, 'assets/products/iphone/iphone16pro_titanden.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10022, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10022, 2002);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10023, 1002, '1002-512GB-TITANTRNG', 35990000, 20, 'assets/products/iphone/iphone16pro_titantrang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10023, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10023, 2003);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10024, 1002, '1002-512GB-TITANSAMC', 35990000, 20, 'assets/products/iphone/iphone16pro_titansamac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10024, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10024, 2004);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10025, 1002, '1002-1TB-TITANTNHIN', 39990000, 20, 'assets/products/iphone/iphone16pro_titantunhien.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10025, 1003);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10025, 2001);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10026, 1002, '1002-1TB-TITANEN', 39990000, 20, 'assets/products/iphone/iphone16pro_titanden.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10026, 1003);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10026, 2002);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10027, 1002, '1002-1TB-TITANTRNG', 39990000, 20, 'assets/products/iphone/iphone16pro_titantrang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10027, 1003);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10027, 2003);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10028, 1002, '1002-1TB-TITANSAMC', 39990000, 20, 'assets/products/iphone/iphone16pro_titansamac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10028, 1003);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10028, 2004);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1003, 'iPhone 16 Plus', 25990000, 'iPhone 16 Plus mang đến trải nghiệm di động lớn hơn và mạnh mẽ hơn, được trang bị chip A18 tiên tiến của Apple. Với màn hình rộng rãi và thời lượng pin ấn tượng, đây là lựa chọn lý tưởng cho những ai yêu thích giải trí và làm việc trên một thiết bị lớn hơn.
Màn hình Super Retina XDR 6.7 inch với công nghệ Dynamic Island mang lại không gian hiển thị rộng lớn, màu sắc chân thực và độ sáng cao, lý tưởng cho việc xem phim, chơi game và đa nhiệm. Tận hưởng hình ảnh sắc nét và rõ ràng trong mọi điều kiện ánh sáng.
Hệ thống camera kép được nâng cấp với camera chính 48MP và camera Ultra Wide 12MP cho phép bạn chụp ảnh và quay video 4K tuyệt đẹp. Chế độ chụp đêm và các tính năng chụp ảnh chuyên nghiệp giúp bạn ghi lại mọi khoảnh khắc với chất lượng cao nhất.
Khả năng kết nối Wi-Fi 7 và Bluetooth 5.3 đảm bảo tốc độ truyền tải dữ liệu cực nhanh và kết nối ổn định. Viên pin dung lượng lớn cung cấp thời lượng sử dụng cả ngày dài, cùng với công nghệ sạc nhanh tiện lợi.
iPhone 16 Plus cũng tích hợp nút Tác vụ (Action Button) và tính năng Face ID, mang lại sự tiện lợi và bảo mật tối ưu. Đây là một thiết bị đa năng, hoàn hảo cho cả công việc và giải trí.', 'assets/products/iphone/iphone16plus_hong.png', 1, 'IPHONE16PLUS', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2005, 1, 'Đen');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2006, 1, 'Trắng');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2007, 1, 'Hồng');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2008, 1, 'Xanh Lục');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2009, 1, 'Xanh Lam');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10029, 1003, '1003-128GB-EN', 25990000, 20, 'assets/products/iphone/iphone16plus_den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10029, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10029, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10030, 1003, '1003-128GB-TRNG', 25990000, 20, 'assets/products/iphone/iphone16plus_trang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10030, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10030, 2006);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10031, 1003, '1003-128GB-HNG', 25990000, 20, 'assets/products/iphone/iphone16plus_hong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10031, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10031, 2007);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10032, 1003, '1003-128GB-XANHLC', 25990000, 20, 'assets/products/iphone/iphone16plus_xanhmongket.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10032, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10032, 2008);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10033, 1003, '1003-128GB-XANHLAM', 25990000, 20, 'assets/products/iphone/iphone16plus_xanhluuly.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10033, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10033, 2009);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10034, 1003, '1003-256GB-EN', 28990000, 20, 'assets/products/iphone/iphone16plus_den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10034, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10034, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10035, 1003, '1003-256GB-TRNG', 28990000, 20, 'assets/products/iphone/iphone16plus_trang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10035, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10035, 2006);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10036, 1003, '1003-256GB-HNG', 28990000, 20, 'assets/products/iphone/iphone16plus_hong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10036, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10036, 2007);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10037, 1003, '1003-256GB-XANHLC', 28990000, 20, 'assets/products/iphone/iphone16plus_xanhmongket.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10037, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10037, 2008);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10038, 1003, '1003-256GB-XANHLAM', 28990000, 20, 'assets/products/iphone/iphone16plus_xanhluuly.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10038, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10038, 2009);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10039, 1003, '1003-512GB-EN', 32990000, 20, 'assets/products/iphone/iphone16plus_den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10039, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10039, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10040, 1003, '1003-512GB-TRNG', 32990000, 20, 'assets/products/iphone/iphone16plus_trang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10040, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10040, 2006);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10041, 1003, '1003-512GB-HNG', 32990000, 20, 'assets/products/iphone/iphone16plus_hong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10041, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10041, 2007);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10042, 1003, '1003-512GB-XANHLC', 32990000, 20, 'assets/products/iphone/iphone16plus_xanhmongket.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10042, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10042, 2008);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10043, 1003, '1003-512GB-XANHLAM', 32990000, 20, 'assets/products/iphone/iphone16plus_xanhluuly.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10043, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10043, 2009);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1004, 'iPhone 16', 22990000, 'iPhone 16 là phiên bản mới nhất của Apple, tích hợp chip A18 mạnh mẽ, mang đến hiệu năng vượt trội cho mọi tác vụ từ làm việc đến giải trí. Với thiết kế sang trọng và bền bỉ, chiếc điện thoại này hứa hẹn sẽ nâng tầm trải nghiệm di động của bạn.
Màn hình Super Retina XDR 6.1 inch sắc nét với độ phân giải cao và công nghệ Dynamic Island mang lại hình ảnh sống động, màu sắc chân thực. Độ sáng tối đa lên đến 2000 nits giúp hiển thị rõ ràng ngay cả dưới ánh nắng mặt trời gay gắt.
Hệ thống camera kép tiên tiến của iPhone 16 cho phép bạn chụp ảnh và quay video 4K chất lượng cao. Camera chính 48MP và camera Ultra Wide 12MP được cải tiến giúp ghi lại mọi khoảnh khắc với độ chi tiết và màu sắc ấn tượng. Camera trước 12MP hỗ trợ FaceTime và các cuộc gọi video chất lượng cao.
iPhone 16 tương thích với Wi-Fi 7 siêu tốc và Bluetooth 5.3, đảm bảo kết nối ổn định và nhanh chóng. Dung lượng pin lớn cùng công nghệ sạc nhanh giúp bạn thoải mái sử dụng suốt cả ngày dài mà không lo gián đoạn.
Ngoài ra, iPhone 16 còn tích hợp nút Tác vụ (Action Button) tùy chỉnh, cho phép bạn truy cập nhanh các tính năng yêu thích chỉ với một lần nhấn. Tính năng nhận diện khuôn mặt Face ID an toàn và tiện lợi, bảo vệ thông tin cá nhân của bạn.', 'assets/products/iphone/iphone16_trang.png', 1, 'IPHONE16', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10044, 1004, '1004-128GB-EN', 22990000, 20, 'assets/products/iphone/iphone16_den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10044, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10044, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10045, 1004, '1004-128GB-TRNG', 22990000, 20, 'assets/products/iphone/iphone16_trang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10045, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10045, 2006);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10046, 1004, '1004-128GB-HNG', 22990000, 20, 'assets/products/iphone/iphone16_hong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10046, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10046, 2007);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10047, 1004, '1004-128GB-XANHLC', 22990000, 20, 'assets/products/iphone/iphone16_xanhmongket.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10047, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10047, 2008);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10048, 1004, '1004-128GB-XANHLAM', 22990000, 20, 'assets/products/iphone/iphone16_xanhluuly.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10048, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10048, 2009);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10049, 1004, '1004-256GB-EN', 25990000, 20, 'assets/products/iphone/iphone16_den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10049, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10049, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10050, 1004, '1004-256GB-TRNG', 25990000, 20, 'assets/products/iphone/iphone16_trang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10050, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10050, 2006);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10051, 1004, '1004-256GB-HNG', 25990000, 20, 'assets/products/iphone/iphone16_hong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10051, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10051, 2007);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10052, 1004, '1004-256GB-XANHLC', 25990000, 20, 'assets/products/iphone/iphone16_xanhmongket.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10052, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10052, 2008);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10053, 1004, '1004-256GB-XANHLAM', 25990000, 20, 'assets/products/iphone/iphone16_xanhluuly.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10053, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10053, 2009);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10054, 1004, '1004-512GB-EN', 29990000, 20, 'assets/products/iphone/iphone16_den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10054, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10054, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10055, 1004, '1004-512GB-TRNG', 29990000, 20, 'assets/products/iphone/iphone16_trang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10055, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10055, 2006);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10056, 1004, '1004-512GB-HNG', 29990000, 20, 'assets/products/iphone/iphone16_hong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10056, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10056, 2007);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10057, 1004, '1004-512GB-XANHLC', 29990000, 20, 'assets/products/iphone/iphone16_xanhmongket.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10057, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10057, 2008);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10058, 1004, '1004-512GB-XANHLAM', 29990000, 20, 'assets/products/iphone/iphone16_xanhluuly.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10058, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10058, 2009);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1005, 'iPhone 16e', 17990000, 'iPhone 16e là phiên bản \'essential\' của dòng iPhone 16, mang đến trải nghiệm Apple cốt lõi với mức giá phải chăng hơn. Được trang bị chip A17 Bionic (tiếp bước chip của thế hệ trước), thiết bị này vẫn đảm bảo hiệu năng mượt mà cho các tác vụ hàng ngày, từ lướt web, mạng xã hội đến chơi game nhẹ và xem phim.
Màn hình Liquid Retina HD 6.1 inch của iPhone 16e cung cấp màu sắc sống động và độ rõ nét tuyệt vời cho mọi nội dung. Viền màn hình có thể sẽ dày hơn một chút so với các mẫu cao cấp, nhưng vẫn mang lại trải nghiệm xem thoải mái và trực quan.
Hệ thống camera đơn hoặc kép được tối ưu hóa với camera chính 12MP sẽ giúp bạn chụp ảnh và quay video chất lượng tốt trong nhiều điều kiện. Camera trước cũng được cải tiến để có những cuộc gọi video sắc nét và ảnh selfie đẹp.
iPhone 16e hỗ trợ kết nối 5G và Wi-Fi 6, đảm bảo bạn luôn có tốc độ truy cập internet nhanh chóng và đáng tin cậy. Dung lượng pin được cải thiện đáng kể so với các mẫu iPhone SE trước đây, cho phép bạn sử dụng thoải mái trong suốt cả ngày.
Thiết kế của iPhone 16e sẽ vẫn giữ nguyên sự chắc chắn và sang trọng đặc trưng của Apple, với khung nhôm và mặt lưng kính. Máy cũng sẽ tích hợp các tính năng bảo mật quen thuộc như Face ID hoặc Touch ID (nếu sử dụng nút nguồn tích hợp).', 'assets/products/iphone/iphone16e_trang.png', 1, 'IPHONE16E', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10059, 1005, '1005-128GB-TRNG', 17990000, 20, 'assets/products/iphone/iphone16e_trang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10059, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10059, 2006);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10060, 1005, '1005-128GB-EN', 17990000, 20, 'assets/products/iphone/iphone16e_den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10060, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10060, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10061, 1005, '1005-256GB-TRNG', 19990000, 20, 'assets/products/iphone/iphone16e_trang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10061, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10061, 2006);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10062, 1005, '1005-256GB-EN', 19990000, 20, 'assets/products/iphone/iphone16e_den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10062, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10062, 2005);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1006, 'iPhone 15 Pro Max', 31990000, 'iPhone 15 Pro Max là chiếc điện thoại mạnh mẽ và tiên tiến nhất của Apple, sở hữu màn hình lớn nhất, hiệu năng đỉnh cao với chip A17 Pro và hệ thống camera chuyên nghiệp hàng đầu. Đây là lựa chọn tối ưu cho những người dùng yêu cầu khắt khe nhất về hiệu suất và khả năng chụp ảnh.
Màn hình Super Retina XDR 6.7 inch với công nghệ ProMotion và Always-On Display mang lại trải nghiệm hiển thị tuyệt vời, mượt mà và cực kỳ sắc nét. Khung viền Titanium bền bỉ và nhẹ hơn đáng kể, tạo nên một thiết kế cao cấp và thoải mái khi cầm nắm.
Hệ thống camera Pro Max với camera chính 48MP, camera Ultra Wide 12MP và đặc biệt là camera Telephoto 12MP với khả năng zoom quang học 5x, cho phép bạn chụp những bức ảnh và quay video chất lượng đỉnh cao, kể cả từ xa. Chế độ Cinematic Mode, ProRes video và khả năng quay video không gian (Spatial Video) mở ra những khả năng sáng tạo mới.
iPhone 15 Pro Max được trang bị cổng USB-C với tốc độ USB 3, giúp truyền dữ liệu cực nhanh. Khả năng kết nối Wi-Fi 6E và 5G tiên tiến nhất đảm bảo tốc độ internet và độ trễ thấp tối đa cho mọi hoạt động trực tuyến.
Nút Tác vụ (Action Button) mới có thể tùy chỉnh, Face ID bảo mật, và thời lượng pin vượt trội giúp bạn thoải mái sử dụng cả ngày dài mà không cần lo lắng. iPhone 15 Pro Max là biểu tượng của sự kết hợp hoàn hảo giữa công nghệ, thiết kế và trải nghiệm người dùng.', 'assets/products/iphone/iphone15prm_titanden.png', 1, 'IPHONE15PROMAX', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2010, 1, 'Titan Xanh');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10063, 1006, '1006-256GB-TITANTNHIN', 31990000, 20, 'assets/products/iphone/iphone15prm_titantunhien.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10063, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10063, 2001);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10064, 1006, '1006-256GB-TITANEN', 31990000, 20, 'assets/products/iphone/iphone15prm_titanden.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10064, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10064, 2002);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10065, 1006, '1006-256GB-TITANTRNG', 31990000, 20, 'assets/products/iphone/iphone15prm_titantrang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10065, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10065, 2003);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10066, 1006, '1006-256GB-TITANXANH', 31990000, 20, 'assets/products/iphone/iphone15prm_titanxanh.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10066, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10066, 2010);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10067, 1006, '1006-512GB-TITANTNHIN', 36990000, 20, 'assets/products/iphone/iphone15prm_titantunhien.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10067, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10067, 2001);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10068, 1006, '1006-512GB-TITANEN', 36990000, 20, 'assets/products/iphone/iphone15prm_titanden.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10068, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10068, 2002);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10069, 1006, '1006-512GB-TITANTRNG', 36990000, 20, 'assets/products/iphone/iphone15prm_titantrang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10069, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10069, 2003);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10070, 1006, '1006-512GB-TITANXANH', 36990000, 20, 'assets/products/iphone/iphone15prm_titanxanh.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10070, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10070, 2010);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10071, 1006, '1006-1TB-TITANTNHIN', 41990000, 20, 'assets/products/iphone/iphone15prm_titantunhien.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10071, 1003);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10071, 2001);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10072, 1006, '1006-1TB-TITANEN', 41990000, 20, 'assets/products/iphone/iphone15prm_titanden.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10072, 1003);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10072, 2002);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10073, 1006, '1006-1TB-TITANTRNG', 41990000, 20, 'assets/products/iphone/iphone15prm_titantrang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10073, 1003);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10073, 2003);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10074, 1006, '1006-1TB-TITANXANH', 41990000, 20, 'assets/products/iphone/iphone15prm_titanxanh.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10074, 1003);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10074, 2010);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1007, 'iPhone 15 Pro', 25990000, 'iPhone 15 Pro là dòng sản phẩm cao cấp của Apple, được thiết kế cho những người dùng đòi hỏi hiệu năng tối đa và khả năng chụp ảnh chuyên nghiệp. Máy được trang bị chip A17 Pro siêu mạnh, mang đến hiệu năng đồ họa và xử lý chưa từng có trên điện thoại thông minh.
Màn hình Super Retina XDR 6.1 inch với công nghệ ProMotion và Always-On Display mang lại trải nghiệm hiển thị mượt mà, sống động và cực kỳ chi tiết. Khung viền mỏng hơn và chất liệu Titanium cao cấp tạo nên sự sang trọng và bền bỉ.
Hệ thống camera Pro được nâng cấp vượt bậc với camera chính 48MP, camera Ultra Wide 12MP và camera Telephoto 12MP (zoom quang học 3x). Khả năng chụp ảnh thiếu sáng, quay video ProRes và chế độ Cinematic Mode cho phép bạn ghi lại những thước phim chất lượng điện ảnh.
iPhone 15 Pro tích hợp cổng USB-C tốc độ cao (USB 3), cho phép truyền dữ liệu cực nhanh. Khả năng kết nối Wi-Fi 6E và 5G tiên tiến đảm bảo tốc độ internet và độ trễ thấp tối đa.
Nút Tác vụ (Action Button) mới có thể tùy chỉnh giúp truy cập nhanh vào các tính năng yêu thích. Pin được tối ưu hóa cho thời lượng sử dụng cả ngày dài, cùng với công nghệ sạc nhanh và MagSafe tiện lợi. Đây là chiếc iPhone dành cho những người dùng chuyên nghiệp và sáng tạo.', 'assets/products/iphone/iphone15pro_titanden.png', 1, 'IPHONE15PRO', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10075, 1007, '1007-128GB-TITANTNHIN', 25990000, 20, 'assets/products/iphone/iphone15pro_titantunhien.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10075, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10075, 2001);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10076, 1007, '1007-128GB-TITANEN', 25990000, 20, 'assets/products/iphone/iphone15pro_titanden.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10076, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10076, 2002);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10077, 1007, '1007-128GB-TITANTRNG', 25990000, 20, 'assets/products/iphone/iphone15pro_titantrang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10077, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10077, 2003);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10078, 1007, '1007-128GB-TITANXANH', 25990000, 20, 'assets/products/iphone/iphone15pro_titanxanh.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10078, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10078, 2010);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10079, 1007, '1007-256GB-TITANTNHIN', 28990000, 20, 'assets/products/iphone/iphone15pro_titantunhien.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10079, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10079, 2001);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10080, 1007, '1007-256GB-TITANEN', 28990000, 20, 'assets/products/iphone/iphone15pro_titanden.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10080, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10080, 2002);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10081, 1007, '1007-256GB-TITANTRNG', 28990000, 20, 'assets/products/iphone/iphone15pro_titantrang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10081, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10081, 2003);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10082, 1007, '1007-256GB-TITANXANH', 28990000, 20, 'assets/products/iphone/iphone15pro_titanxanh.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10082, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10082, 2010);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10083, 1007, '1007-512GB-TITANTNHIN', 33990000, 20, 'assets/products/iphone/iphone15pro_titantunhien.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10083, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10083, 2001);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10084, 1007, '1007-512GB-TITANEN', 33990000, 20, 'assets/products/iphone/iphone15pro_titanden.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10084, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10084, 2002);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10085, 1007, '1007-512GB-TITANTRNG', 33990000, 20, 'assets/products/iphone/iphone15pro_titantrang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10085, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10085, 2003);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10086, 1007, '1007-512GB-TITANXANH', 33990000, 20, 'assets/products/iphone/iphone15pro_titanxanh.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10086, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10086, 2010);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10087, 1007, '1007-1TB-TITANTNHIN', 38990000, 20, 'assets/products/iphone/iphone15pro_titantunhien.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10087, 1003);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10087, 2001);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10088, 1007, '1007-1TB-TITANEN', 38990000, 20, 'assets/products/iphone/iphone15pro_titanden.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10088, 1003);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10088, 2002);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10089, 1007, '1007-1TB-TITANTRNG', 38990000, 20, 'assets/products/iphone/iphone15pro_titanden.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10089, 1003);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10089, 2003);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10090, 1007, '1007-1TB-TITANXANH', 38990000, 20, 'assets/products/iphone/iphone15pro_titanxanh.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10090, 1003);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10090, 2010);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1008, 'iPhone 15 Plus', 22990000, 'iPhone 15 Plus mang đến trải nghiệm màn hình lớn hơn và thời lượng pin ấn tượng, lý tưởng cho những ai yêu thích không gian giải trí và làm việc rộng rãi. Máy được trang bị chip A16 Bionic mạnh mẽ, đảm bảo hiệu năng mượt mà cho mọi tác vụ.
Màn hình Super Retina XDR 6.7 inch với công nghệ Dynamic Island mở rộng không gian hiển thị, mang lại hình ảnh sắc nét, sống động. Đây là lựa chọn hoàn hảo để xem phim, chơi game hay làm việc đa nhiệm.
Hệ thống camera kép với camera chính 48MP và camera Ultra Wide 12MP giúp bạn chụp ảnh và quay video chất lượng cao với độ chi tiết ấn tượng. Khả năng chụp ảnh chân dung được cải thiện và các tính năng thông minh giúp bạn dễ dàng có được những bức ảnh đẹp.
Với cổng USB-C tiện lợi, iPhone 15 Plus giúp việc sạc và truyền dữ liệu trở nên dễ dàng hơn bao giờ hết. Khả năng kết nối 5G và Wi-Fi 6 siêu tốc đảm bảo bạn luôn được kết nối ổn định và nhanh chóng.
Thiết kế bền bỉ với mặt lưng kính màu sắc được xử lý đặc biệt và khung nhôm chắc chắn. Viên pin dung lượng lớn mang lại thời lượng sử dụng dài, giúp bạn thoải mái trải nghiệm suốt cả ngày dài mà không cần lo lắng về việc sạc pin.', 'assets/products/iphone/iphone15plus_hongnhat.png', 1, 'IPHONE15PLUS', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2011, 1, 'Xanh Lá');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2012, 1, 'Vàng');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2013, 1, 'Xanh Dương');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10091, 1008, '1008-128GB-EN', 22990000, 20, 'assets/products/iphone/iphone15plus_den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10091, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10091, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10092, 1008, '1008-128GB-XANHL', 22990000, 20, 'assets/products/iphone/iphone15plus_xanhlanhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10092, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10092, 2011);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10093, 1008, '1008-128GB-HNG', 22990000, 20, 'assets/products/iphone/iphone15plus_hongnhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10093, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10093, 2007);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10094, 1008, '1008-128GB-VNG', 22990000, 20, 'assets/products/iphone/iphone15_vangnhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10094, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10094, 2012);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10095, 1008, '1008-128GB-XANHDNG', 22990000, 20, 'assets/products/iphone/iphone15plus_xanhduongnhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10095, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10095, 2013);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10096, 1008, '1008-256GB-EN', 25990000, 20, 'assets/products/iphone/iphone15plus_den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10096, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10096, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10097, 1008, '1008-256GB-XANHL', 25990000, 20, 'assets/products/iphone/iphone15plus_xanhlanhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10097, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10097, 2011);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10098, 1008, '1008-256GB-HNG', 25990000, 20, 'assets/products/iphone/iphone15plus_hongnhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10098, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10098, 2007);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10099, 1008, '1008-256GB-VNG', 25990000, 20, 'assets/products/iphone/iphone15_vangnhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10099, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10099, 2012);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10100, 1008, '1008-256GB-XANHDNG', 25990000, 20, 'assets/products/iphone/iphone15plus_xanhduongnhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10100, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10100, 2013);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10101, 1008, '1008-512GB-EN', 30990000, 20, 'assets/products/iphone/iphone15plus_den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10101, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10101, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10102, 1008, '1008-512GB-XANHL', 30990000, 20, 'assets/products/iphone/iphone15plus_xanhlanhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10102, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10102, 2011);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10103, 1008, '1008-512GB-HNG', 30990000, 20, 'assets/products/iphone/iphone15plus_hongnhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10103, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10103, 2007);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10104, 1008, '1008-512GB-VNG', 30990000, 20, 'assets/products/iphone/iphone15_vangnhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10104, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10104, 2012);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10105, 1008, '1008-512GB-XANHDNG', 30990000, 20, 'assets/products/iphone/iphone15plus_xanhduongnhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10105, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10105, 2013);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1009, 'iPhone 15', 19990000, 'iPhone 15 là mẫu điện thoại mới nhất của Apple, kế thừa những tinh hoa thiết kế và công nghệ. Máy được trang bị chip A16 Bionic mạnh mẽ, mang đến hiệu năng vượt trội và khả năng xử lý đồ họa ấn tượng, đáp ứng mọi nhu cầu từ công việc đến giải trí.
Màn hình Super Retina XDR 6.1 inch sắc nét với công nghệ Dynamic Island độc đáo, mang lại trải nghiệm tương tác trực quan và thú vị. Độ sáng tối đa cao giúp hiển thị rõ ràng ngay cả dưới ánh nắng mặt trời gay gắt.
Hệ thống camera kép được nâng cấp đáng kể với camera chính 48MP và camera Ultra Wide 12MP, cho phép bạn chụp ảnh chi tiết hơn và quay video 4K chất lượng cao. Chế độ chân dung thế hệ mới và các tính năng chụp ảnh thông minh khác giúp bạn ghi lại mọi khoảnh khắc một cách chuyên nghiệp.
iPhone 15 tích hợp cổng USB-C, mang lại sự tiện lợi và tương thích cao với nhiều thiết bị. Khả năng kết nối 5G siêu tốc và Wi-Fi 6 giúp bạn luôn duy trì kết nối ổn định và truyền tải dữ liệu nhanh chóng.
Với thiết kế bền bỉ, mặt lưng kính màu sắc được xử lý bằng phương pháp mới và khung nhôm chuẩn hàng không vũ trụ, iPhone 15 không chỉ đẹp mà còn chắc chắn. Thời lượng pin được cải thiện đáng kể, cho phép bạn sử dụng thiết bị suốt cả ngày dài mà không lo hết pin.', 'assets/products/iphone/iphone15_xanhduongnhat.png', 1, 'IPHONE15', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10106, 1009, '1009-128GB-EN', 19990000, 20, 'assets/products/iphone/iphone15_den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10106, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10106, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10107, 1009, '1009-128GB-XANHL', 19990000, 20, 'assets/products/iphone/iphone15_xanhlanhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10107, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10107, 2011);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10108, 1009, '1009-128GB-HNG', 19990000, 20, 'assets/products/iphone/iphone15_hongnhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10108, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10108, 2007);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10109, 1009, '1009-128GB-VNG', 19990000, 20, 'assets/products/iphone/iphone15_vangnhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10109, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10109, 2012);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10110, 1009, '1009-128GB-XANHDNG', 19990000, 20, 'assets/products/iphone/iphone15_xanhduongnhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10110, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10110, 2013);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10111, 1009, '1009-256GB-EN', 22990000, 20, 'assets/products/iphone/iphone15_den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10111, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10111, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10112, 1009, '1009-256GB-XANHL', 22990000, 20, 'assets/products/iphone/iphone15_xanhlanhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10112, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10112, 2011);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10113, 1009, '1009-256GB-HNG', 22990000, 20, 'assets/products/iphone/iphone15_hongnhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10113, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10113, 2007);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10114, 1009, '1009-256GB-VNG', 22990000, 20, 'assets/products/iphone/iphone15_vangnhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10114, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10114, 2012);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10115, 1009, '1009-256GB-XANHDNG', 22990000, 20, 'assets/products/iphone/iphone15_xanhduongnhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10115, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10115, 2013);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10116, 1009, '1009-512GB-EN', 27990000, 20, 'assets/products/iphone/iphone15_den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10116, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10116, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10117, 1009, '1009-512GB-XANHL', 27990000, 20, 'assets/products/iphone/iphone15_xanhlanhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10117, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10117, 2011);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10118, 1009, '1009-512GB-HNG', 27990000, 20, 'assets/products/iphone/iphone15_hongnhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10118, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10118, 2007);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10119, 1009, '1009-512GB-VNG', 27990000, 20, 'assets/products/iphone/iphone15_vangnhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10119, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10119, 2012);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10120, 1009, '1009-512GB-XANHDNG', 27990000, 20, 'assets/products/iphone/iphone15_xanhduongnhat.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10120, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10120, 2013);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1010, 'iPhone 14 Pro Max', 27990000, 'iPhone 14 Pro Max là siêu phẩm cao cấp nhất của Apple với màn hình lớn 6.7 inch và công nghệ Dynamic Island hiện đại, mang đến trải nghiệm tương tác hoàn toàn mới.
Trang bị màn hình Super Retina XDR với ProMotion 120Hz, thiết bị hiển thị hình ảnh mượt mà, sắc nét, ngay cả khi bạn cuộn hoặc chơi game tốc độ cao.
Cụm camera chuyên nghiệp với cảm biến chính 48MP, hỗ trợ chụp ảnh RAW, chế độ điện ảnh và quay video 4K giúp bạn sáng tạo như một nhiếp ảnh gia thực thụ.
Chip A16 Bionic siêu mạnh mẽ đảm bảo mọi thao tác đều mượt mà, đồng thời tiết kiệm điện năng, tối ưu hiệu suất sử dụng lâu dài.
Máy có thiết kế thép không gỉ bền bỉ, mặt lưng kính nhám sang trọng và hỗ trợ sạc nhanh cùng các kết nối hiện đại như 5G, Wi-Fi 6, và Lightning.', 'assets/products/iphone/iphone14prm_vang.png', 1, 'IPHONE14PROMAX', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2014, 1, 'Bạc');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2015, 1, 'Tím');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10121, 1010, '1010-128GB-EN', 27990000, 20, 'assets/products/iphone/iphone14prm_den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10121, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10121, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10122, 1010, '1010-128GB-BC', 27990000, 20, 'assets/products/iphone/iphone14prm_bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10122, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10122, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10123, 1010, '1010-128GB-VNG', 27990000, 20, 'assets/products/iphone/iphone14prm_vang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10123, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10123, 2012);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10124, 1010, '1010-128GB-TM', 27990000, 20, 'assets/products/iphone/iphone14prm_tim.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10124, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10124, 2015);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10125, 1010, '1010-256GB-EN', 30990000, 20, 'assets/products/iphone/iphone14prm_den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10125, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10125, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10126, 1010, '1010-256GB-BC', 30990000, 20, 'assets/products/iphone/iphone14prm_bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10126, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10126, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10127, 1010, '1010-256GB-VNG', 30990000, 20, 'assets/products/iphone/iphone14prm_vang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10127, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10127, 2012);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10128, 1010, '1010-256GB-TM', 30990000, 20, 'assets/products/iphone/iphone14prm_tim.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10128, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10128, 2015);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1011, 'iPhone 14 Pro', 25990000, 'iPhone 14 Pro mang đến thiết kế đột phá với Dynamic Island – khu vực tương tác mới thông minh và đầy sáng tạo.
Màn hình Super Retina XDR 6.1 inch kết hợp công nghệ ProMotion 120Hz cho trải nghiệm mượt mà, màu sắc rực rỡ và chi tiết cao.
Camera chính 48MP hỗ trợ chụp ảnh ProRAW cùng chế độ ban đêm, điện ảnh, giúp bạn lưu giữ mọi khoảnh khắc sắc nét và sống động.
Bên trong là chip A16 Bionic mạnh mẽ, tối ưu hiệu năng và tiết kiệm pin, phù hợp cả chơi game, làm việc lẫn chỉnh sửa ảnh/video chuyên nghiệp.
Thiết kế với khung thép không gỉ, mặt kính nhám chống bám vân tay, đi kèm khả năng chống nước IP68 cao cấp.', 'assets/products/iphone/iphone14pro_vang.png', 1, 'IPHONE14PRO', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10129, 1011, '1011-128GB-EN', 25990000, 20, 'assets/products/iphone/iphone14pro_den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10129, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10129, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10130, 1011, '1011-128GB-BC', 25990000, 20, 'assets/products/iphone/iphone14pro_bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10130, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10130, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10131, 1011, '1011-128GB-VNG', 25990000, 20, 'assets/products/iphone/iphone14pro_vang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10131, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10131, 2012);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10132, 1011, '1011-128GB-TM', 25990000, 20, 'assets/products/iphone/iphone14pro_tim.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10132, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10132, 2015);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10133, 1011, '1011-256GB-EN', 28990000, 20, 'assets/products/iphone/iphone14pro_den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10133, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10133, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10134, 1011, '1011-256GB-BC', 28990000, 20, 'assets/products/iphone/iphone14pro_bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10134, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10134, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10135, 1011, '1011-256GB-VNG', 28990000, 20, 'assets/products/iphone/iphone14pro_vang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10135, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10135, 2012);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10136, 1011, '1011-256GB-TM', 28990000, 20, 'assets/products/iphone/iphone14pro_tim.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10136, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10136, 2015);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1012, 'iPhone 14 Plus', 21990000, 'iPhone 14 Plus sở hữu màn hình lớn 6.7 inch cùng thời lượng pin vượt trội, lý tưởng cho người dùng yêu thích xem phim, chơi game hay làm việc cả ngày dài.
Trang bị chip A15 Bionic mạnh mẽ với GPU 5 lõi, iPhone 14 Plus xử lý mượt mà mọi tác vụ từ đa nhiệm đến giải trí nặng như game 3D hay chỉnh sửa video.
Camera kép 12MP với công nghệ Photonic Engine cải thiện khả năng chụp thiếu sáng, mang lại ảnh và video sắc nét, sống động trong mọi điều kiện ánh sáng.
Thiết kế nguyên khối với khung nhôm bền bỉ và mặt kính bóng bẩy. Mặt trước là kính Ceramic Shield tăng cường độ bền và chống va đập.
Hỗ trợ 5G, Wi-Fi 6 cùng cổng Lightning quen thuộc, mang đến trải nghiệm kết nối nhanh và ổn định trong mọi tình huống.', 'assets/products/iphone/iphone14plus_vang_256v128.png', 1, 'IPHONE14PLUS', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2016, 1, 'Đỏ');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10137, 1012, '1012-128GB-EN', 21990000, 20, 'assets/products/iphone/iphone14plus_den_512v256v128.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10137, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10137, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10138, 1012, '1012-128GB-TRNG', 21990000, 20, 'assets/products/iphone/iphone14plus_trang_512v256.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10138, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10138, 2006);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10139, 1012, '1012-128GB-', 21990000, 20, 'assets/products/iphone/iphone14plus_do_512GB.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10139, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10139, 2016);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10140, 1012, '1012-128GB-TM', 21990000, 20, 'assets/products/iphone/iphone14plus_timnhat_512v256.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10140, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10140, 2015);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10141, 1012, '1012-128GB-XANHDNG', 21990000, 20, 'assets/products/iphone/iphone14plus_xanhduong_512v256.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10141, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10141, 2013);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10142, 1012, '1012-256GB-EN', 24990000, 20, 'assets/products/iphone/iphone14plus_den_512v256v128.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10142, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10142, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10143, 1012, '1012-256GB-TRNG', 24990000, 20, 'assets/products/iphone/iphone14plus_trang_512v256.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10143, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10143, 2006);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10144, 1012, '1012-256GB-', 24990000, 20, 'assets/products/iphone/iphone14plus_do_512GB.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10144, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10144, 2016);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10145, 1012, '1012-256GB-TM', 24990000, 20, 'assets/products/iphone/iphone14plus_timnhat_512v256.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10145, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10145, 2015);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10146, 1012, '1012-256GB-XANHDNG', 24990000, 20, 'assets/products/iphone/iphone14plus_xanhduong_512v256.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10146, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10146, 2013);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1013, 'iPhone 14', 18990000, 'iPhone 14 mang đến thiết kế quen thuộc nhưng được cải tiến về hiệu năng và trải nghiệm camera, phù hợp với người dùng yêu thích sự ổn định và mượt mà trong sử dụng hằng ngày.
Màn hình Super Retina XDR 6.1 inch hiển thị sắc nét với độ sáng cao, hỗ trợ hiển thị nội dung sống động, thích hợp cho giải trí và làm việc.
Camera kép với cảm biến chính 12MP và camera Ultra Wide 12MP hỗ trợ chụp ảnh thiếu sáng tốt hơn, cùng chế độ Photonic Engine nâng cao chất lượng ảnh toàn diện.
Chip A15 Bionic với GPU 5 lõi mang lại hiệu năng mạnh mẽ, tiết kiệm pin, đồng thời hỗ trợ các tác vụ nặng và chơi game một cách mượt mà.
Thiết kế nguyên khối chắc chắn với mặt lưng kính cường lực và khung nhôm, tích hợp eSIM, 5G và Wi-Fi 6 cho kết nối tốc độ cao, cùng thời lượng pin đủ dùng cả ngày.', 'assets/products/iphone/iphone14_xanhduong_256v128.png', 1, 'IPHONE14', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10147, 1013, '1013-128GB-EN', 18990000, 20, 'assets/products/iphone/iphone14_den_256v128.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10147, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10147, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10148, 1013, '1013-128GB-TRNG', 18990000, 20, 'assets/products/iphone/iphone14_trang_256v128.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10148, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10148, 2006);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10149, 1013, '1013-128GB-XANHDNG', 18990000, 20, 'assets/products/iphone/iphone14_xanhduong_256v128.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10149, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10149, 2013);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10150, 1013, '1013-256GB-EN', 21990000, 20, 'assets/products/iphone/iphone14_den_256v128.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10150, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10150, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10151, 1013, '1013-256GB-TRNG', 21990000, 20, 'assets/products/iphone/iphone14_trang_256v128.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10151, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10151, 2006);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10152, 1013, '1013-256GB-XANHDNG', 21990000, 20, 'assets/products/iphone/iphone14_xanhduong_256v128.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10152, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10152, 2013);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10153, 1013, '1013-512GB-EN', 26990000, 20, 'assets/products/iphone/iphone14_den_256v128.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10153, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10153, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10154, 1013, '1013-512GB-TRNG', 26990000, 20, 'assets/products/iphone/iphone14_trang_256v128.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10154, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10154, 2006);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10155, 1013, '1013-512GB-XANHDNG', 26990000, 20, 'assets/products/iphone/iphone14_xanhduong_256v128.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10155, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10155, 2013);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1014, 'iPhone 13 Pro Max', 27990000, 'iPhone 13 Pro Max là chiếc iPhone mạnh mẽ nhất năm 2021 với màn hình lớn 6.7 inch và pin cực trâu.
Tích hợp ProMotion 120Hz cho trải nghiệm cảm ứng mượt mà vượt trội, đặc biệt khi chơi game hay xem video tốc độ cao.
Camera 3 ống kính hỗ trợ Zoom quang học 3x, quay video chuẩn điện ảnh với Cinematic Mode, chụp đêm, Deep Fusion.
Hiệu năng đầu bảng nhờ chip A15 Bionic, RAM 6GB và GPU 5 nhân – mọi tác vụ đều siêu mượt.
Pin lớn nhất trên iPhone, dùng cả ngày thoải mái, hỗ trợ sạc nhanh, sạc không dây và MagSafe hiện đại.', 'assets/products/iphone/iphone13prm_xam.png', 1, 'IPHONE13PROMAX', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2017, 1, 'Xám');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2018, 1, 'Xanh lá');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10156, 1014, '1014-128GB-XANHDNG', 27990000, 20, 'assets/products/iphone/iphone13prm_xanhduong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10156, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10156, 2013);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10157, 1014, '1014-128GB-XM', 27990000, 20, 'assets/products/iphone/iphone13prm_xam.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10157, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10157, 2017);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10158, 1014, '1014-128GB-XANHL', 27990000, 20, 'assets/products/iphone/iphone13prm_xanhla.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10158, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10158, 2018);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10159, 1014, '1014-128GB-BC', 27990000, 20, 'assets/products/iphone/iphone13prm_bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10159, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10159, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10160, 1014, '1014-128GB-VNG', 27990000, 20, 'assets/products/iphone/iphone13prm_vangdong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10160, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10160, 2012);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10161, 1014, '1014-256GB-XANHDNG', 29990000, 20, 'assets/products/iphone/iphone13prm_xanhduong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10161, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10161, 2013);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10162, 1014, '1014-256GB-XM', 29990000, 20, 'assets/products/iphone/iphone13prm_xam.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10162, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10162, 2017);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10163, 1014, '1014-256GB-XANHL', 29990000, 20, 'assets/products/iphone/iphone13prm_xanhla.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10163, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10163, 2018);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10164, 1014, '1014-256GB-BC', 29990000, 20, 'assets/products/iphone/iphone13prm_bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10164, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10164, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10165, 1014, '1014-256GB-VNG', 29990000, 20, 'assets/products/iphone/iphone13prm_vangdong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10165, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10165, 2012);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1015, 'iPhone 13 Pro', 24990000, 'iPhone 13 Pro mang đến trải nghiệm cao cấp với thiết kế sang trọng, hiệu năng vượt trội và cụm 3 camera chuyên nghiệp.
Màn hình Super Retina XDR 6.1 inch với ProMotion 120Hz cho độ mượt mà tối đa khi cuộn và chơi game.
Trang bị chip A15 Bionic với GPU 5 nhân, iPhone 13 Pro xử lý mượt mọi tác vụ nặng, chơi game đồ họa cao hay quay dựng video chuyên nghiệp.
Camera 3 ống kính (Chính, Tele, Ultra Wide) hỗ trợ chụp đêm, xóa phông, quay video Dolby Vision, Cinematic Mode đầy ấn tượng.
iPhone 13 Pro sở hữu pin tốt hơn nhiều thế hệ trước, kết hợp cùng sạc nhanh, MagSafe và các công nghệ mới nhất của Apple.', 'assets/products/iphone/iphone13pro_xanhla.jpg', 1, 'IPHONE13PRO', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10166, 1015, '1015-128GB-XANHL', 24990000, 20, 'assets/products/iphone/iphone13pro_xanhla.jpg', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10166, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10166, 2011);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10167, 1015, '1015-128GB-VNG', 24990000, 20, 'assets/products/iphone/iphone13pro_vangdong.webp', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10167, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10167, 2012);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1016, 'iPhone 13', 16990000, 'iPhone 13 mang đến hiệu năng mạnh mẽ nhờ chip A15 Bionic cùng thiết kế hiện đại và nhiều màu sắc trẻ trung. Đây là lựa chọn lý tưởng cho những ai muốn một chiếc iPhone mạnh mẽ nhưng vừa tay.
Màn hình Super Retina XDR 6.1 inch cho hình ảnh sắc nét và sống động, lý tưởng để xem video, chơi game và sử dụng hàng ngày.
Camera kép 12MP hỗ trợ chế độ chụp ảnh chân dung, Deep Fusion và Smart HDR 4 giúp bạn ghi lại mọi khoảnh khắc một cách rõ nét và chuyên nghiệp.
iPhone 13 sử dụng kết nối 5G, Wi-Fi 6 và hỗ trợ sạc không dây MagSafe, mang lại trải nghiệm hiện đại và tiện lợi.
Với thời lượng pin cả ngày, thiết kế bền bỉ cùng iOS mượt mà, iPhone 13 là người bạn đồng hành đáng tin cậy trong công việc lẫn giải trí.', 'assets/products/iphone/iphone13_do.png', 1, 'IPHONE13', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10168, 1016, '1016-128GB-XANHL', 16990000, 20, 'assets/products/iphone/iphone13_xanhla_512v256.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10168, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10168, 2011);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10169, 1016, '1016-128GB-TRNG', 16990000, 20, 'assets/products/iphone/iphone13_trang_512v128.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10169, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10169, 2006);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10170, 1016, '1016-128GB-HNG', 16990000, 20, 'assets/products/iphone/iphone13_hong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10170, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10170, 2007);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10171, 1016, '1016-128GB-XANHDNG', 16990000, 20, 'assets/products/iphone/iphone13_xanhduong_512v256v128.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10171, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10171, 2013);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10172, 1016, '1016-128GB-', 16990000, 20, 'assets/products/iphone/iphone13_do.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10172, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10172, 2016);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10173, 1016, '1016-128GB-EN', 16990000, 20, 'assets/products/iphone/iphone13_den_512v128.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10173, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10173, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10174, 1016, '1016-256GB-XANHL', 19990000, 20, 'assets/products/iphone/iphone13_xanhla_512v256.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10174, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10174, 2011);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10175, 1016, '1016-256GB-TRNG', 19990000, 20, 'assets/products/iphone/iphone13_trang_512v128.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10175, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10175, 2006);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10176, 1016, '1016-256GB-HNG', 19990000, 20, 'assets/products/iphone/iphone13_hong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10176, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10176, 2007);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10177, 1016, '1016-512GB-', 23990000, 20, 'assets/products/iphone/iphone13_do.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10177, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10177, 2016);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10178, 1016, '1016-512GB-TRNG', 23990000, 20, 'assets/products/iphone/iphone13_trang_512v128.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10178, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10178, 2006);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10179, 1016, '1016-512GB-EN', 23990000, 20, 'assets/products/iphone/iphone13_den_512v128.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10179, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10179, 2005);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1017, 'iPhone 12 Pro Max', 24990000, 'iPhone 12 Pro Max mang đến trải nghiệm cao cấp với màn hình lớn 6.7 inch và thiết kế thép không gỉ sang trọng. Máy sở hữu hiệu năng mạnh mẽ cùng khả năng chụp ảnh vượt trội.
Trang bị chip A14 Bionic tiên tiến, iPhone 12 Pro Max dễ dàng xử lý mọi tác vụ từ chơi game đến chỉnh sửa video 4K mượt mà.
Cụm 3 camera sau 12MP cùng cảm biến LiDAR hỗ trợ chụp ảnh chân dung trong điều kiện thiếu sáng và quay video Dolby Vision đỉnh cao.
Màn hình Super Retina XDR cho hình ảnh sắc nét, màu sắc chân thực và độ sáng cao lên đến 1200 nits.
Kết nối 5G tốc độ cao, sạc không dây MagSafe tiện lợi cùng thời lượng pin được cải thiện giúp bạn yên tâm sử dụng cả ngày dài.', 'assets/products/iphone/iphone12prm_xanhduong.png', 1, 'IPHONE12PROMAX', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10180, 1017, '1017-128GB-XANHDNG', 24990000, 20, 'assets/products/iphone/iphone12prm_xanhduong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10180, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10180, 2013);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10181, 1017, '1017-128GB-VNG', 24990000, 20, 'assets/products/iphone/iphone12prm_vangdong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10181, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10181, 2012);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10182, 1017, '1017-128GB-BC', 24990000, 20, 'assets/products/iphone/iphone12prm_bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10182, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10182, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10183, 1017, '1017-128GB-XM', 24990000, 20, 'assets/products/iphone/iphone12prm_xam.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10183, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10183, 2017);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10184, 1017, '1017-256GB-XANHDNG', 27990000, 20, 'assets/products/iphone/iphone12prm_xanhduong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10184, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10184, 2013);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10185, 1017, '1017-256GB-VNG', 27990000, 20, 'assets/products/iphone/iphone12prm_vangdong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10185, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10185, 2012);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10186, 1017, '1017-256GB-BC', 27990000, 20, 'assets/products/iphone/iphone12prm_bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10186, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10186, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10187, 1017, '1017-256GB-XM', 27990000, 20, 'assets/products/iphone/iphone12prm_xam.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10187, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10187, 2017);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1018, 'iPhone 12 Pro', 21990000, 'iPhone 12 Pro là lựa chọn hoàn hảo cho người dùng muốn trải nghiệm thiết kế cao cấp, hiệu năng mạnh mẽ với chip A14 Bionic và cụm camera chuyên nghiệp.
Màn hình Super Retina XDR 6.1 inch với độ sáng cao, màu sắc chân thực, phù hợp cho việc giải trí và làm việc hiệu quả.
Hệ thống 3 camera sau: chính 12MP, tele 12MP, ultra wide 12MP, hỗ trợ chụp ảnh chân dung, zoom quang học và quay video chất lượng cao.
Thiết kế sang trọng với khung thép không gỉ và mặt kính Ceramic Shield chống trầy xước, độ bền vượt trội.
Hỗ trợ 5G, Wi-Fi 6, Face ID, sạc MagSafe tiện lợi và các tính năng bảo mật nâng cao.', 'assets/products/iphone/iphone12pro_xam.png', 1, 'IPHONE12PRO', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10188, 1018, '1018-128GB-XM', 21990000, 20, 'assets/products/iphone/iphone12pro_xam.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10188, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10188, 2017);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10189, 1018, '1018-128GB-BC', 21990000, 20, 'assets/products/iphone/iphone12pro_bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10189, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10189, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10190, 1018, '1018-128GB-VNG', 21990000, 20, 'assets/products/iphone/iphone12pro_vangdong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10190, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10190, 2012);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10191, 1018, '1018-128GB-XANHDNG', 21990000, 20, 'assets/products/iphone/iphone12pro_xanhduong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10191, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10191, 2013);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10192, 1018, '1018-256GB-XM', 24990000, 20, 'assets/products/iphone/iphone12pro_xam.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10192, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10192, 2017);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10193, 1018, '1018-256GB-BC', 24990000, 20, 'assets/products/iphone/iphone12pro_bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10193, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10193, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10194, 1018, '1018-256GB-VNG', 24990000, 20, 'assets/products/iphone/iphone12pro_vangdong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10194, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10194, 2012);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10195, 1018, '1018-256GB-XANHDNG', 24990000, 20, 'assets/products/iphone/iphone12pro_xanhduong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10195, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10195, 2013);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10196, 1018, '1018-512GB-XM', 29990000, 20, 'assets/products/iphone/iphone12pro_xam.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10196, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10196, 2017);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10197, 1018, '1018-512GB-BC', 29990000, 20, 'assets/products/iphone/iphone12pro_bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10197, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10197, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10198, 1018, '1018-512GB-VNG', 29990000, 20, 'assets/products/iphone/iphone12pro_vangdong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10198, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10198, 2012);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10199, 1018, '1018-512GB-XANHDNG', 29990000, 20, 'assets/products/iphone/iphone12pro_xanhduong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10199, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10199, 2013);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1019, 'iPhone 12', 16990000, 'iPhone 12 là mẫu iPhone thế hệ mới với thiết kế mỏng nhẹ, hiệu năng mạnh mẽ và hệ thống camera chất lượng.
Màn hình Super Retina XDR 6.1 inch mang đến hình ảnh sắc nét, màu sắc chân thực, phù hợp cho nhu cầu sử dụng hàng ngày.
Camera kép 12MP hỗ trợ chụp ảnh xóa phông, chụp đêm và quay video 4K HDR.
Khung nhôm nguyên khối, mặt kính Ceramic Shield giúp chống trầy xước và tăng độ bền cho máy.
Hỗ trợ 5G, Face ID, sạc MagSafe và nhiều tính năng thông minh khác.', 'assets/products/iphone/iphone12_tim.png', 1, 'IPHONE12', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (1005, 2, '64GB');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10200, 1019, '1019-64GB-EN', 16990000, 20, 'assets/products/iphone/iphone12_den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10200, 1005);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10200, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10201, 1019, '1019-64GB-TRNG', 16990000, 20, 'assets/products/iphone/iphone12_trang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10201, 1005);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10201, 2006);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10202, 1019, '1019-64GB-XANHL', 16990000, 20, 'assets/products/iphone/iphone12_xanhla.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10202, 1005);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10202, 2011);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10203, 1019, '1019-64GB-TM', 16990000, 20, 'assets/products/iphone/iphone12_tim.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10203, 1005);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10203, 2015);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10204, 1019, '1019-128GB-EN', 18990000, 20, 'assets/products/iphone/iphone12_den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10204, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10204, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10205, 1019, '1019-128GB-TRNG', 18990000, 20, 'assets/products/iphone/iphone12_trang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10205, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10205, 2006);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10206, 1019, '1019-128GB-XANHL', 18990000, 20, 'assets/products/iphone/iphone12_xanhla.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10206, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10206, 2011);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10207, 1019, '1019-128GB-TM', 18990000, 20, 'assets/products/iphone/iphone12_tim.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10207, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10207, 2015);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10208, 1019, '1019-256GB-EN', 21990000, 20, 'assets/products/iphone/iphone12_den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10208, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10208, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10209, 1019, '1019-256GB-TRNG', 21990000, 20, 'assets/products/iphone/iphone12_trang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10209, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10209, 2006);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10210, 1019, '1019-256GB-XANHL', 21990000, 20, 'assets/products/iphone/iphone12_xanhla.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10210, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10210, 2011);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10211, 1019, '1019-256GB-TM', 21990000, 20, 'assets/products/iphone/iphone12_tim.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10211, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10211, 2015);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1020, 'iPad Air M3 (2024)', 16990000, 'iPad Air M3 trang bị chip Apple M3 mạnh mẽ, mang lại hiệu năng vượt trội cho mọi tác vụ từ học tập đến làm việc chuyên nghiệp.
Màn hình Liquid Retina 10.9 inch với độ phân giải cao, hỗ trợ công nghệ True Tone và Wide Color (P3) cho màu sắc sống động.
Thiết kế mỏng nhẹ với viền màn hình mỏng, camera sau 12MP và camera trước Ultra Wide 12MP hỗ trợ Center Stage.
Hỗ trợ bút Apple Pencil 2 và bàn phím Magic Keyboard, biến iPad Air M3 thành công cụ sáng tạo và năng suất hiệu quả.
Kết nối 5G, Wi-Fi 6E, bảo mật Face ID và thời lượng pin dài lên đến 10 giờ.', 'assets/products/ipad/ipad_AirM3_Tim.png', 1, 'IPADAIRM32024', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2019, 1, 'Đen Xám');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10212, 1020, '1020-64GB-ENXM', 16990000, 20, 'assets/products/ipad/ipad_AirM3_denxam.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10212, 1005);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10212, 2019);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10213, 1020, '1020-64GB-XANHDNG', 16990000, 20, 'assets/products/iapd/ipad_AirM3_Xanhduong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10213, 1005);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10213, 2013);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10214, 1020, '1020-64GB-TM', 16990000, 20, 'assets/products/ipad/ipad_AirM3_Tim.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10214, 1005);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10214, 2015);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10215, 1020, '1020-64GB-TRNG', 16990000, 20, 'assets/products/ipad/ipad_AirM3_TrangStarlight.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10215, 1005);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10215, 2006);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10216, 1020, '1020-256GB-ENXM', 19990000, 20, 'assets/products/ipad/ipad_AirM3_denxam.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10216, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10216, 2019);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10217, 1020, '1020-256GB-XANHDNG', 19990000, 20, 'assets/products/iapd/ipad_AirM3_Xanhduong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10217, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10217, 2013);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10218, 1020, '1020-256GB-TM', 19990000, 20, 'assets/products/ipad/ipad_AirM3_Tim.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10218, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10218, 2015);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10219, 1020, '1020-256GB-TRNG', 19990000, 20, 'assets/products/ipad/ipad_AirM3_TrangStarlight.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10219, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10219, 2006);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1021, 'iPad 10 (A16 Bionic)', 12990000, 'iPad 10 sử dụng chip A16 Bionic, cho hiệu năng mạnh mẽ, đáp ứng mọi nhu cầu học tập và giải trí.
Màn hình Retina 10.9 inch sắc nét, màu sắc trung thực với True Tone.
Camera sau 12MP và camera trước 12MP Ultra Wide hỗ trợ Center Stage giúp video call sống động.
Thiết kế mỏng nhẹ, hỗ trợ Apple Pencil 1 và bàn phím ngoài Smart Keyboard Folio.
Hỗ trợ kết nối 5G và Wi-Fi 6, thời lượng pin lên đến 10 giờ sử dụng.', 'assets/products/ipad/ipad_10_Hong.png', 1, 'IPAD10A16BIONIC', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10220, 1021, '1021-64GB-BC', 12990000, 20, 'assets/products/ipad/ipad_10_Bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10220, 1005);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10220, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10221, 1021, '1021-64GB-XANHDNG', 12990000, 20, 'assets/products/ipad/ipad_10_Xanhduong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10221, 1005);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10221, 2013);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10222, 1021, '1021-64GB-HNG', 12990000, 20, 'assets/products/ipad/ipad_10_Hong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10222, 1005);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10222, 2007);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10223, 1021, '1021-64GB-VNG', 12990000, 20, 'assets/products/ipad/ipad_10_Vang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10223, 1005);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10223, 2012);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10224, 1021, '1021-256GB-BC', 15990000, 20, 'assets/products/ipad/ipad_10_Bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10224, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10224, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10225, 1021, '1021-256GB-XANHDNG', 15990000, 20, 'assets/products/ipad/ipad_10_Xanhduong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10225, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10225, 2013);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10226, 1021, '1021-256GB-HNG', 15990000, 20, 'assets/products/ipad/ipad_10_Hong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10226, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10226, 2007);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10227, 1021, '1021-256GB-VNG', 15990000, 20, 'assets/products/ipad/ipad_10_Vang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10227, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10227, 2012);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1022, 'iPad Pro M4 (2024)', 30990000, 'iPad Pro M4 là dòng iPad cao cấp nhất với chip Apple M4 mới, hiệu suất vượt trội và tiết kiệm năng lượng.
Màn hình Ultra Retina XDR 13 inch (hoặc 11 inch) sử dụng công nghệ OLED kép mang đến trải nghiệm hình ảnh tuyệt đẹp.
Thiết kế mỏng nhất từ trước đến nay của Apple, thân nhôm bền nhẹ.
Hỗ trợ Apple Pencil Pro và Magic Keyboard mới, mở ra trải nghiệm như laptop thực thụ.
Kết nối Wi-Fi 6E và 5G, thời lượng pin cả ngày với công nghệ sạc nhanh.', 'assets/products/ipad/ipad_proM4_bac.png', 1, 'IPADPROM42024', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10228, 1022, '1022-256GB-EN', 30990000, 20, 'assets/products/ipad/ipad_proM4_den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10228, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10228, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10229, 1022, '1022-256GB-BC', 30990000, 20, 'assets/products/ipad/ipad_proM4_bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10229, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10229, 2014);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1023, 'iPad Air M2 (2024)', 16990000, 'iPad Air M2 mang đến hiệu suất vượt trội nhờ chip Apple M2, phục vụ tốt nhu cầu học tập, làm việc và sáng tạo.
Màn hình Liquid Retina 11 inch rực rỡ với công nghệ True Tone và P3 Color.
Camera sau 12MP và camera trước 12MP hỗ trợ Center Stage.
Hỗ trợ Apple Pencil 2 và Magic Keyboard giúp nâng cao trải nghiệm làm việc.
Kết nối Wi-Fi 6E và 5G giúp truy cập internet siêu nhanh.', 'assets/products/ipad/ipad_Air6M2_TrangStarlight.png', 1, 'IPADAIRM22024', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10230, 1023, '1023-128GB-XANHDNG', 16990000, 20, 'assets/products/iapd/ipad_Air6M2_Xanhduong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10230, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10230, 2013);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10231, 1023, '1023-128GB-TM', 16990000, 20, 'assets/products/ipad/ipad_Air6M2_Tim.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10231, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10231, 2015);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10232, 1023, '1023-128GB-XM', 16990000, 20, 'assets/products/ipad/ipad_Air6M2_Xam.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10232, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10232, 2017);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10233, 1023, '1023-128GB-TRNG', 16990000, 20, 'assets/products/ipad/ipad_Air6M2_TrangStarlight.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10233, 1004);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10233, 2006);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1024, 'iPad Mini (Gen 6)', 12990000, 'iPad Mini 6 nhỏ gọn nhưng mạnh mẽ, với chip A15 Bionic, lý tưởng cho công việc di động và giải trí.
Màn hình Liquid Retina 8.3 inch sắc nét với True Tone và P3 Wide Color.
Camera sau và trước đều 12MP, hỗ trợ Center Stage khi gọi video.
Hỗ trợ Apple Pencil 2 giúp ghi chú và vẽ dễ dàng.
Cổng USB-C và hỗ trợ Wi-Fi 6, 5G giúp kết nối nhanh chóng.', 'assets/products/ipad/ipad_mini_trangstarlight.png', 1, 'IPADMINIGEN6', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10234, 1024, '1024-64GB-XM', 12990000, 20, 'assets/products/ipad/ipad_mini_denxam.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10234, 1005);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10234, 2017);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10235, 1024, '1024-64GB-XANHDNG', 12990000, 20, 'assets/products/iapd/ipad_mini_xanhduong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10235, 1005);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10235, 2013);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10236, 1024, '1024-64GB-TRNG', 12990000, 20, 'assets/products/ipad/ipad_mini_trangstarlight.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10236, 1005);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10236, 2006);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10237, 1024, '1024-64GB-TM', 12990000, 20, 'assets/products/ipad/ipad_mini_tim.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10237, 1005);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10237, 2015);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1025, 'iPad 9 (2021)', 8990000, 'iPad 9 là lựa chọn phổ thông phù hợp cho học sinh và người dùng cơ bản.
Sử dụng chip A13 Bionic, hiệu năng ổn định trong học tập, xem phim và lướt web.
Màn hình Retina 10.2 inch sắc nét, hỗ trợ True Tone.
Camera trước 12MP Ultra Wide với Center Stage, camera sau 8MP.
Hỗ trợ Apple Pencil 1 và Smart Keyboard thông qua cổng Lightning.', 'assets/products/ipad/ipad_9_Bac.png', 1, 'IPAD92021', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10238, 1025, '1025-64GB-BC', 8990000, 20, 'assets/products/ipad/ipad_9_Bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10238, 1005);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10238, 2014);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1026, 'iPad 10 (A14 Bionic)', 10990000, 'iPad 10 phiên bản dùng chip A14 Bionic mang đến hiệu năng ổn định cho học tập và giải trí.
Thiết kế mới với màn hình Liquid Retina 10.9 inch viền mỏng, hỗ trợ True Tone và độ sáng cao.
Camera trước 12MP Ultra Wide đặt ngang giúp gọi video thuận tiện, hỗ trợ Center Stage.
Tương thích với Apple Pencil (Gen 1) qua adapter USB-C và bàn phím Smart Keyboard Folio.
Hỗ trợ Wi-Fi 6 và tùy chọn 5G giúp kết nối tốc độ cao mọi lúc mọi nơi.', 'assets/products/ipad/ipad_10_Bac.png', 1, 'IPAD10A14BIONIC', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10239, 1026, '1026-64GB-BC', 10990000, 20, 'assets/products/ipad/ipad_10_Bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10239, 1005);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10239, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10240, 1026, '1026-64GB-VNG', 10990000, 20, 'assets/products/ipad/ipad_10_Vang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10240, 1005);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10240, 2012);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10241, 1026, '1026-256GB-BC', 14490000, 20, 'assets/products/ipad/ipad_10_Bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10241, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10241, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10242, 1026, '1026-256GB-VNG', 14490000, 20, 'assets/products/ipad/ipad_10_Vang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10242, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10242, 2012);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1027, 'MacBook Air', 27990000, 'MacBook Air với chip Apple mang lại hiệu năng vượt trội cho cả công việc và giải trí.
Thiết kế siêu mỏng nhẹ, màn hình Liquid Retina 13.6 inch sắc nét, pin sử dụng đến 18 giờ.
Tích hợp webcam 1080p, Touch ID, và hệ thống loa 4 loa sống động.
Hỗ trợ Wi-Fi 6E, Thunderbolt 3, và sạc nhanh qua MagSafe 3.', 'assets/products/macbook/MacbookAir_Vang.png', 1, 'MACBOOKAIR', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2020, 1, 'Xanh Da Trời');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2021, 1, 'Xanh Đen');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10243, 1027, '1027-256GB-XANHDATRI', 27990000, 20, 'assets/products/macbook/MacbookAir_Xanhdatroinhat', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10243, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10243, 2020);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10244, 1027, '1027-256GB-BC', 27990000, 20, 'assets/products/macbook/MacbookAir_Bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10244, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10244, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10245, 1027, '1027-256GB-VNG', 27990000, 20, 'assets/products/macbook/MacbookAir_Vang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10245, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10245, 2012);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10246, 1027, '1027-256GB-XANHEN', 27990000, 20, 'assets/products/macbook/MacbookAir_Xanhden.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10246, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10246, 2021);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10247, 1027, '1027-512GB-XANHDATRI', 32990000, 20, 'assets/products/macbook/MacbookAir_Xanhdatroinhat', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10247, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10247, 2020);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10248, 1027, '1027-512GB-BC', 32990000, 20, 'assets/products/macbook/MacbookAir_Bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10248, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10248, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10249, 1027, '1027-512GB-VNG', 32990000, 20, 'assets/products/ipad/MacbookAir_Vang.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10249, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10249, 2012);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10250, 1027, '1027-512GB-XANHEN', 32990000, 20, 'assets/products/macbook/MacbookAir_Xanhden.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10250, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10250, 2021);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1028, 'MacBook Pro', 46990000, 'MacBook Pro là cỗ máy mạnh mẽ với chip Apple , tối ưu cho đồ họa, lập trình và xử lý video.
Màn hình Liquid Retina XDR 14 inch, độ sáng cao và hỗ trợ ProMotion 120Hz.
Hệ thống tản nhiệt hiệu quả, thời lượng pin lên đến 17 giờ.
Nhiều cổng kết nối chuyên nghiệp: HDMI, SDXC, MagSafe, 3 cổng Thunderbolt 4.', 'assets/products/macbook/MacbookPro_Bac.png', 1, 'MACBOOKPRO', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10251, 1028, '1028-512GB-XM', 46990000, 20, 'assets/products/macbook/MacbookPro_Xam.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10251, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10251, 2017);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10252, 1028, '1028-512GB-BC', 46990000, 20, 'assets/products/macbook/MacbookPro_Bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10252, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10252, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10253, 1028, '1028-512GB-EN', 46990000, 20, 'assets/products/macbook/MacbookPro_Den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10253, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10253, 2005);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10254, 1028, '1028-1TB-XM', 51990000, 20, 'assets/products/macbook/MacbookPro_Xam.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10254, 1003);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10254, 2017);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10255, 1028, '1028-1TB-BC', 51990000, 20, 'assets/products/macbook/MacbookPro_Bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10255, 1003);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10255, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10256, 1028, '1028-1TB-EN', 51990000, 20, 'assets/products/macbook/MacbookPro_Den.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10256, 1003);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10256, 2005);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1029, 'iMac 24 inch', 35990000, 'iMac thiết kế siêu mỏng, chip Apple mang lại hiệu suất đáng kinh ngạc.
Màn hình Retina 4.5K 24 inch sống động, hỗ trợ 1 tỷ màu.
Camera FaceTime HD 1080p và hệ thống 6 loa cao cấp.
Tích hợp Touch ID trên bàn phím và nhiều lựa chọn màu sắc bắt mắt.', 'assets/products/macbook/iMac24_M3_Hong.png', 1, 'IMAC24INCH', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10257, 1029, '1029-256GB-XANHDNG', 35990000, 20, 'assets/products/macbook/iMac24_M3_Xanhduong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10257, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10257, 2013);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10258, 1029, '1029-256GB-HNG', 35990000, 20, 'assets/products/macbook/iMac24_M3_Hong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10258, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10258, 2007);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1030, 'Mac mini', 14990000, 'Mac mini nhỏ gọn nhưng mạnh mẽ với chip Apple, phù hợp làm việc đa nhiệm và giải trí.
Thiết kế tối giản, dễ dàng đặt ở bất kỳ không gian nào.
Trang bị nhiều cổng kết nối hiện đại: Thunderbolt 4, HDMI, USB-A, Ethernet.
Hỗ trợ RAM lên đến 16GB và lưu trữ SSD nhanh chóng.', 'assets/products/macbook/Macmini_M4_Bac.png', 1, 'MACMINI', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10259, 1030, '1030-256GB-BC', 14990000, 20, 'assets/products/macbook/Macmini_M4_Bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10259, 1001);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10259, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10260, 1030, '1030-512GB-BC', 17990000, 20, 'assets/products/macbook/Macmini_M4_Bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10260, 1002);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10260, 2014);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1031, 'Mac Studio M2 Ultra', 87990000, 'Mac Studio M2 Ultra mạnh mẽ, tối ưu cho công việc sáng tạo chuyên sâu như biên tập video, thiết kế đồ họa.
Trang bị chip Apple M2 Ultra với hiệu năng vượt trội.
Hỗ trợ RAM lên tới 128GB, lưu trữ SSD tốc độ cao.
Nhiều cổng kết nối đa dạng, thích hợp cho môi trường làm việc chuyên nghiệp.', 'assets/products/macbook/MacStudio_M2Ultra.png', 1, 'MACSTUDIOM2ULTRA', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (1006, 2, '2TB');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10261, 1031, '1031-1TB-XM', 87990000, 20, 'assets/products/macbook/MacStudio_M2Ultra.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10261, 1003);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10261, 2017);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10262, 1031, '1031-2TB-XM', 99990000, 20, 'assets/products/macbook/MacStudio_M2Ultra.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10262, 1006);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10262, 2017);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1032, 'Apple Watch Series 10', 10990000, 'Apple Watch Series 10 với thiết kế mỏng nhẹ, viền màn hình siêu mỏng.
Màn hình Always-On Retina LTPO OLED sắc nét, hỗ trợ cảm biến sức khỏe nâng cao.
Chip S10 siêu mạnh, tối ưu hiệu năng và tiết kiệm pin.
Hỗ trợ đo nhịp tim, ECG, theo dõi oxy trong máu và nhiều chế độ luyện tập.', 'assets/products/watch/applewatch_series10_vanghong.png', 1, 'APPLEWATCHSERIES10', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (1007, 2, '41mm');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (1008, 2, '45mm');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2022, 1, 'Đen bóng');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2023, 1, 'Vàng hồng');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10263, 1032, '1032-41MM-BC', 10990000, 20, 'assets/products/watch/applewatch_series10_bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10263, 1007);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10263, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10264, 1032, '1032-41MM-ENBNG', 17990000, 20, 'assets/products/watch/applewatch_series10_denbong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10264, 1007);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10264, 2022);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10265, 1032, '1032-41MM-VNGHNG', 11990000, 20, 'assets/products/watch/applewatch_series10_vanghong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10265, 1007);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10265, 2023);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10266, 1032, '1032-45MM-BC', 11990000, 20, 'assets/products/watch/applewatch_series10_bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10266, 1008);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10266, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10267, 1032, '1032-45MM-ENBNG', 18990000, 20, 'assets/products/watch/applewatch_series10_denbong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10267, 1008);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10267, 2022);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10268, 1032, '1032-45MM-VNGHNG', 12990000, 20, 'assets/products/watch/applewatch_series10_vanghong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10268, 1008);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10268, 2023);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1033, 'Apple Watch Ultra 2', 30990000, 'Apple Watch Ultra 2 dành cho người dùng yêu thích thể thao mạo hiểm, với thiết kế bền bỉ, chịu va đập tốt.
Màn hình lớn 49mm Retina LTPO OLED, độ sáng cực cao, dễ dàng nhìn ngoài trời nắng.
Chip S10 Ultra cải tiến, tối ưu cho các hoạt động ngoài trời và thể thao.
Tích hợp GPS đa băng tần, đo độ sâu, la bàn nâng cao và các cảm biến sinh học tiên tiến.', 'assets/products/watch/applewatch_ultra2_dayocean.png', 1, 'APPLEWATCHULTRA2', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (1009, 2, '49mm');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2024, 1, 'Alpine');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2025, 1, 'Trail');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2026, 1, 'Ocean');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10269, 1033, '1033-49MM-ALPINE', 30990000, 20, 'assets/products/watch/applewatch_ultra2_dayalpine.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10269, 1009);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10269, 2024);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10270, 1033, '1033-49MM-TRAIL', 32990000, 20, 'assets/products/watch/applewatch_ultra2_daytrail.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10270, 1009);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10270, 2025);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10271, 1033, '1033-49MM-OCEAN', 34990000, 20, 'assets/products/watch/applewatch_ultra2_dayocean.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10271, 1009);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10271, 2026);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1034, 'Apple Watch Series 9', 9990000, 'Apple Watch Series 9 cải tiến với chip S9 mạnh mẽ, thời lượng pin lâu hơn.
Màn hình Always-On Retina với độ sáng được nâng cấp.
Nhiều chế độ luyện tập và cảm biến sức khỏe tích hợp.
Hỗ trợ đo điện tâm đồ (ECG), nhịp tim và oxy trong máu.', 'assets/products/watch/applewatch_series9_do.png', 1, 'APPLEWATCHSERIES9', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2027, 1, 'Xanh đen');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10272, 1034, '1034-41MM-', 9990000, 20, 'assets/products/watch/applewatch_series9_do.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10272, 1007);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10272, 2016);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10273, 1034, '1034-41MM-XANHEN', 16990000, 20, 'assets/products/watch/applewatch_series9_xanhdendam.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10273, 1007);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10273, 2027);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10274, 1034, '1034-45MM-', 10990000, 20, 'assets/products/watch/applewatch_series9_do.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10274, 1008);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10274, 2016);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10275, 1034, '1034-45MM-XANHEN', 17990000, 20, 'assets/products/watch/applewatch_series9_xanhdendam.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10275, 1008);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10275, 2027);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1035, 'Apple Watch SE 2', 6990000, 'Apple Watch SE 2 phù hợp cho người mới dùng, với nhiều tính năng cơ bản và giá cả phải chăng.
Chip S8 cho hiệu năng mượt mà, hỗ trợ đa dạng các chế độ luyện tập.
Màn hình Retina lớn, cảm biến nhịp tim và theo dõi sức khỏe cơ bản.
Hỗ trợ chống nước 50 mét và nhiều màu sắc trẻ trung.', 'assets/products/watch/applewatch_se2_xanhden.png', 1, 'APPLEWATCHSE2', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (1010, 2, '40mm');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (1011, 2, '44mm');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10276, 1035, '1035-40MM-BC', 6990000, 20, 'assets/products/watch/applewatch_se2_bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10276, 1010);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10276, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10277, 1035, '1035-40MM-XANHEN', 10990000, 20, 'assets/products/watch/applewatch_se2_xanhden.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10277, 1010);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10277, 2027);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10278, 1035, '1035-40MM-TRNG', 7490000, 20, 'assets/products/watch/applewatch_se2_trangstarlight.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10278, 1010);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10278, 2006);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10279, 1035, '1035-44MM-BC', 7990000, 20, 'assets/products/watch/applewatch_se2_bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10279, 1011);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10279, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10280, 1035, '1035-44MM-XANHEN', 11990000, 20, 'assets/products/watch/applewatch_se2_xanhden.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10280, 1011);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10280, 2027);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10281, 1035, '1035-44MM-TRNG', 8490000, 20, 'assets/products/watch/applewatch_se2_trangstarlight.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10281, 1011);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10281, 2006);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1036, 'EarPods Lightning', 390000, 'Tai nghe EarPods với đầu kết nối Lightning tương thích với iPhone và iPad.
Thiết kế gọn nhẹ, vừa vặn tai, mang lại âm thanh rõ ràng và chân thực.
Tích hợp micro và điều khiển từ xa để điều chỉnh âm lượng, phát/tạm dừng nhạc.', 'assets/products/tainghe/Earpods_lightning.png', 1, 'EARPODSLIGHTNING', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (1012, 2, 'standard');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10282, 1036, '1036-STANDARD-TRNG', 390000, 20, 'assets/products/tainghe/Earpods_lightning.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10282, 1012);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10282, 2006);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1037, 'EarPods USB-C', 450000, 'Tai nghe EarPods với đầu kết nối USB-C dành cho các thiết bị Apple và Android hỗ trợ USB-C.
Thiết kế tương tự EarPods Lightning, âm thanh trong trẻo, độ bền cao.
Điều khiển âm lượng và micro tích hợp.', 'assets/products/tainghe/earpods_usb_c.png', 1, 'EARPODSUSBC', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10283, 1037, '1037-STANDARD-TRNG', 450000, 20, 'assets/products/tainghe/earpods_usb_c.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10283, 1012);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10283, 2006);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1038, 'AirPods 3', 3990000, 'AirPods 3 với thiết kế tai nghe mở, vừa vặn thoải mái cho nhiều kích cỡ tai.
Hỗ trợ âm thanh không gian Spatial Audio, Adaptive EQ và cảm biến lực.
Thời lượng pin lên đến 6 giờ nghe liên tục, hộp sạc MagSafe hỗ trợ sạc không dây.', 'assets/products/tainghe/Airpod3_lightning.png', 1, 'AIRPODS3', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10284, 1038, '1038-STANDARD-TRNG', 3990000, 20, 'assets/products/tainghe/Airpod3_lightning.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10284, 1012);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10284, 2006);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1039, 'AirPods Pro', 6990000, 'AirPods Pro với tính năng chống ồn chủ động (ANC) vượt trội.
Thiết kế in-ear với 3 kích cỡ đệm tai silicon giúp cách âm tốt hơn.
Hỗ trợ Spatial Audio, Adaptive EQ và chế độ Transparency.
Thời lượng pin đến 4.5 giờ nghe, hộp sạc hỗ trợ sạc không dây MagSafe.', 'assets/products/tainghe/Airpods_pro_usb_c.png', 1, 'AIRPODSPRO', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10285, 1039, '1039-STANDARD-TRNG', 6990000, 20, 'assets/products/tainghe/Airpods_pro_usb_c.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10285, 1012);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10285, 2006);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1040, 'AirPods Max', 15990000, 'AirPods Max là tai nghe chụp đầu cao cấp với âm thanh Hi-Fi chất lượng cao.
Thiết kế khung nhôm và đệm tai memory foam sang trọng, thoải mái khi đeo lâu.
Tích hợp công nghệ chống ồn chủ động ANC và Transparency mode.
Thời lượng pin lên đến 20 giờ nghe liên tục, hỗ trợ sạc nhanh.', 'assets/products/tainghe/AirpodsMax_Hong.png', 1, 'AIRPODSMAX', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10286, 1040, '1040-STANDARD-BC', 15990000, 20, 'assets/products/tainghe/AirpodsMax_Bac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10286, 1012);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10286, 2014);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10287, 1040, '1040-STANDARD-HNG', 15990000, 20, 'assets/products/tainghe/AirpodsMax_Hong.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10287, 1012);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10287, 2007);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1041, 'AirPods 4', 4990000, 'AirPods 4 kế thừa thiết kế nhỏ gọn, cải tiến khả năng chống ồn và âm thanh Spatial Audio sống động.
Tích hợp chip Apple H2 mới nhất giúp kết nối nhanh và ổn định hơn.
Thời lượng pin cải thiện lên đến 8 giờ nghe liên tục, hỗ trợ sạc không dây MagSafe.
Tính năng nâng cao như Adaptive EQ, Transparency Mode và cảm biến lực nhạy bén.', 'assets/products/tainghe/Airpods4_chongon.png', 1, 'AIRPODS4', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10288, 1041, '1041-STANDARD-TRNG', 4990000, 20, 'assets/products/tainghe/Airpods4_chongon.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10288, 1012);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10288, 2006);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1042, 'Adapter sạc sbC', 590000, 'Adapter sạc USB-C với công suất cao, tương thích với nhiều thiết bị Apple như iPhone, iPad và MacBook.
Thiết kế nhỏ gọn, an toàn với công nghệ sạc nhanh và bảo vệ quá tải.', 'assets/products/phukien/Adapter_sacusbC.png', 1, '', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (1013, 2, 'Standard');
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2028, 1, 'White');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10289, 1042, '1042-STANDARD-WHITE', 590000, 20, 'assets/products/phukien/Adapter_sacusbC.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10289, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10289, 2028);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1043, 'Airtag', 790000, 'Thiết bị theo dõi thông minh từ Apple, sử dụng với ứng dụng Find My để định vị đồ vật.
Tích hợp chip U1 Ultra Wideband cho độ chính xác cao.', 'assets/products/phukien/Airtag.png', 1, 'AIRTAG', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2029, 1, 'Silver');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10290, 1043, '1043-STANDARD-SILVER', 790000, 20, 'assets/products/phukien/Airtag.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10290, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10290, 2029);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1044, 'Apple Pencil Pro', 3290000, 'Bút stylus cao cấp cho iPad, hỗ trợ các tính năng chuyên nghiệp như nghiêng và cảm ứng lực.
Tích hợp sạc không dây và cảm biến áp suất mới.', 'assets/products/phukien/apple_pencilpro.png', 1, 'APPLEPENCILPRO', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10291, 1044, '1044-STANDARD-WHITE', 3290000, 20, 'assets/products/phukien/apple_pencilpro.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10291, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10291, 2028);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1045, 'Bandichuot', 490000, 'Dây đeo chuột không dây, thiết kế tiện lợi cho người dùng máy tính.
Tăng cường độ chính xác và thoải mái khi sử dụng.', 'assets/products/phukien/bandichuot.png', 1, 'BANDICHUOT', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2030, 1, 'Pink');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10292, 1045, '1045-STANDARD-PINK', 490000, 20, 'assets/products/phukien/bandichuot.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10292, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10292, 2030);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1046, 'Baoda iPad', 990000, 'Ốp lưng bảo vệ dành riêng cho iPad, làm từ chất liệu cao cấp.
Thiết kế mỏng nhẹ, hỗ trợ sạc không dây.', 'assets/products/phukien/baodaIpad.png', 1, 'BAODAIPAD', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2031, 1, 'Gray');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10293, 1046, '1046-STANDARD-GRAY', 990000, 20, 'assets/products/phukien/baodaIpad.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10293, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10293, 2031);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1047, 'Cáp Type-C', 290000, 'Cáp sạc và truyền dữ liệu Type-C, tương thích với nhiều thiết bị.
Hỗ trợ sạc nhanh và truyền dữ liệu tốc độ cao.', 'assets/products/phukien/cap_typeC.png', 1, 'CAPTYPEC', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2032, 1, 'Black');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10294, 1047, '1047-STANDARD-BLACK', 290000, 20, 'assets/products/phukien/cap_typeC.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10294, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10294, 2032);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1048, 'Capsac Type-C', 690000, 'Bao da sạc không dây Type-C, tiện lợi cho việc bảo vệ và sạc.
Thiết kế chắc chắn, hỗ trợ sạc nhanh.', 'assets/products/phukien/capsac_typeC.png', 1, 'CAPSACTYPEC', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10295, 1048, '1048-STANDARD-BLACK', 690000, 20, 'assets/products/phukien/capsac_typeC.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10295, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10295, 2032);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1049, 'Chuot Laptop', 390000, 'Chuột không dây dành cho laptop, thiết kế nhỏ gọn và tiện dụng.
Phù hợp cho công việc và giải trí.', 'assets/products/phukien/chuotlaptop.png', 1, 'CHUOTLAPTOP', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10296, 1049, '1049-STANDARD-PINK', 390000, 20, 'assets/products/phukien/chuotlaptop.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10296, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10296, 2030);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1050, 'Cuongluc iPad Air', 290000, 'Miếng dán cường lực dành cho iPad Air, bảo vệ màn hình khỏi trầy xước.
Độ trong suốt cao, chống vân tay hiệu quả.', 'assets/products/phukien/cuongluc_iPadAir.png', 1, 'CUONGLUCIPADAIR', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2033, 1, 'Clear');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10297, 1050, '1050-STANDARD-CLEAR', 290000, 20, 'assets/products/phukien/cuongluc_iPadAir.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10297, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10297, 2033);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1051, 'Cuongluc iPad Pro', 350000, 'Miếng dán cường lực dành cho iPad Pro, bảo vệ màn hình khỏi trầy xước và va đập.
Độ trong suốt cao, chống vân tay và bám bẩn hiệu quả.', 'assets/products/phukien/cuongluc_iPadPro.png', 1, 'CUONGLUCIPADPRO', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10298, 1051, '1051-STANDARD-CLEAR', 350000, 20, 'assets/products/phukien/cuongluc_iPadPro.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10298, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10298, 2033);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1052, 'Cuongluc Apple Watch', 200000, 'Miếng dán cường lực dành cho Apple Watch, bảo vệ màn hình khỏi trầy xước.
Thiết kế mỏng nhẹ, không ảnh hưởng đến cảm ứng.', 'assets/products/phukien/cuonglucAppleWatch.png', 1, 'CUONGLUCAPPLEWATCH', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10299, 1052, '1052-STANDARD-CLEAR', 200000, 20, 'assets/products/phukien/cuonglucAppleWatch.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10299, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10299, 2033);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1053, 'Cuongluc iPhone', 300000, 'Miếng dán cường lực dành cho iPhone, bảo vệ màn hình khỏi trầy xước và va đập mạnh.
Công nghệ chống bám vân tay và độ trong suốt cao.', 'assets/products/phukien/cuonglucIphone.png', 1, 'CUONGLUCIPHONE', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10300, 1053, '1053-STANDARD-CLEAR', 300000, 20, 'assets/products/phukien/cuonglucIphone.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10300, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10300, 2033);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1054, 'Cuongluc Mac', 500000, 'Miếng dán cường lực dành cho MacBook, bảo vệ màn hình khỏi trầy xước.
Độ trong suốt cao, dễ dàng lắp đặt.', 'assets/products/phukien/cuonglucMac.png', 1, 'CUONGLUCMAC', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10301, 1054, '1054-STANDARD-CLEAR', 500000, 20, 'assets/products/phukien/cuonglucMac.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10301, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10301, 2033);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1055, 'Daycaosu Apple Watch', 790000, 'Dây đeo cao su chính hãng cho Apple Watch, mềm mại và bền bỉ.
Thiết kế thể thao, phù hợp cho mọi hoạt động.', 'assets/products/phukien/daycaosuAppleWatch.png', 1, 'DAYCAOSUAPPLEWATCH', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10302, 1055, '1055-STANDARD-PINK', 790000, 20, 'assets/products/phukien/daycaosuAppleWatch.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10302, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10302, 2030);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1056, 'Daynylon Apple Watch', 890000, 'Dây đeo nylon cho Apple Watch, nhẹ và thoáng khí.
Thiết kế thời trang, dễ dàng thay đổi.', 'assets/products/phukien/dayNylonAppleWatch.png', 1, 'DAYNYLONAPPLEWATCH', 1);
INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES (2034, 1, 'Brown');
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10303, 1056, '1056-STANDARD-BROWN', 890000, 20, 'assets/products/phukien/dayNylonAppleWatch.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10303, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10303, 2034);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1057, 'Daysilicone Aggi Apple Watch', 690000, 'Dây đeo silicone cao cấp cho Apple Watch, chống thấm nước.
Thiết kế trẻ trung, phù hợp với mọi phong cách.', 'assets/products/phukien/daysiliconeAppleWatch.png', 1, 'DAYSILICONEAGGIAPPLEWATCH', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10304, 1057, '1057-STANDARD-BLACK', 690000, 20, 'assets/products/phukien/daysiliconeAppleWatch.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10304, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10304, 2032);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1058, 'Daythephkhong ggi Apple Watch', 1490000, 'Dây đeo thép không gỉ cho Apple Watch, sang trọng và bền bỉ.
Phù hợp với phong cách chuyên nghiệp.', 'assets/products/phukien/daythepkhonggiAppleWatch.png', 1, 'DAYTHEPHKHONGGGIAPPLEWATCH', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10305, 1058, '1058-STANDARD-SILVER', 1490000, 20, 'assets/products/phukien/daythepkhonggiAppleWatch.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10305, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10305, 2029);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1059, 'Desachkhongday Type-C', 990000, 'Đế sạc không dây Type-C, hỗ trợ sạc nhanh cho nhiều thiết bị.
Thiết kế tối giản, dễ dàng sử dụng.', 'assets/products/phukien/desackhongday_TypeC.png', 1, 'DESACHKHONGDAYTYPEC', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10306, 1059, '1059-STANDARD-WHITE', 990000, 20, 'assets/products/phukien/desackhongday_TypeC.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10306, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10306, 2028);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1060, 'Giado Laptop', 490000, 'Giá đỡ laptop, giúp điều chỉnh góc nhìn và tản nhiệt hiệu quả.
Thiết kế gấp gọn, dễ dàng mang theo.', 'assets/products/phukien/GiadoLaptop.png', 1, 'GIADOLAPTOP', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10307, 1060, '1060-STANDARD-SILVER', 490000, 20, 'assets/products/phukien/GiadoLaptop.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10307, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10307, 2029);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1061, 'Magic Keyboard', 2990000, 'Bàn phím Magic Keyboard từ Apple, thiết kế mỏng nhẹ và sang trọng.
Hỗ trợ kết nối không dây, tích hợp cảm ứng đa điểm.', 'assets/products/phukien/MagicKeyboard.png', 1, 'MAGICKEYBOARD', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10308, 1061, '1061-STANDARD-WHITE', 2990000, 20, 'assets/products/phukien/MagicKeyboard.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10308, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10308, 2028);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1062, 'Op Apple Watch', 200000, 'Ốp bảo vệ dành cho Apple Watch, chống va đập và trầy xước.
Thiết kế trong suốt, không che khuất vẻ đẹp của đồng hồ.', 'assets/products/phukien/OpAppleWatch.png', 1, 'OPAPPLEWATCH', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10309, 1062, '1062-STANDARD-CLEAR', 200000, 20, 'assets/products/phukien/OpAppleWatch.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10309, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10309, 2033);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1063, 'Thietbicamtay chongrung', 2490000, 'Thiết bị cầm tay chống rung, hỗ trợ quay video mượt mà.
Tương thích với iPhone và các thiết bị khác.', 'assets/products/phukien/thietbicamtaychongrung.png', 1, 'THIETBICAMTAYCHONGRUNG', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10310, 1063, '1063-STANDARD-BLACK', 2490000, 20, 'assets/products/phukien/thietbicamtaychongrung.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10310, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10310, 2032);
INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES (1064, 'Thuam', 1990000, 'Thiết bị thu âm chuyên nghiệp, hỗ trợ ghi âm chất lượng cao.
Tương thích với iPhone, MacBook và các thiết bị khác.', 'assets/products/phukien/thuam.png', 1, 'THUAM', 1);
INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES (10311, 1064, '1064-STANDARD-BLACK', 1990000, 20, 'assets/products/phukien/thuam.png', 1);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10311, 1013);
INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (10311, 2032);