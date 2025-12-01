# Website_it_me

Một website mẫu được chuyển đổi từ HTML sang PHP thuần, phù hợp để chạy cục bộ trên môi trường WAMP/XAMPP. Tệp này mô tả nhanh mục đích dự án, cách cài đặt và cấu trúc thư mục để bạn dễ tiếp tục phát triển hoặc tùy chỉnh.

## Yêu cầu
- Môi trường web server (WAMP, XAMPP) với PHP (khuyến nghị PHP 7.2+).
- MySQL nếu bạn sử dụng `database.php` để kết nối cơ sở dữ liệu.

## Cài đặt nhanh
1. Sao chép toàn bộ thư mục `Website_it_me` vào thư mục web root của WAMP/XAMPP (ví dụ `C:\wamp\www\Website_it_me`).
2. Mở tệp `database.php` và chỉnh sửa thông tin kết nối (host, username, password, database) nếu cần.
3. Bật WAMP/XAMPP và truy cập: `http://localhost/Website_it_me`.
4. Nếu dự án kèm file SQL để import, import vào MySQL trước khi chạy.

## Cấu trúc thư mục chính
- `index.html` — Trang chính (nếu đã chuyển sang PHP, có thể là `index.php`).
- `database.php` — Tệp cấu hình/kết nối cơ sở dữ liệu.
- `css/` — Chứa các stylesheet: `style.css`, `normalize.css`, `vendor.css`.
- `js/` — Chứa các script: `jquery-1.11.0.min.js`, `modernizr.js`, `plugins.js`, `script.js`, `slideNav.js`.
- `images/` — Thư mục ảnh và thư mục con `chocolat/`.
- `LICENSE` — Thông tin bản quyền/giấy phép (xem file để biết chi tiết).
- `README.md` — Tệp này.

## Tùy chỉnh nhanh
- Thay đổi giao diện: chỉnh `css/style.css` và `css/vendor.css`.
- Thay đổi hành vi/hiệu ứng: chỉnh trong `js/script.js` hoặc các file JS liên quan.
- Ảnh hiển thị: cập nhật trong `images/`.

## Ghi chú
- Nếu bạn muốn chuyển toàn bộ trang thành PHP (ví dụ để include header/footer động), đổi `index.html` thành `index.php` và sử dụng `include`/`require` để tái sử dụng phần đầu/cuối.
- Kiểm tra quyền file và đường dẫn trên môi trường Windows khi gặp lỗi truy cập tài nguyên.

## Liên hệ
Nếu cần hỗ trợ thêm hoặc muốn mình sửa README theo yêu cầu cụ thể (ngôn ngữ, chi tiết cài đặt, thêm hướng dẫn DB), cho mình biết chi tiết thay đổi bạn muốn.
