api_block = open('/tmp/nginx_api_location.conf').read()
with open('/etc/nginx/sites-enabled/intsolcom','r') as f:
    config = f.read()
config = config.replace('    rewrite ^/api/blog', api_block + '\n    rewrite ^/api/blog')
with open('/etc/nginx/sites-enabled/intsolcom','w') as f:
    f.write(config)
print('ok')
