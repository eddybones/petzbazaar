CREATE TABLE stats (
    `key` VARCHAR(255) NOT NULL,
    `value` JSON,
    created DATETIME NOT NULL DEFAULT NOW()
);

INSERT INTO stats (key, value) VALUES
('clawmachine', '{"plays": 0, "coinz": 0}'),
('boutique_purchases', '{"total_purchases": 0, "total_quartz": 0}'),
('coinz_purchases', '{"total_purchases": 0, "total_coinz": 0}');
