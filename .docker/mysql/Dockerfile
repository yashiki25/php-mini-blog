FROM mysql:5.7

COPY init/create_table.sql /docker-entrypoint-initdb.d/create_table.sql

CMD ["mysqld"]