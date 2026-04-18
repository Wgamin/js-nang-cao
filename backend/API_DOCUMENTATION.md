# Tài liệu API - Hệ thống Quản lý Học tập (Backend API)

Hệ thống sử dụng **Laravel 11** và **Laravel Sanctum** để xác thực bằng Token.

## 1. Thông tin chung
- **Base URL:** `http://localhost:8000/api`
- **Định dạng dữ liệu:** `application/json`
- **Xác thực:** Header `Authorization: Bearer {token}`

---

## 2. Các Vai trò (Roles)
- `admin`: Toàn quyền.
- `teacher`: Giáo viên (chỉ thấy các lớp mình dạy).
- `parent`: Phụ huynh (chỉ thấy thông tin con cái của mình).

---

## 3. Nhóm Xác thực (Authentication)

### 3.1. Đăng nhập (Login)
- **Method:** `POST`
- **URL:** `/login`
- **Body:**
```json
{
    "email": "admin@example.com",
    "password": "password"
}
```
- **Response (200 OK):**
```json
{
    "access_token": "1|...",
    "user": { "id": 1, "name": "Admin", "role": "admin" }
}
```

### 3.2. Thông tin User hiện tại (Profile)
- **Method:** `GET`
- **URL:** `/me`
- **Auth:** Required
- **Response:** Trả về User + Profile (`teacher`, `guardian`, hoặc `student`).

---

## 4. Nhóm Quản trị (Dành cho Admin)

### 4.1. Quản lý Giáo viên (Teachers)
- **Endpoints:**
  - `GET /teachers`: Liệt kê (Phân trang 10/trang).
  - `POST /teachers`: Tạo mới.
    - Body: `{ "name", "phone", "specialization", "user_id"? }`
  - `GET /teachers/{id}`: Xem chi tiết.
  - `PUT /teachers/{id}`: Cập nhật.
  - `DELETE /teachers/{id}`: Xóa.

### 4.2. Quản lý Môn học (Subjects)
- **Endpoints:**
  - `GET /subjects`: Liệt kê.
  - `POST /subjects`: Tạo mới.
    - Body: `{ "name", "description" }`
  - `GET /subjects/{id}`: Xem chi tiết.
  - `PUT /subjects/{id}`: Cập nhật.
  - `DELETE /subjects/{id}`: Xóa.

---

## 5. Nhóm Nghiệp vụ (Data Scoping)

### 5.1. Quản lý Lớp học (Classes)
- **Method:** `GET` | `POST` | `PUT` | `DELETE`
- **URL:** `/classes`
- **Logic Phân quyền:**
  - **Admin:** Thấy tất cả.
  - **Teacher:** Chỉ thấy lớp do mình dạy (Backend tự động lọc qua Token).
- **Body (Tạo/Sửa):**
```json
{
    "subject_id": 1,
    "teacher_id": 1,
    "name": "Lớp Toán 10A1",
    "base_price": 500000,
    "status": "active"
}
```

### 5.2. Danh sách Học sinh của Phụ huynh (My Children)
- **Method:** `GET`
- **URL:** `/my-children`
- **Auth:** Required (`admin`, `parent`)
- **Logic:**
  - **Parent:** Chỉ trả về danh sách con cái liên kết với tài khoản phụ huynh này.
  - **Admin:** Trả về toàn bộ học sinh.

### 5.3. Đăng ký học (Enrollments)
- **Method:** `POST`
- **URL:** `/enrollments`
- **Auth:** Required (`admin`, `parent`)
- **Body:**
```json
{
    "student_id": 1,
    "class_id": 1,
    "actual_price": 450000,
    "enrolled_at": "2024-04-11"
}
```

---

## 6. Cấu trúc Dữ liệu trả về (Resources)

### 6.1. Học sinh (Student Object)
```json
{
    "id": 1,
    "name": "Nguyễn Văn A",
    "dob": "2010-05-20",
    "gender": "male",
    "guardian": { "id": 1, "name": "Bố Nguyễn Văn A" }
}
```

### 6.2. Lớp học (Class Object)
```json
{
    "id": 1,
    "name": "Toán 10A1",
    "subject": { "id": 1, "name": "Toán" },
    "teacher": { "id": 1, "name": "Thầy Bình" }
}
```

---

## 7. Mã lỗi chuẩn (HTTP Status Codes)
- `200`: Thành công.
- `401`: Chưa đăng nhập / Token sai.
- `403`: Không có quyền (Sai Role).
- `422`: Lỗi dữ liệu đầu vào (Validation Error - Xem chi tiết trong JSON trả về).
- `404`: Không tìm thấy dữ liệu.
