# Repository Guidelines

## Cấu Trúc Dự Án & Tổ Chức Module
- Mã nguồn Laravel: `app/` (controller ở `App\Http\Controllers`, model ở `App\Models`).
- Tuyến: `routes/web.php` (web) và `routes/api.php` (API).
- Giao diện/asset: `resources/` (Blade `resources/views`, Vite `resources/js`, `resources/css`).
- CSDL: `database/` (migrations, seeders, factories). Kiểm thử ở `tests/` (`Feature`, `Unit`). Public root: `public/`.
- Context ngoài repo: đọc `../context/README.md` trước khi làm. Specs tại `../context/spec/` gồm `models/`, `Api/`, `Rules/`, `Flows/`. Chức năng tổng quan ở `../context/chucnangchinh/`.

## Lệnh Build, Test, Phát Triển
- Cài đặt: `composer install` và `npm install`.
- Cấu hình: `cp .env.example .env` rồi `php artisan key:generate`.
- Chạy tất cả trong dev (PHP, queue, logs, Vite): `composer run dev`.
- Riêng lẻ: `php artisan serve`; Vite: `npm run dev`; build asset: `npm run build`.
- CSDL: `php artisan migrate` (thêm `--seed` để seed).
- Kiểm thử: `composer test` hoặc `php artisan test`.

## Quy Ước Mã & Đặt Tên
- Tuân thủ PSR-12; thụt 4 khoảng; UTF-8; LF.
- Laravel naming: lớp `PascalCase` (vd: `ProductController`), model số ít (`App\Models\Product`), bảng/cột `snake_case`, Blade dùng `kebab-case` khi phù hợp.
- Gom route theo middleware/prefix; controller kết thúc bằng `Controller`.
- Lint/format bằng Laravel Pint: `./vendor/bin/pint` (chỉ kiểm tra: `--test`).
- Trong `../context/spec`, file YAML/Markdown đặt tên `kebab-case`; heading bắt đầu bằng `#`.

## Hướng Dẫn Kiểm Thử
- Dùng PHPUnit qua `php artisan test` với thư mục `tests/Feature` và `tests/Unit`.
- Đặt tên `SomethingTest.php`; ưu tiên `RefreshDatabase` cho test DB; dùng factories ở `database/factories`.
- Sinh test dựa trên `../context/spec/Flows/*` và quy tắc trong `../context/spec/Rules/*`.

## Commit & Pull Request
- Ưu tiên Conventional Commits (vd: `feat: add cart totals`, `fix: prevent double charge`).
- PR cần mô tả rõ, liên kết issue (`Closes #123`), bước tái hiện, screenshot UI nếu có, và test đi kèm.
- Nếu thay đổi theo spec, nêu rõ file liên quan ở `../context/spec/...` hoặc `../context/chucnangchinh/...` trong mô tả PR.

## Bảo Mật & Cấu Hình
- Không commit secrets. Dùng `.env`; khóa `APP_KEY` chỉ tạo khi setup.
- Trước khi chạy: `php artisan about` để kiểm tra môi trường; sau đó `php artisan migrate --seed`.
## quy trinh ưu tiên 
- thực hiện đọc file TODO.md để thực hiện hiểu được công việc cần làm , công việc đang triển khai , công tiếp theo .
- đọc file spec để hiểu rõ các quy tắc nghiệp vụ , các tính năng cần làm , các bước thực hiện .
- thực hiện các bước theo thứ tự từ top xuống bottom , từ cơ bản đến phức tạp .
- nhớ chú thích trong file TODO.md đối với những công việc đang triển khai , chưa triển khai .
- nhớ kiểm tra lại các bước thực hiện để đảm bảo đúng với quy tắc nghiệp vụ và các tính năng cần làm .
- chú ý việc yêu cầu người dùng thực hiện bổ sung thêm thông tin hoặc biến cần thiết lập trước khi bắt tay triển khai một công việc mới . 
    ví dụ : chức năng xác thực gamil cần có các biến key cần dev là mình thực hiện cấu hình bổ sung vì key này cần phải tự thiết lập riêng .
- trong quá trình yêu cầu xây dựng một chức năng / module / feature , cần phải đảm bảo rằng tất cả các bước đều được thực hiện đúng và hợp lệ , không để xảy ra lỗi hoặc bug trong quá trình triển khai . để đảm bảo điều này , cần cho mình biết bạn đã duyệt qua những file tài liệu context nào và spec để hiểu rõ quy tắc nghiệp vụ , các tính năng cần làm , các bước thực hiện . sau khi mình xác nhận bạn đang đi đúng hướng mới thực hiện quá trình tạo sinh code dành cho dự án .
- C:\store\HURE\dh12c1\dh12c1\nam_4\project\Laravel\economic-lovisong\microwave-ecommerce\database\database.json day la file thiet ke cau truc bang danh cho database , enun , pk moi quan he giua cac bang chi tiet .

