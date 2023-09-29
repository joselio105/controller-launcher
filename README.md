# Controller Launcher

![GitHub release (release name instead of tag name)](https://img.shields.io/github/v/release/joselio105/controller-launcher?include_prereleases)
![GitHub](https://img.shields.io/github/license/joselio105/controller-launcher)
![PHP](https://img.shields.io/badge/PHP-7.4.33-blue)
![PHP Unit](https://img.shields.io/badge/depencencies-PHPUnit9.6-yellowgreen)

Executando controllers a partir de rotas especificadas previamente

## Menu

-   [Instalação como Dependência](#instalação-como-dependência)
    -   [Diretamente pelo Composer](#diretamente-pelo-composer)
    -   [Alterando o arquivo composer.json](#alterando-o-arquivo-composerjson)
-   [Rodando os Testes](#rodando-os-testes)
-   [Funcionalidades](#funcionalidades)
-   [Exceções](#exceções)

## Instalação como dependência

Instale File and Path usando o **Composer**

### Diretamente pelo Composer

```bash
  composer require plugse/fileandpath
```

### Alterando o arquivo composer.json

1. Crie ou altere o arquivo composer.json
2. Crie ou altere a propriedade **require**

```json
{
    "require": {
        "plugse/fileandpath": "^1"
    }
}
```

3. Atualize a biblioteca com o comando abaixo:

```bash
    composer update
```

## Rodando os testes

Para rodar os testes, rode o seguinte comando

```bash
  composer run-script post-install-cmd
```

## Funcionalidades

- **Retorna o resultado do método executado na classe controller definida na rota.**
```php
/**
     * Retorna o resultado do método executado na classe controller definida na rota.
     * @return string
     */
    $boot = new Bootstrap();
    echo $boot->getResponse();
```

-   **Permite a criação de uma classe Route (uma rota).**
```php
    /**
     * @param string $controllerName - O nome do controller (sem o sufixo Controller).
     * @param string $method - O método http que será usado para acessar a action. Valor padrão GET.
     * @param string $action - A função a ser executada na classe controller. Valor padrão index.
     * @param bool $isPrivate - Essa rota é privada? Valor padrão false.
     */
    new Route('foo');
```

-   **Permite a criação de oleção de cinco rotas: index, create, update, cancel e erase.**
```php
    /**
     * @param string $controllerName - O nome do controller (sem o sufixo Controller).
     * @param array $omit - A lista de rotas que devem alterar o seu parâmetro $isPrivate. Valor padrão [].
     */

    new RouteCollection('foo');
```

- **Executa o método da classe controller definido na rota**
```php
    /**
     * @param Request $request - Objeto com os parâmetros definidos na requisição http.
     * @param Routes $routes - Listagem das rotas definidas na aplicação.
     * @param string $controllersPath - Caminho para a pasta onde se encontram as classes controller. Valor padão: './src/infra/http/controllers/' .
     */
    $starter = new ControllerStarter($request, $routes);
    echo $starter->execute();
```

## Exceções

1. ClassNotFoundError
1. FileNotFoundError
1. MethodNotImplementedError
1. PermitionDeniedError
1. PermitionIncorrectError
1. RouteNotImplementedError
1. TokenDecodeError
1. TokenExpiredError