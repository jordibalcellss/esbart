# Derivando da imagem oficial do MySQL
FROM mysql:5.7

# Adicionando os scripts SQL para serem executados na criação do banco
COPY ./database.sql /docker-entrypoint-initdb.d/
