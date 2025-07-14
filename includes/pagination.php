<?php


if (!isset($total_pages) || !isset($current_page) || !isset($base_url) || !isset($query_params)) {
    echo "Error: Pagination variables not set.";
    return; // Dừng thực thi nếu thiếu biến
}

if ($total_pages > 1): ?>
    <div class="pagination">
        <?php
        $base_query_string = http_build_query($query_params);

        // Previous page button
        if ($current_page > 1) {
            $prev_page_link = $base_url . '?' . $base_query_string . '&page=' . ($current_page - 1);
            if (empty($base_query_string)) {
                 $prev_page_link = $base_url . '?page=' . ($current_page - 1);
            }
            echo '<a href="' . htmlspecialchars($prev_page_link) . '" class="page-link prev-next-btn">&laquo; Trước</a>';
        }

        // Page numbers
        $num_links = 5; // Số lượng liên kết số trang hiển thị
        $start_page = max(1, $current_page - floor($num_links / 2));
        $end_page = min($total_pages, $current_page + ceil($num_links / 2) - 1);

        if ($end_page - $start_page + 1 < $num_links) {
            $start_page = max(1, $end_page - $num_links + 1);
        }

        if ($start_page > 1) {
            echo '<a href="' . htmlspecialchars($base_url . '?' . $base_query_string . '&page=1') . '" class="page-link">1</a>';
            if ($start_page > 2) {
                echo '<span class="pagination-ellipsis">...</span>';
            }
        }

        for ($i = $start_page; $i <= $end_page; $i++) {
            $page_link = $base_url . '?' . $base_query_string . '&page=' . $i;
            if (empty($base_query_string)) {
                $page_link = $base_url . '?page=' . $i;
            }
            $active_class = ($i == $current_page) ? 'active' : '';
            echo '<a href="' . htmlspecialchars($page_link) . '" class="page-link ' . $active_class . '">' . $i . '</a>';
        }

        if ($end_page < $total_pages) {
            if ($end_page < $total_pages - 1) {
                echo '<span class="pagination-ellipsis">...</span>';
            }
            echo '<a href="' . htmlspecialchars($base_url . '?' . $base_query_string . '&page=' . $total_pages) . '" class="page-link">' . $total_pages . '</a>';
        }

        // Next page button
        if ($current_page < $total_pages) {
            $next_page_link = $base_url . '?' . $base_query_string . '&page=' . ($current_page + 1);
             if (empty($base_query_string)) {
                 $next_page_link = $base_url . '?page=' . ($current_page + 1);
            }
            echo '<a href="' . htmlspecialchars($next_page_link) . '" class="page-link prev-next-btn">Tiếp &raquo;</a>';
        }
        ?>
    </div>
<?php endif; ?>