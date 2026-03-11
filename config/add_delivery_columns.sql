-- Run this once if your orders table was created before the delivery feature.
-- Adds: delivered_at, delivery_message (skip if you get "duplicate column" error)
ALTER TABLE orders ADD COLUMN delivered_at TIMESTAMP NULL DEFAULT NULL AFTER status;
ALTER TABLE orders ADD COLUMN delivery_message TEXT DEFAULT NULL AFTER delivered_at;
