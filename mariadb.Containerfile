FROM registry.access.redhat.com/rhel8/mariadb-105

COPY database/perpustakaan.sql /docker-entrypoint-initdb.d/perpustakaan.sql
