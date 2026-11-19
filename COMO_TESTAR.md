**1. Instalar as dependências e inicializar o Yii2**

Dentro da pasta do projeto, abrir o CMD:

composer install

php init --env=Development



**2. Importar o SQL**

No MySQL Workbench, cria uma base de dados vazia. Depois, em Server -> Data Import, escolhe Import from Self-Contained File, seleciona o SQL da Etapa 1 e indica o nome da base dados criada como destino.



**3. Ligar à base de dados**

Abre common/config/main-local.php. Dentro de components -> db, ajusta estes valores e mantém o resto do ficheiro.

'dsn' => 'mysql:host=127.0.0.1;port=3306;dbname=nome\_base\_de\_dados',

'username' => 'O\_TEU\_UTILIZADOR\_MYSQL',

'password' => 'A\_TUA\_PASSWORD\_MYSQL',



**4. Abrir e testar a aplicação**

frontend:

http://localhost/SmartLiving\_Etapa1\_PLSI/frontend/web/index.php



backend:

http://localhost/SmartLiving\_Etapa1\_PLSI/backend/web/index.php?r=site%2Findex



Credenciais de teste para utilizador comum:

username: teste1

email: teste1@gmail.com

password: teste1\_pass



Credenciais de teste para administrador:

username: admin

email: admin@gmail.com

password: admin\_pass

