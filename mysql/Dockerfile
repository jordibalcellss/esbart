# Derivando da imagem oficial do MySQL
FROM mysql:5.7
# Adicionando os scripts SQL para serem executados na criação do banco
COPY ./database.sql /tmp/
CMD [ "mysqld", "--init-file=/tmp/database.sql" ]

#docker compose build --no-cache mysql