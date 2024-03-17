#!/bin/bash

# Start Apache
apache2-foreground &

# Wait for Apache to start
while ! pgrep -x httpd > /dev/null; do sleep 1; done

# Execute PHP scripts
cd /var/www/html/src/Mensageria/
nohup php ObterNovosPedidos.php > /dev/null 2>&1 &
nohup php CriarTransacoes.php > /dev/null 2>&1 &

# Keep container running
exec "$@"
