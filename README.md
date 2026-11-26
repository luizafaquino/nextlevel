# Next Level

A Next Level é uma loja virtual de acessórios gamers, desenvolvida como projeto do 4º bimestre do curso técnico em Tecnologia da Informação do Senac.

## Sobre o projeto

O site permite a compra simulada de teclados, mouses, headsets e mousepads, além de possuir uma área administrativa para o gerenciamento da loja.

## Tecnologias utilizadas

- HTML5
- CSS3
- Bootstrap
- JavaScript
- PHP
- MySQL
- PDO
- Git e GitHub

## Funcionalidades

- Catálogo de produtos.
- Busca, filtro por categoria e ordenação.
- Página de detalhes dos produtos.
- Cadastro e login de usuários.
- Carrinho de compras.
- Controle de estoque.
- Finalização de pedidos.
- Pagamento simulado por PIX, boleto e cartão.
- Cadastro, edição e exclusão de produtos.
- Visualização dos pedidos pelo administrador.
- Validação de formulários com JavaScript.

## Como executar

1. Baixe ou clone o repositório.
2. Coloque a pasta nextlevel dentro da pasta htdocs do XAMPP.
3. Inicie o Apache e o MySQL.
4. Importe o arquivo database/schema.sql no phpMyAdmin.
5. Crie o arquivo .env utilizando o .env.example como modelo.
6. Acesse http://localhost/nextlevel/.

## Desenvolvimento da loja

Inicialmente, foi planejada uma loja voltada para o público gamer, com quatro categorias de produtos: teclados, mouses, headsets e mousepads.

A interface foi desenvolvida com um visual escuro, menu lateral e catálogo com opções de pesquisa e ordenação. Com a estrutura do banco de dados pronta, foram implementadas as funcionalidades de cadastro, login, carrinho e finalização de compras.

Na parte administrativa, foram desenvolvidos recursos para gerenciar os produtos, acompanhar os pedidos e controlar o estoque.

Por fim, foram realizados testes para verificar o funcionamento da loja e das validações em JavaScript.

Este relato foi acrescentado após a implementação do sistema para registrar seu processo de construção.

## Acesso administrativo

Crie uma conta pelo site. No phpMyAdmin, acesse a tabela usuarios e altere o campo tipo da conta para admin. Depois, realize o login novamente.

## Observação

O projeto possui finalidade acadêmica. Os preços, estoques e pagamentos são simulados.

Senac — 2026.

## Recursos implementados

- Integração com o banco de dados por meio do PDO.
- Configurações de conexão armazenadas no arquivo .env.
- Armazenamento seguro e verificação de senhas.
- Gerenciamento do carrinho através de sessões PHP.
- Processamento de pedidos com transações no banco de dados.
- Cadastro, consulta, edição e exclusão de produtos.
- Painel administrativo com acesso restrito.
- Interatividade e validações com JavaScript.
- Controle de versões e publicação pelo Git e GitHub.