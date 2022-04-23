FROM nginx:1.19.5-alpine
COPY . /var/www
RUN rm /etc/nginx/conf.d/default.conf
COPY docker-compose/nginx/nginx.conf /etc/nginx/conf.d/default.conf
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
