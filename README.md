# GOP Full Stack Project

## Mô tả
Dự án này kết hợp:
- `frontend/`: giao diện Vue 3 + Pinia
- `backend/`: API Laravel

## Chạy nhanh
1. Cài dependencies cho frontend:
```bash
cd frontend
npm install
```
2. Cài dependencies cho backend:
```bash
cd backend
composer install
```
3. Tạo file `.env` trong backend và sửa cấu hình DB, sau đó:
```bash
php artisan key:generate
php artisan migrate
```
4. Quay về gốc dự án và cài root package để chạy đồng thời:
```bash
cd ..
npm install
```
5. Chạy cùng lúc frontend và backend:
```bash
npm run dev
```

## Môi trường
- Frontend dùng biến môi trường trong `frontend/.env.example`
- Backend dùng `backend/.env.example` và `FRONTEND_URL` để cấu hình CORS

## API
- Frontend gọi API backend tại `http://127.0.0.1:8000/api`
- Login: `POST /api/login`
- Lấy user hiện tại: `GET /api/me`
- Logout: `POST /api/logout`

## Build & Deploy
- Frontend sẽ build vào `backend/public/frontend`
- Khi build xong, SPA có thể truy cập tại `http://localhost:8000/`
- Frontend assets sẽ được lưu tại `backend/public/frontend/assets`

### Build production
```bash
cd gop
npm install
npm run build
```

### Chạy production local
- Backend: `cd gop/backend && php artisan serve --host=127.0.0.1 --port=8000`
- Mở trình duyệt vào `http://127.0.0.1:8000/`
