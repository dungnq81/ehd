# XSS Header
<IfModule mod_headers.c>
     Header always set X-Content-Type-Options "nosniff"
     Header set X-XSS-Protection "1; mode=block"
</IfModule>
# XSS Header END
