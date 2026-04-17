PROMPT: XÂY DỰNG HỆ THỐNG QUẢN LÝ HỌC THÊM (LARAVEL 11 & VUE 3)
Role: Bạn là một Fullstack Developer chuyên gia, thành thạo Laravel 11 (PHP 8.3) hệ điều hành window 11, Spatie Permission, Sanctum và Vue 3 (Composition API) với Tailwind CSS.

Task: Xây dựng hệ thống quản lý học thêm với phân quyền 3 tầng: Admin (Toàn quyền), Giáo viên (Điểm danh), Học sinh (Xem lịch).

1. BACKEND: KIẾN TRÚC API (LARAVEL 11)
Hãy viết mã nguồn cho các phần sau:

Database & Migrations: - Bảng users (tích hợp Spatie Roles).

Bảng classes (id, name, teacher_id).

Bảng schedules (id, class_id, start_time, room).

Bảng attendances (id, schedule_id, student_id, is_present).

Bảng pivot class_user để quản lý học sinh trong lớp.

Models & Relationships: Định nghĩa đầy đủ các mối quan hệ hasMany, belongsTo, belongsToMany.

Authorization (Phân quyền):

Sử dụng Spatie Laravel-Permission.

Cấu hình Gate::before trong AuthServiceProvider cho Admin.

Viết AttendancePolicy để Giáo viên chỉ được điểm danh lớp mình dạy.

Controllers:

AuthController: Đăng ký, Đăng nhập (trả về Token Sanctum và Role).

AttendanceController: Hàm store điểm danh cho Giáo viên.

ScheduleController: Hàm index lọc lịch học theo đúng học sinh đang đăng nhập.

Seeders: Viết DatabaseSeeder.php tạo sẵn 3 tài khoản: admin@test.com, teacher@test.com, student@test.com (mật khẩu: password) và dữ liệu lớp học mẫu.

2. FRONTEND: GIAO DIỆN VÀ TƯƠNG TÁC (VUE 3 + TAILWIND CSS)
Hãy xây dựng các trang Resource sau (sử dụng Fetch API, không dùng thư viện ngoài nếu không cần thiết):

Trang Đăng Nhập: Form Tailwind đẹp, lưu Token và Role vào LocalStorage.

Trang Dashboard Tổng: Phân loại UI dựa trên Role:

Admin: Hiển thị thống kê tổng quát (Số học sinh, giáo viên).

Giáo Viên: Danh sách lớp đang dạy và nút bấm "Điểm danh".

Học Sinh: Danh sách lịch học cá nhân.

Logic Fetch: Viết một hàm helper xử lý Fetch API có đính kèm Header Authorization: Bearer <token> tự động.

3. YÊU CẦU VỀ CODE & FORMAT:
Code Backend phải có Type Hinting rõ ràng.

Response API trả về dạng JSON chuẩn: { "status": "success", "data": [...] }.

Sử dụng Tailwind CSS để giao diện hiện đại, sạch sẽ, chuẩn Mobile Responsive.

Viết hướng dẫn ngắn gọn các lệnh Artisan cần chạy để cài đặt dự án.

Hãy bắt đầu triển khai từng phần một cách chi tiết nhất!