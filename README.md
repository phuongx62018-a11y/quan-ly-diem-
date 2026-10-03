## BÁO CÁO DỰ ÁN : WEB QUẢN LÝ ĐIỂM
  - Thành viên tham gia : Nguyễn Quốc Bảo + Nguyễn Bá Duy Phương
## 1. Cấu trúc file dự án dự kiến xây dựng đề tài hệ thống quản lý điểm :
quan-ly-diem/
│
├── assets/                 # Thư mục chứa các tài nguyên tĩnh (Giao diện)
│   ├── css/
│   │   └── style.css       # Viết CSS tùy chỉnh giao diện (màu sắc, bố cục)
│   └── js/
│       └── script.js       # Viết JavaScript (nếu cần alert báo lỗi hoặc validate frontend)
│
├── config/                 # Thư mục chứa cấu hình lỗi
│   └── db.php              # Nơi DUY NHẤT chứa code kết nối cơ sở dữ liệu MySQL
│
├── db_export/              #  Để lưu trữ database
│   └── quan_ly_diem.sql    # File script chứa các bảng (users, grades,...)
│
├── index.php               # File mồi: Nếu đã có session thì đẩy vào dashboard.php, chưa có thì đẩy ra login.php
├── login.php               # Giao diện đăng nhập + Logic kiểm tra tài khoản & check ký tự '@'
├── logout.php              # Logic hủy Session đăng nhập và đẩy về trang login
├── dashboard.php           # Trang chủ sau khi đăng nhập (hiển thị tóm tắt, menu phân quyền)
├── quanly_diem.php         # Nơi chứa logic CRUD (Xem, Thêm, Sửa, Xóa điểm của sinh viên)
│
├── README_4_11.md          
## 2. NGÔN NGỮ VÀ CÔNG NGHỆ ỨNG DỤNG
Dự án được xây dựng dựa trên mô hình Client - Server cơ bản, sử dụng các ngôn ngữ dễ kiểm soát và phù hợp để nắm bắt luồng dữ liệu:
- **Backend (Logic Core):** PHP thuần.
- **Cơ sở dữ liệu (Database):** MySQL (thông qua XAMPP). 
- **Frontend (Giao diện):** HTML5, CSS3 và JavaScript thuần.

## 3. CÁC KỸ NĂNG (SKILLS) & CÔNG CỤ TRỢ LÝ (VIBE CODE ERA)
Để đáp ứng yêu cầu lập trình hiện đại, dự án áp dụng tối đa các kỹ năng điều khiển công cụ:

### a) Kỹ năng điều khiển AI Agent (Prompt Engineering)
- **Công cụ:** Cursor / GitHub Copilot / ChatGPT.
- **Kỹ năng áp dụng:**
  - *Generative Code:* Ra lệnh cho AI viết các đoạn mã lặp lại (boilerplate) hoặc các câu lệnh SQL dài.
  - *Explain & Debug:* Sử dụng AI như một người hướng dẫn để giải thích các đoạn mã báo lỗi (Syntax Error, PDO Exception) và đề xuất phương án sửa lỗi tối ưu nhất.

### b) Kỹ năng mô hình hóa (Modeling & Graphify)
- **Công cụ:** Graphify / Mermaid JS / Draw.io.
- **Kỹ năng áp dụng:** Chuyển đổi logic mã nguồn thành hình ảnh trực quan. Thay vì đọc chay code, em sử dụng skill này để vẽ biểu đồ luồng hoạt động (Flowchart).
  - *Ví dụ:* Dựng sơ đồ luồng đăng nhập (từ lúc User nhập form -> kiểm tra ký tự `@` -> truy xuất DB -> cấp quyền vào Dashboard). Việc này giúp em và thành viên trong nhóm thống nhất tư duy trước khi gõ code.

### c) Kỹ năng Kiểm thử độc lập (API/Backend Testing)
- **Công cụ:** Postman.
- **Kỹ năng áp dụng:** Không phụ thuộc vào giao diện HTML (Frontend) để test. Em dùng Postman bắn trực tiếp các HTTP Request (POST/GET) vào các file xử lý như `login.php` hay `quanly_diem.php` kèm theo dữ liệu giả (Mock Data) để kiểm tra xem Backend chặn lỗi (validate) có chính xác hay không.

### d) Kỹ năng Quản trị Mã nguồn & Làm việc nhóm
- **Công cụ:** Git & GitHub.
- **Kỹ năng áp dụng:** 
  -  đọc `git log` để kiểm tra tiến độ, và đọc `git diff` để review chi tiết từng dòng code (thêm/sửa/xóa) của thành viên trước khi chấp nhận tích hợp (merge) vào mã nguồn chính.        
