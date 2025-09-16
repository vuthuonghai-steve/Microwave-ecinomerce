<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `orders` MODIFY `payment_method` ENUM('cod','vnpay') DEFAULT 'cod'");
            DB::statement("ALTER TABLE `payments` MODIFY `provider` ENUM('cod','vnpay') DEFAULT 'cod'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TYPE orders_payment_method_enum ADD VALUE IF NOT EXISTS 'vnpay'");
            DB::statement("ALTER TYPE payments_provider_enum ADD VALUE IF NOT EXISTS 'vnpay'");
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF;');

            DB::statement(<<<SQL
CREATE TABLE orders_tmp (
    id integer primary key autoincrement,
    user_id integer not null,
    code varchar(255) not null unique,
    status varchar(20) not null default 'pending' CHECK(status in ('pending','processing','packed','shipping','delivered','cancelled')),
    payment_status varchar(10) not null default 'unpaid' CHECK(payment_status in ('unpaid','paid')),
    payment_method varchar(20) not null default 'cod' CHECK(payment_method in ('cod','vnpay')),
    subtotal numeric(12, 2) not null,
    discount_total numeric(12, 2) not null default 0,
    shipping_fee numeric(12, 2) not null default 0,
    grand_total numeric(12, 2) not null,
    shipping_address_id integer not null,
    notes text null,
    created_at datetime null,
    updated_at datetime null,
    foreign key(user_id) references users(id) on delete cascade,
    foreign key(shipping_address_id) references addresses(id) on delete cascade
);
SQL);

            DB::statement('INSERT INTO orders_tmp (id, user_id, code, status, payment_status, payment_method, subtotal, discount_total, shipping_fee, grand_total, shipping_address_id, notes, created_at, updated_at) SELECT id, user_id, code, status, payment_status, payment_method, subtotal, discount_total, shipping_fee, grand_total, shipping_address_id, notes, created_at, updated_at FROM orders;');
            DB::statement('DROP TABLE orders;');
            DB::statement('ALTER TABLE orders_tmp RENAME TO orders;');
            DB::statement('CREATE UNIQUE INDEX orders_code_unique ON orders(code);');

            DB::statement(<<<SQL
CREATE TABLE payments_tmp (
    id integer primary key autoincrement,
    order_id integer not null,
    provider varchar(20) not null default 'cod' CHECK(provider in ('cod','vnpay')),
    amount numeric(12, 2) not null,
    txn_code varchar(255) null,
    status varchar(20) not null default 'initiated' CHECK(status in ('initiated','succeeded','failed')),
    paid_at datetime null,
    created_at datetime null,
    updated_at datetime null,
    foreign key(order_id) references orders(id) on delete cascade
);
SQL);

            DB::statement('INSERT INTO payments_tmp (id, order_id, provider, amount, txn_code, status, paid_at, created_at, updated_at) SELECT id, order_id, provider, amount, txn_code, status, paid_at, created_at, updated_at FROM payments;');
            DB::statement('DROP TABLE payments;');
            DB::statement('ALTER TABLE payments_tmp RENAME TO payments;');
            DB::statement('CREATE UNIQUE INDEX payments_order_id_unique ON payments(order_id);');

            DB::statement('PRAGMA foreign_keys=ON;');
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `orders` MODIFY `payment_method` ENUM('cod') DEFAULT 'cod'");
            DB::statement("ALTER TABLE `payments` MODIFY `provider` ENUM('cod') DEFAULT 'cod'");
        } elseif ($driver === 'pgsql') {
            DB::statement("DELETE FROM pg_enum WHERE enumlabel='vnpay' AND enumtypid = 'orders_payment_method_enum'::regtype");
            DB::statement("DELETE FROM pg_enum WHERE enumlabel='vnpay' AND enumtypid = 'payments_provider_enum'::regtype");
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF;');

            DB::statement(<<<SQL
CREATE TABLE orders_tmp (
    id integer primary key autoincrement,
    user_id integer not null,
    code varchar(255) not null unique,
    status varchar(20) not null default 'pending' CHECK(status in ('pending','processing','packed','shipping','delivered','cancelled')),
    payment_status varchar(10) not null default 'unpaid' CHECK(payment_status in ('unpaid','paid')),
    payment_method varchar(20) not null default 'cod' CHECK(payment_method in ('cod')),
    subtotal numeric(12, 2) not null,
    discount_total numeric(12, 2) not null default 0,
    shipping_fee numeric(12, 2) not null default 0,
    grand_total numeric(12, 2) not null,
    shipping_address_id integer not null,
    notes text null,
    created_at datetime null,
    updated_at datetime null,
    foreign key(user_id) references users(id) on delete cascade,
    foreign key(shipping_address_id) references addresses(id) on delete cascade
);
SQL);

            DB::statement('INSERT INTO orders_tmp (id, user_id, code, status, payment_status, payment_method, subtotal, discount_total, shipping_fee, grand_total, shipping_address_id, notes, created_at, updated_at) SELECT id, user_id, code, status, payment_status, payment_method, subtotal, discount_total, shipping_fee, grand_total, shipping_address_id, notes, created_at, updated_at FROM orders;');
            DB::statement('DROP TABLE orders;');
            DB::statement('ALTER TABLE orders_tmp RENAME TO orders;');
            DB::statement('CREATE UNIQUE INDEX orders_code_unique ON orders(code);');

            DB::statement(<<<SQL
CREATE TABLE payments_tmp (
    id integer primary key autoincrement,
    order_id integer not null,
    provider varchar(20) not null default 'cod' CHECK(provider in ('cod')),
    amount numeric(12, 2) not null,
    txn_code varchar(255) null,
    status varchar(20) not null default 'initiated' CHECK(status in ('initiated','succeeded','failed')),
    paid_at datetime null,
    created_at datetime null,
    updated_at datetime null,
    foreign key(order_id) references orders(id) on delete cascade
);
SQL);

            DB::statement('INSERT INTO payments_tmp (id, order_id, provider, amount, txn_code, status, paid_at, created_at, updated_at) SELECT id, order_id, provider, amount, txn_code, status, paid_at, created_at, updated_at FROM payments;');
            DB::statement('DROP TABLE payments;');
            DB::statement('ALTER TABLE payments_tmp RENAME TO payments;');
            DB::statement('CREATE UNIQUE INDEX payments_order_id_unique ON payments(order_id);');

            DB::statement('PRAGMA foreign_keys=ON;');
        }
    }
};
