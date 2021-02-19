FROM infracamp/kickstart-flavor-php:unstable

ENV DEV_CONTAINER_NAME="rudl-cloudfront"

ENV CONF_PRINCIPAL_SERVICE="rudl-principal"
ENV CONF_NGINX_ERROR_LOG="/var/log/nginx/error.log"
ENV CONF_NGINX_ACCESS_LOG="/var/log/nginx/access.log main"

ENV CONF_CLUSTER_NAME="unnamed"
ENV CONF_METRICS_HOST=""


ADD / /opt
RUN ["bash", "-c",  "chown -R user /opt"]
RUN ["/kickstart/run/entrypoint.sh", "build"]

ENTRYPOINT ["/kickstart/run/entrypoint.sh", "standalone"]
