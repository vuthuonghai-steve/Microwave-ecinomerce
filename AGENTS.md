# lư ý khi phản hồi 
- vi người dùng là lập trình viên Việt Nam nên ưu tiên đầu ra phản hồi là tiếng việt.

# Repository Guidelines

## Tóm Tắt Ưu Điểm & Nhược Điểm
- Ưu điểm: bao quát cấu trúc → quy trình; tích hợp `../context` (spec/models, Api, Rules, Flows); nhấn mạnh workflow ưu tiên và bảo mật.
- Nhược điểm: thiếu hướng dẫn xử lý lỗi (migrate/seed); chưa nêu rõ cách AI duyệt file bằng công cụ; thiếu gợi ý CI/CD; một số lệnh chưa kèm ví dụ output.

## Cấu Trúc Dự Án & Tổ Chức Module
- Laravel: mã ở `app/` (controllers `App\Http\Controllers`, models `App\Models`).
- Routes: `routes/web.php`, `routes/api.php`. Views/assets: `resources/` (Blade, Vite/Tailwind).
- Database: `database/` (migrations, seeders, factories). Tests: `tests/` (`Feature`, `Unit`). Public root: `public/`.
- Context ngoài repo: đọc `../context/README.md`. Specs tại `../context/spec/{models,Api,Rules,Flows}`; chức năng tổng quan ở `../context/chucnangchinh/`. Nếu có, tham chiếu thêm `../context/Design_database/database.json`.

## Quy Trình Ưu Tiên (AI Agent)
- Đọc `TODO.md` của repo → chốt phạm vi và thứ tự thực thi (MVP trước, mở rộng sau).
- spec duoc dat trong `../context` . Các file spec nằm trong `../context/spec/{models,Api,Rules,Flows}`.
- Chức năng tổng quan ở `../context/chucnangchinh/`. Nếu có, tham chiếu thêm `../context/Design_database/database.json`.
- Duyệt specs và ghi rõ nguồn: models→migrations/Eloquent; Api→routes/controllers; Rules/Flows→test cases.
- Thiếu dữ liệu → hỏi bổ sung (tránh “đoán mò”). PR nhỏ, tập trung; luôn kèm test và dẫn chiếu file spec liên quan.
 - Template xác nhận trước khi code: "Duyệt files: [danh sách]. Hướng: [tóm tắt]. Có xác nhận triển khai?".
 - Sau khi triển khai: chạy full test suite và tạo coverage report nếu khả dụng.

## Lệnh Build, Test, Dev (kèm output kỳ vọng)
- Cài đặt: `composer install` + `npm install`. 
- Cấu hình: `cp .env.example .env` → `php artisan key:generate`.
- Dev all-in-one: `composer run dev` (server, queue, logs, Vite chạy song song).
- DB: `php artisan migrate` → thấy “Migrating: …”, “Migrated: …”; seed: `php artisan db:seed`.
- Test: `php artisan test` → “OK (xx tests, xx assertions)” hoặc báo failed để xử lý.
 - Vite port conflict: chạy `npm run dev -- --port 5174` (đổi cổng) nếu cổng mặc định bận.

## Xử Lý Lỗi & Phục Hồi Nhanh
- Unknown database/Access denied: tạo DB, kiểm tra `DB_*` trong `.env`, quyền user; chạy lại `php artisan migrate`.
- Lỗi khóa ngoại: kiểm tra thứ tự migrations/quan hệ; local có thể dùng `php artisan migrate:fresh --seed` (không dùng trên prod).
- Trùng unique (slug/code): sửa factory/seed sinh giá trị unique (vd. `Str::random()` ghép slug).
- Khi cập nhật schema: tạo migration mới (không sửa file migration đã chạy trên môi trường dùng chung).
 - Xem log: `storage/logs/laravel.log` để tra lỗi chi tiết khi migrate/seed/test thất bại.

