# To-Do List — Microwave E-commerce

Tài liệu định hướng quy trình xây dựng website, dựa trên context ở `../context/` (đọc `../context/README.md`, specs trong `../context/spec/*`, chức năng tổng quan trong `../context/chucnangchinh/*`).

## 1) Phạm vi & Ưu tiên (MVP → Mở rộng)
- Chức năng chính (MVP):
  - Danh mục & Sản phẩm (list, detail, filter/search theo thông số); tồn kho cơ bản.
  - Giỏ hàng; Checkout; Đơn hàng; Thanh toán COD; Theo dõi trạng thái đơn.
  - Người dùng: đăng ký/đăng nhập; hồ sơ; địa chỉ giao hàng.
- Chức năng phụ (mở rộng): Wishlist, Đánh giá, Voucher/Khuyến mãi, Loyalty, CMS pages, Thống kê/Báo cáo, Thông báo/Email, Refund, Webhook, Admin dashboard.

## 2) Quy trình làm việc
1. Đọc spec: `spec/models` (mô hình), `spec/Api` (OpenAPI), `spec/Rules` (rules), `spec/Flows` (flows).
2. Chuẩn hóa phạm vi MVP từ `chucnangchinh/dac-ta-thong-nhat.md` và `spec.md`.
3. Mapping models → migrations/Eloquent; API → routes/controllers; flows → test cases.
4. Triển khai theo module; viết test trước cho các flow quan trọng; cập nhật spec nếu thay đổi nghiệp vụ.
5. PR nhỏ, tập trung; dẫn chiếu file spec liên quan; CI chạy `php artisan test`.

## 3) Tài liệu cần bổ sung/khóa cấu hình
- Quy tắc filter/search sản phẩm chi tiết; tham số kỹ thuật bắt buộc hiển thị.
- Chính sách COD (quy trình xác nhận, trạng thái); phí vận chuyển; SLA giao hàng.
- Quy tắc voucher (stacking, phạm vi, hết hạn); tiers loyalty; quy trình refund.
- Wireframe/UI kit; nội dung CMS; SEO (slug/meta); email provider, domain sending.
- Lựa chọn auth token (Sanctum/JWT) cho MVP; quyết định Admin (Filament) giai đoạn nào.

## 4) Tasks khởi động sinh code
- [ ] Môi trường: `cp .env.example .env` → `php artisan key:generate`; cấu hình DB.
- [ ] Migrations (từ `spec/models`): `products`, `brands`, `categories`, `product_stock`, `users`, `addresses`, `carts`, `cart_items`, `orders`, `order_items`, `payments`, `shipments`.
- [ ] Seeders mẫu: brand/category/product với dữ liệu tối thiểu để demo.
- [ ] Catalog API & Web: routes/controller theo `spec/Api/products.openapi.yaml`; trang list/detail (Blade + Vite/Tailwind).
- [ ] Cart/Checkout: service + endpoints theo `spec/Flows/user_checkout.md` và `spec/Rules/cart_and_order_rules.md`.
- [ ] Đơn hàng + trạng thái: model events/log; chuyển trạng thái theo `spec/Flows/order_management.md`.
- [ ] Thanh toán COD: quy trình xác nhận (admin) theo rule; cập nhật `paid/delivered`.
- [ ] Auth: đăng ký/đăng nhập + profile; cân nhắc Sanctum cho MVP.
- [ ] Tests: Feature cho flows (checkout, order); Unit cho services (pricing/stock).
- [ ] Admin (tùy chọn): scaffold quản trị sản phẩm/đơn; có thể tích hợp Filament sau khi ổn định mô hình.

Gợi ý lệnh: `php artisan make:model Product -m`, `php artisan migrate`, `php artisan make:controller ProductController --resource`, `php artisan test`.
