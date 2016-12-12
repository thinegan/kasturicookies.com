server {
    server_name  www.kasturicookies.com;
    rewrite ^ https://$server_name$request_uri? permanent;
}


server {
	server_name www.kasturicookies.com;
	listen 443;
	add_header Strict-Transport-Security "max-age=31536000; includeSubDomains";
	ssl_stapling on;
	ssl_stapling_verify on;
	ssl_trusted_certificate /etc/ssl/certs/stapling.trusted.crt;
	resolver 8.8.8.8 8.8.4.4 valid=300s;
	resolver_timeout 5s;
	ssl on;
	ssl_session_timeout 10m;
	ssl_protocols TLSv1.2;
	ssl_prefer_server_ciphers on;
	ssl_ciphers "ECDH+AESGCM:ECDH+AES256:ECDH+AES128:DH+3DES:RSA+3DES:!RC4:HIGH:!ADH:!AECDH:!MD5";
	ssl_certificate /etc/ssl/certs/kasturicookies.com.crt;
	ssl_certificate_key /etc/ssl/private/kasturicookies.com.key;
	ssl_session_cache shared:SSL:10m;
	
	root /home/www/public_html/www.kasturicookies.com/;
	index index.php index.html index.htm;

	location / {
		#try_files $uri $uri.html $uri/ =404;
		#try_files $uri $uri/ /index.php?q=$request_uri;
		try_files $uri $uri/ /index.php?$args;

		###################################
		# Security Check
		###################################

		## Block some nasty robots
		if ($http_user_agent ~ (Purebot|Baiduspider|Lipperhey|Mail.Ru|scrapbot) ) {
			return 403;
		}

		## Deny referal spam
		if ($http_referer ~* (jewelry|viagra|nude|girl|nudit|casino|poker|porn|sex|teen|babes) ) {
			return 403; 
		}

		include proxy.conf;
	}

	location ~* .(jpg|jpeg|png|css|js|ico|gif|eot|ttf|woff|svg|otf|webm)$ {
		root /home/www/public_html/www.kasturicookies.com/;
		access_log off;
		log_not_found off;
		expires 365d;
		add_header Cache-Control public;
		proxy_cache_valid any 1m;
		proxy_cache_valid 200 304 12h;
		proxy_cache_valid 302 301 12h;
		proxy_cache_key $host$uri#is_args$args;

		if ($request_filename ~* ^.*?.(eot)|(ttf)|(woff)|(svg)|(otf)|(webm)$){
             		add_header Access-Control-Allow-Origin *;
          	}
	}

	location ~ \.php$ {
	  try_files $uri =404;
	  fastcgi_split_path_info ^(.+\.php)(/.+)$;
	  fastcgi_pass unix:/var/run/php5-fpm.sock;
	  fastcgi_index index.php;
	  fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
	  include fastcgi_params;
          fastcgi_read_timeout 300;
	}   

	location /nginx_status {
		stub_status on;
		access_log off;
		# only allow localhost access
		allow 127.0.0.1;
		deny all;
	}

}

