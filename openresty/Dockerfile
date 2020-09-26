FROM openresty/openresty:1.15.8.2-7-alpine-fat

ENV TZ=Asia/Tehran
RUN apk add -U tzdata && cp /usr/share/zoneinfo/$TZ /etc/localtime
RUN apk add git && luarocks install luasocket \
 && luarocks install inspect \
 && luarocks install httpclient \
 && luarocks install json-lua \
 && luarocks install murmurhash3 \
 && luarocks install lua-resty-jwt

RUN mkdir -p /var/log/nginx