## Công Cụ & Cách Duyệt Tài Liệu
- Liệt kê: PowerShell `Get-ChildItem -Recurse ../context/spec`.
- Đọc nhanh: `Get-Content -TotalCount 200 ../context/spec/models/product.yaml`.
- Tìm kiếm: ưu tiên `rg -n "keyword"` (nếu có); thay thế bằng `Select-String -Path ../context/** -Pattern keyword`.
- Kiểm tra môi trường: `php artisan about` (phiên bản, drivers, cache/queue/session).

## Quy Ước Mã & Đặt Tên
- PSR-12; 4 spaces; UTF-8; LF. Lớp `PascalCase`, model số ít, bảng/cột `snake_case`, Blade `kebab-case` khi phù hợp.
- Lint/format: `./vendor/bin/pint` (check-only: `--test`). Specs YAML/MD dùng `kebab-case`, H1 mở đầu.

## Hướng Dẫn Kiểm Thử
- PHPUnit: `php artisan test`; ưu tiên `RefreshDatabase` cho DB; dùng factories trong `database/factories`.
- Viết test theo `../context/spec/Flows/*` & `../context/spec/Rules/*`; đặt tên `SomethingTest.php`.
 - Mục tiêu coverage: ≥ 80% cho business-critical (checkout, orders, payments). Thêm integration tests cho API JSON.
 - Ví dụ (rút gọn):
   ```php
   // tests/Feature/CartTest.php
   use Illuminate\Foundation\Testing\RefreshDatabase;
   class CartTest extends TestCase {
     use RefreshDatabase;
     public function test_add_product_to_cart() {
       $p = \App\Models\Product::factory()->create();
       $res = $this->postJson('/api/cart/add', ['product_id' => $p->id]);
       $res->assertStatus(201);
     }
   }
   ```

## Commit & Pull Request
- Conventional Commits (vd: `feat: catalog filters`, `fix: migrate fk order`).
- PR: mô tả rõ, liên kết issue (`Closes #123`), bước tái hiện, screenshot UI, log lỗi (nếu có), đường dẫn file spec tham chiếu.
 - Nhánh: `feature/<slug>`, `fix/<slug>`, `chore/<slug>`. PR checklist: [ ] tests pass, [ ] cập nhật docs/spec nếu đổi nghiệp vụ, [ ] ảnh chụp UI (nếu có), [ ] migration thứ tự/rollback OK.

## Bảo Mật & Cấu Hình
- Không commit secrets/.env. `APP_KEY` chỉ tạo khi setup. Trước khi chạy: `php artisan about` → `php artisan migrate --seed`.
 - OAuth/SSO: yêu cầu cung cấp keys (vd. Google) qua biến `.env` trước khi tích hợp; bật rate limiting (`throttle:api`) cho endpoints công khai.

## CI/CD (Gợi Ý Ngắn)
- CI cơ bản (GitHub Actions): PHP 8.2 + MySQL service → `composer install --no-interaction --prefer-dist`, `cp .env.example .env`, `php artisan key:generate`, `php artisan migrate --graceful --seed`, `php artisan test`.
- Bật cache Composer/NPM; không dùng `migrate:fresh` trên prod; secrets cấu hình qua CI secrets.

## Ví Dụ Snippet (tham khảo)
- Migration từ database.json (ví dụ):
  ```php
  Schema::create('products', function (Blueprint $table) {
      $table->id();
      $table->foreignId('category_id')->constrained('categories');
      $table->string('name');
      $table->string('slug')->unique();
      $table->enum('status', ['active','inactive'])->default('active');
      $table->timestamps();
  });
  ```
## Lưu ý khi thực hiện fix bug : 
- đọc log trong đường dẫn sau để hiểu thêm về bug xảy ra : C:\store\HURE\dh12c1\dh12c1\nam_4\project\Laravel\economic-lovisong\microwave-ecommerce\storage\logs\laravel.log