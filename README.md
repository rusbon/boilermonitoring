# Boiler Monitoring

for KiKi

## Database Configuration

Ubah nama database, username, dan password dalam file database-config.php

Pada database yang dituju, jalankan command sql berikut
```sql
CREATE TABLE `log` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`level` TINYINT NOT NULL,
	`pump_control` VARCHAR(3) NOT NULL,
	`date` DATE NOT NULL,
	`time` TIME NOT NULL,
	KEY `date` (`date`) USING BTREE,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;
```

## Server Configuration

- Masukkan folder ke dalam file server.

- NPM dan NodeJS diperlukan untuk menjalankan realtime database. Pertama jalankan command berikut untuk menginstall dependency
```bash
npm install
```

kemudian jalankan server menggunakan Node JS
```bash
node realtime_database_listener.js
```
