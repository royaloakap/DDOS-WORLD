CREATE TABLE `users` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `username` TEXT NOT NULL,
    `key` BLOB NOT NULL,
    `salt` BLOB NOT NULL,
    `roles` TEXT NOT NULL,
    `expiry` INTEGER NOT NULL,
    `concurrents` INTEGER DEFAULT 1,
    `servers` INTEGER DEFAULT 1,
    `duration` INTEGER DEFAULT 60,
    `balance` INTEGER DEFAULT 0,
    `membership` TEXT NOT NULL DEFAULT "free"
);

CREATE TABLE `attacks` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `method` TEXT NOT NULL,
    `target` TEXT NOT NULL,
    `port` INTEGER DEFAULT 0,
    `threads` INTEGER DEFAULT 3,
    `pps` INTEGER DEFAULT 250000,
    `parent` INTEGER NOT NULL,
    `duration` INTEGER,
    `type` INTEGER DEFAULT 1,
    `stopped` INTEGER DEFAULT 0,
    `date` INTEGER NOT NULL
);

CREATE TABLE `news` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `title` TEXT NOT NULL,
    `from` TEXT NOT NULL,
    `content` TEXT NOT NULL,
    `date` INTEGER NOT NULL
);

create table `sales` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `uniqid` TEXT NOT NULL,
    `amount` INTEGER NOT NULL,
    `crypto_amount` REAL NOT NULL,
    `crypto_address` TEXT NOT NULL,
    `recieved` REAL NOT NULL,
    `coin` TEXT NOT NULL,
    `status` TEXT NOT NULL,
    `product` TEXT NOT NULL,
    `parent` INTEGER NOT NULL,
    `date` INTEGER NOT NULL
);