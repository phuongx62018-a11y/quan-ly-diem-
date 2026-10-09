# BÁO CÁO CÁ NHÂN – WEB QUẢN LÝ ĐIỂM (ngày 11/10/2026)
- Công nghệ: PHP thuần, MySQL (XAMPP), HTML/CSS/JS
## Ý 1. Xây dựng hệ thống với Agent

Công cụ: GitHub Copilot (Agent) + Gemini trong VS Code.

Quy trình em dùng:
1. Chốt cấu trúc thư mục và luồng đăng nhập trước (README_4_11).
2. Giao Agent từng việc nhỏ (ví dụ: `config/db.php`, form đăng nhập, CRUD sinhvien).
3. Đọc lạidif của Agent trước khi chấp nhận, không bấm Accept all.
4. Chạy thử trên XAMPP, chạy được thì commit.

## Ý 2a. Kiểm soát tính năng

| Tính năng | File |
|---|---|
| Kết nối database | `config/db.php` |
| Đăng nhập + ràng buộc mật khẩu có `@` | `login.php` |
| Chặn người chưa đăng nhập | `includes/auth.php`, đầu `dashboard.php` |
| Đăng xuất | `logout.php` |
| Chuyển hướng ban đầu | `index.php` |
| Trang chủ, menu | `dashboard.php` |
| Giao diện chung | `includes/header.php`, `footer.php`, `assets/css/style.css` |
| CRUD sinh viên / môn học / điểm | `sinhvien/`, `monhoc/`, `diem/` (index, add, edit, delete) |
| Cấu trúc database | `db_export/db.sql` |

**Ràng buộc mật khẩu phải có `@`** nằm trong `login.php`, ngay sau khi lấy `$password` từ form:

```php
if (strpos($password, '@') === false) {
    $error = "Mật khẩu bảo mật yếu! Bắt buộc phải chứa ký tự '@'.";
} else {
    // truy vấn DB, đúng thì lưu SESSION và vào dashboard.php
}
```

Muốn đổi yêu cầu chỉ cần sửa điều kiện này, ví dụ thêm độ dài tối thiểu: `strpos($password, '@') === false || strlen($password) < 8`.

## Ý 2b. Skill dùng AI hỗ trợ viết code

Công cụ: GitHub Copilot Chat trong VS Code. Em dùng 3 kỹ năng:

 - Cung cấp ngữ cảnh : Đính kèm file liên quan (ví dụ README.md, login.php) hoặc dùng #tên_file để AI hiểu đúng cấu trúc dự án, không viết code lệch hướng
 - Chọn đúng chế độ	: Dùng Ask khi cần hiểu/giải thích code; dùng Agent khi giao việc sửa file. Không dùng Agent khi chưa hiểu mình muốn sửa gì
 - Explain & Debug : Dán thông báo lỗi kèm đoạn code liên quan, nhờ AI giải thích nguyên nhân rồi mới sửa, sau đó em tự diễn đạt lại bằng lời của mình

## Ý 2c. Log, diff 

| Lệnh | Dùng để |
|---|---|
| `git log --oneline` | Xem lịch sử commit |
| `git log --author="Tên"` | Lọc commit theo người |
| `git shortlog -sn` | Đếm commit từng thành viên |
| `git log --stat` | Mỗi commit sửa bao nhiêu file/dòng |
| `git diff` | Xem chi tiết từng dòng thêm/sửa/xóa |
| `git blame file` | Biết dòng code do ai viết |

## Ý 3. Test
Sử dụng bộ test case cho đăng nhập và CRUD, kết hợp Postman, trình duyệt và phpMyAdmin.
