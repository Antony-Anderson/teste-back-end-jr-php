# Instalações

É necessário ter instalado o php 8.2, symfony 7.1.3, banco de dados (mysql, postgress, etc..) e postman ou insomnia para testar as rotas. 

# Passo a passo

- **Passo 1**: git clone https://github.com/Antony-Anderson/teste-back-end-jr-php.git
- **Passo 2**: cd .\teste-back-end-jr-php\
- **Passo 3**: cd .\app\ 
- **Passo 4**: Configuração do Banco de Dados, adicione as configurações do banco de dados no arquivo `.env` ou crie um arquivo `.env.local`, é necessário alterar para suas configurações, mas aqui está um exemplo:

```env
DATABASE_URL="postgresql://postgres:root@127.0.0.1:5432/atendimento?serverVersion=12.19 (Debian 12.19-1.pgdg120+1)&charset=utf8" 
```
- **Passo 5**: composer install
- **Passo 6**: symfony server:start

# Rotas

No postman ou insomnia poderá ser testado as seguintes rotas:

#### Observação:
Onde tiver {id} significa que tem que passar o número do id do determinado exemplo

- **Prefixo**: http://127.0.0.1:8000

### Beneficiário

- **GET**: `/beneficiario`
- **POST** (store): `/beneficiario/store`
- **PUT** (update): `/beneficiario/update/{id}`
- **DELETE** (destroy): `/beneficiario/destroy/{id}`

Campos:  nome, email, data_nascimento

### Hospital

- **GET**: `/hospital`
- **POST** (store): `/hospital/store`
- **PUT** (update): `/hospital/update/{id}`
- **DELETE** (destroy): `/hospital/destroy/{id}`

Campos:  nome, endereco

### Médico

- **GET**: `/medico`
- **POST** (store): `/medico/store`
- **PUT** (update): `/medico/update/{id}`
- **DELETE** (destroy): `/medico/destroy/{id}`

Campos:  nome, hospital_id

### Consulta

- **GET**: `/consulta`
- **POST** (store): `/consulta/store`
- **PUT** (update): `/consulta/update/{id}`
- **DELETE** (destroy): `/consulta/destroy/{id}`

Campos:  data, status, beneficiario_id, medico_id, hospital_id
