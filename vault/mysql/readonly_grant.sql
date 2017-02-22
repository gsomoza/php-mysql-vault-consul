CREATE USER '{{name}}'@'%' IDENTIFIED BY '{{password}}';
GRANT ALL PRIVILEGES ON myapp.* TO '{{name}}'@'%';
FLUSH PRIVILEGES;
