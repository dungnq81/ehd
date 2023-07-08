# Https Forced
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTP:X-Forwarded-Proto} !https
    RewriteCond %{HTTPS} off
    RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
    RewriteRule ^(.*)$ https://%1/$1/ [R=301,L]
</IfModule>
<IfModule mod_headers.c>
    Header set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
    Header always set Content-Security-Policy "upgrade-insecure-requests;"
</IfModule>
# Https Forced END