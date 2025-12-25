/**
 * Search Enhancement Script
 * Tăng cường trải nghiệm tìm kiếm sách
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================
    // 1. Autocomplete cho ô tìm kiếm (nếu cần)
    // ============================================
    const searchInputs = document.querySelectorAll('input[name="q"]');
    searchInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.boxShadow = '0 0 0 3px rgba(0, 0, 0, 0.1)';
        });
        
        input.addEventListener('blur', function() {
            this.style.boxShadow = 'none';
        });
    });

    // ============================================
    // 2. Hiệu ứng khi click vào nút thêm vào giỏ
    // ============================================
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Tạo hiệu ứng ripple
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });

    // ============================================
    // 3. Highlight từ khóa tìm kiếm
    // ============================================
    const searchQuery = getUrlParameter('q');
    if (searchQuery) {
        highlightKeywords(searchQuery);
    }

    // ============================================
    // 4. Bố cục bộ lọc - cuộn dính
    // ============================================
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        window.addEventListener('scroll', function() {
            const sidebarOffset = sidebar.offsetTop;
            const sidebarHeight = sidebar.offsetHeight;
            const windowHeight = window.innerHeight;
            
            if (window.pageYOffset > sidebarOffset - 20) {
                sidebar.style.position = 'fixed';
                sidebar.style.top = '20px';
                sidebar.style.width = 'calc(25% - 20px)';
            } else {
                sidebar.style.position = 'sticky';
                sidebar.style.top = '20px';
                sidebar.style.width = 'auto';
            }
        });
    }

    // ============================================
    // 5. Xác nhận trước khi gửi bộ lọc
    // ============================================
    const filterForm = document.querySelector('form[method="get"][action="search.php"]');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            // Có thể thêm validation ở đây nếu cần
            // e.preventDefault();
        });
    }

    // ============================================
    // 6. Thêm hiệu ứng hover cho sản phẩm
    // ============================================
    const productItems = document.querySelectorAll('.product-item');
    productItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
        });
        
        item.addEventListener('mouseleave', function() {
            // Reset styles
        });
    });

    // ============================================
    // 7. Lưu bộ lọc vào localStorage (optional)
    // ============================================
    const formInputs = document.querySelectorAll('.sidebar input, .sidebar select');
    formInputs.forEach(input => {
        // Khôi phục bộ lọc trước đó (nếu cần)
        const savedValue = localStorage.getItem('search_' + input.name);
        if (savedValue && input.type !== 'submit') {
            if (input.type === 'radio' || input.type === 'checkbox') {
                if (input.value === savedValue) input.checked = true;
            } else {
                input.value = savedValue;
            }
        }
        
        // Lưu khi thay đổi
        input.addEventListener('change', function() {
            localStorage.setItem('search_' + input.name, input.value);
        });
    });

});

// ============================================
// HỗTRỢ HÀM
// ============================================

/**
 * Lấy giá trị parameter từ URL
 */
function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    const results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

/**
 * Highlight từ khóa tìm kiếm trong kết quả
 */
function highlightKeywords(keywords) {
    if (!keywords.trim()) return;
    
    const regex = new RegExp('(' + keywords + ')', 'gi');
    const elementsToSearch = document.querySelectorAll('.product-item h3, .product-item span');
    
    elementsToSearch.forEach(element => {
        element.innerHTML = element.innerHTML.replace(
            regex,
            '<mark style="background-color: #ffeb3b; padding: 2px 4px; border-radius: 3px;">$1</mark>'
        );
    });
}

/**
 * Thêm hiệu ứng ripple khi click
 */
const style = document.createElement('style');
style.textContent = `
    .add-to-cart {
        position: relative;
        overflow: hidden;
    }
    
    .ripple {
        position: absolute;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple-animation 0.6s ease-out;
        pointer-events: none;
    }
    
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    .search-page .product-item {
        overflow: hidden;
    }
    
    .search-page .product-item figure {
        overflow: hidden;
    }
    
    .search-page .product-item img {
        transition: transform 0.3s ease;
    }
    
    .search-page .product-item:hover img {
        transform: scale(1.05);
    }
`;
document.head.appendChild(style);

/**
 * Thêm thông báo khi thêm vào giỏ thành công
 */
document.addEventListener('submit', function(e) {
    if (e.target.querySelector('input[name="add_to_cart"]')) {
        const bookName = e.target.closest('.product-item')?.querySelector('h3')?.textContent || 'Sách';
        console.log('✅ Đã thêm "' + bookName + '" vào giỏ hàng');
    }
}, true);
