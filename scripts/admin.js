// js/admin.js
document.querySelectorAll('.admin-header nav a').forEach(link => {
    link.addEventListener('click', () => {
        document.querySelector('.admin-header nav a.active')?.classList.remove('active');
        link.classList.add('active');
    });
});

// Tự động làm mới thông báo mỗi 5 phút
setInterval(() => {
    fetch('check_new_orders.php')
        .then(response => response.json())
        .then(data => {
            const notification = document.querySelector('.notification');
            if (data.new_orders > 0) {
                if (!notification) {
                    const container = document.querySelector('.admin-container');
                    const newNotification = document.createElement('div');
                    newNotification.className = 'notification';
                    newNotification.innerHTML = `<i class="fas fa-bell"></i> Có ${data.new_orders} đơn hàng mới trong 24 giờ qua! <a href="manage_orders.php?status=pending">Xem ngay</a>`;
                    container.insertBefore(newNotification, container.firstChild);
                } else {
                    notification.innerHTML = `<i class="fas fa-bell"></i> Có ${data.new_orders} đơn hàng mới trong 24 giờ qua! <a href="manage_orders.php?status=pending">Xem ngay</a>`;
                }
            } else if (notification) {
                notification.remove();
            }
        });
}, 300000);

document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.querySelector('.sidebar');
    const adminContainer = document.querySelector('.admin-container');
    document.querySelectorAll('.sidebar-toggle, .sidebar-toggle-topbar').forEach(button => {
        button.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            adminContainer.classList.toggle('sidebar-collapsed');
        });
    });

    const currentPath = window.location.pathname.split('/').pop();
    document.querySelectorAll('.sidebar nav a').forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
        }
    });

    const successNotification = document.querySelector('.success-notification');
    if (successNotification) {
        setTimeout(() => {
            successNotification.classList.add('fade-out');
            setTimeout(() => successNotification.remove(), 500);
        }, 3000);
    }
});