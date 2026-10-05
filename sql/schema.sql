CREATE TABLE IF NOT EXISTS orders (
 id BIGSERIAL PRIMARY KEY, order_no VARCHAR(40) UNIQUE NOT NULL, qr_token VARCHAR(128) UNIQUE NOT NULL,
 customer_name VARCHAR(150) NOT NULL, customer_mobile VARCHAR(30), total_amount NUMERIC(12,2) NOT NULL CHECK(total_amount>=0),
 status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE' CHECK(status IN ('ACTIVE','DELIVERED','CANCELLED')),
 created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(), delivered_at TIMESTAMPTZ
);
CREATE UNIQUE INDEX IF NOT EXISTS ux_orders_qr_token ON orders(qr_token);
CREATE INDEX IF NOT EXISTS ix_orders_status ON orders(status);
CREATE TABLE IF NOT EXISTS order_items (
 id BIGSERIAL PRIMARY KEY, order_id BIGINT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
 item_name VARCHAR(200) NOT NULL, quantity NUMERIC(12,2) NOT NULL CHECK(quantity>0), rate NUMERIC(12,2) NOT NULL CHECK(rate>=0), amount NUMERIC(12,2) NOT NULL CHECK(amount>=0)
);
CREATE INDEX IF NOT EXISTS ix_order_items_order_id ON order_items(order_id);
