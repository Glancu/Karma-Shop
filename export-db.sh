# dump db from docker container
(docker exec -i karma-shop-mysql mysqldump -uroot -proot karma-shop) > docker/karma-shop.sql
