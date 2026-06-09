FROM mariadb:latest

COPY database/perpustakaan.sql /docker-entrypoint-initdb.d/perpustakaan.sql