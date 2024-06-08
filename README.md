# VUEBI

## Visão geral

É uma aplicação em VueJs e Laravel de um BI de dados de uma transportadora, trazendo visualizações gerenciais das entregas em formato de gráfico e lista.

## Requerimentos
* Python ^8.3
* Docker e Docker Compose (necessário para subir os serviços de dependências: MySQL)

## Como executar

###  Migração do Banco de dados
Após clonar e acessar o projeto, execute o seguinte comando para preparar o banco de dados:

1. Para faciliar a execução local, há um `Makefile` com os comandos prontos. Se seu SO for compatível com comandos `make`, execute o seguinte comando para criar e executar a migração do banco de dados:

```sh
make prepare-database
```

2. Caso seu SO não seja compatível com comandos `make`, execute:
```sh
docker compose up -d mysql

cp backend/.env.example backend/.env

cp ui/.env.example ui/.env

composer install --working-dir=backend

DB_HOST=127.0.0.1 php backend/artisan migrate

DB_HOST=127.0.0.1 php backend/artisan db:seed --class=DeliveriesSeeder
```

### Iniciando os serviços
1. Se seu SO for compatível com comandos `make`, execute o seguinte comando para subir a aplicação front-end, back-end e o banco de dados:

```sh
make up
```

2. Caso seu SO não seja compatível com comandos `make`, execute:
```sh
docker compose up
```

3. Ao subir o conteiner, aguarde alguns instante até que o servidor do Vue esteja pronto.
A UI estará disponivel em [http://localhost:8100/](http://localhost:8100/)

Caso queira inserir mais dados no banco de dados, execute:
```sh
make more-seed
```
ou:
```sh
DB_HOST=127.0.0.1 php backend/artisan db:seed --class=DeliveriesSeeder
```

Caso queira ver todos os comandos `make` disponíveis, execute:
```sh
make help
```

## Conhecendo a Aplicação
O frontend está localizado na pasta `ui`. Se trata de uma aplciação em VueJS 3 utilizando o template [Material Dashboard](https://www.creative-tim.com/learning-lab/bootstrap/overview/material-dashboard).

O backend foi feito em Laravel 11, e está na basta `backend`.

A aplicação depende o Banco de dados MySql, devido à facilidade de algumas funções que ajudam em queries para dashboards como YEAR(), MONTH(), etc.

## Testes
A backend está com cobertura de testes de integração. Siga esses passos se quiser executar o testes localmente:

1. Instale as dependencias com `composer install --working-dir=backend` caso ainda não tenha feito;
2. Acesse a pasta `backend`
3. Execute as migrações para o ambiente de teste:
```sh
php artisan migrate --env=testing
```
4. Execute o Artisan Test com o comando:
```sh
php artisan test
```
