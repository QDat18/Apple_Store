<?php
$json = file_get_contents(__DIR__ . '/assets/product.json');
$data = json_decode($json, true);

$sql = [];
$sql[] = "DELETE FROM product_variant_attribute_links;";
$sql[] = "DELETE FROM product_variants;";
$sql[] = "DELETE FROM products;";
$sql[] = "DELETE FROM variant_attribute_values;";
$sql[] = "INSERT IGNORE INTO variant_attributes (id, name, display_name, type) VALUES (1, 'Color', 'Màu sắc', 'color_picker'), (2, 'Storage', 'Dung lượng', 'text');";

$storageMap = [];
$colorMap = [];
$storageId = 1000;
$colorId = 2000;
$variantId = 10000;
$productId = 1000;

function make_variant_code($productId, $storage, $color) {
    $slug = strtoupper($productId . '-' . preg_replace('/[^A-Za-z0-9]/', '', $storage) . '-' . preg_replace('/[^A-Za-z0-9]/', '', $color));
    return $slug;
}
function make_product_code($name) {
    $slug = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $name)));
    return $slug;
}

foreach ($data['categories'] as $cat) {
    foreach ($cat['products'] as $prod) {
        $productId++;
        $name = addslashes($prod['name']);
        $desc = addslashes(implode("\n", $prod['description']['paragraphs']));
        $img = addslashes($prod['defaultImage']);
        $product_code = make_product_code($prod['name']);
        $price = 0;
        // Lấy giá thấp nhất làm giá chính
        foreach ($prod['configurations'] as $storage => $colors) {
            foreach ($colors as $color => $conf) {
                if ($price == 0 || $conf['price'] < $price) $price = $conf['price'];
            }
        }
        $sql[] = "INSERT INTO products (id, product_name, price, description, product_image, category_id, product_code, status) VALUES ($productId, '$name', $price, '$desc', '$img', 1, '$product_code', 1);";
        // Seed variant_attribute_values (toàn cục, không reset)
        foreach (array_keys($prod['configurations']) as $storage) {
            if (!isset($storageMap[$storage])) {
                $storageId++;
                $storageMap[$storage] = $storageId;
                $sql[] = "INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES ($storageId, 2, '".addslashes($storage)."');";
            }
        }
        foreach ($prod['configurations'] as $storage => $colors) {
            foreach (array_keys($colors) as $color) {
                if (!isset($colorMap[$color])) {
                    $colorId++;
                    $colorMap[$color] = $colorId;
                    $sql[] = "INSERT IGNORE INTO variant_attribute_values (id, attribute_id, value) VALUES ($colorId, 1, '".addslashes($color)."');";
                }
            }
        }
        // Seed product_variants và links
        foreach ($prod['configurations'] as $storage => $colors) {
            foreach ($colors as $color => $conf) {
                $variantId++;
                $vprice = $conf['price'];
                $vimg = addslashes($prod['images'][$storage][$color] ?? $img);
                $stock = 20;
                $variant_code = make_variant_code($productId, $storage, $color);
                $sql[] = "INSERT INTO product_variants (id, product_id, variant_code, variant_price, stock_quantity, variant_image, status) VALUES ($variantId, $productId, '$variant_code', $vprice, $stock, '$vimg', 1);";
                $sql[] = "INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES ($variantId, {$storageMap[$storage]});";
                $sql[] = "INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES ($variantId, {$colorMap[$color]});";
            }
        }
    }
}

file_put_contents(__DIR__ . '/seed_products.sql', implode("\n", $sql));
echo "Đã xuất file seed_products.sql thành công!";