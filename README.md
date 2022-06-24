# rudl-ingress-nginx
Nginx ingress controller


## Configuration

| Environment Variable      | Description |
|---------------------------|-------------|
| RUDL_GITDB_URL            | The full url to GitDb Service (https://some.tld or http://gitdb-service) |
| RUDL_GITDB_CLIENT_ID      | The client id of this service (as defined in gitdb.conf.yml in your repository) |
| RUDL_GITDB_CLIENT_SECRET  | The client secret. Can be loaded from file by prefixing: `file:/var/run/secrets/secret_name` |
| INGRESS_SCOPE             | The scope to look up ingress object (default: ingress) |
| INGRESS_OBJECT_NAME       | The object name inside INGRESS_SCOPE to load config (default: ingress.nginx.yml) |
| SSL_CERT_SCOPE            | The scope to sync ssl-certificates (*.pem - objects) from                        |
| SSL_CERT_ISSUER_URL       | The full url to the issuer service (http://cert_issuer)     |


