# eHD XSS Header
<IfModule mod_headers.c>
     Header set X-Frame-Options "SAMEORIGIN"
     Header always set X-Content-Type-Options "nosniff"
     Header set X-XSS-Protection "1; mode=block"
</IfModule>
# eHD XSS Header END
