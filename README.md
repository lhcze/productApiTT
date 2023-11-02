
## Spec

- PHP 8.1+
- `nette/*` packages
- build PSR-7 API via `contributte/apitte`
- Doctrine ORM via `nettrine/*`
- Symfony components via `contributte/*`
- codestyle checking via **CodeSniffer** and `contributte/qa`
- static analysing via **phpstan** and `contributte/phpstan`
- unit / integration tests via **Nette Tester** and `contributte/tester`
- based on apitte-skeleton https://github.com/contributte/apitte-skeleton

## Supported Endpoints

```json
{
	"openapi": "3.0.2",
	"info": {
		"title": "OpenAPI",
		"version": "1.0.0"
	},
	"paths": {
		"/api/public/v1/openapi/meta": {
			"get": {
				"tags": [
					"OpenApi"
				],
				"summary": "Get OpenAPI definition.",
				"responses": []
			}
		},
		"/api/v1/products/create": {
			"post": {
				"tags": [
					"Products"
				],
				"summary": "Create new product",
				"requestBody": {
					"content": {
						"application/json": {
							"schema": {
								"type": "object",
								"properties": {
									"name": {
										"type": "string"
									},
									"price": {
										"type": "number"
									}
								}
							}
						}
					}
				},
				"responses": []
			}
		},
		"/api/v1/products/delete/{id}": {
			"delete": {
				"tags": [
					"Products"
				],
				"summary": "Delete specified product",
				"parameters": [
					{
						"name": "id",
						"in": "path",
						"required": true,
						"schema": {
							"type": "string"
						}
					}
				],
				"responses": []
			}
		},
		"/api/v1/products/update/{id}": {
			"patch": {
				"tags": [
					"Products"
				],
				"summary": "Update product",
				"parameters": [
					{
						"name": "id",
						"in": "path",
						"required": true,
						"schema": {
							"type": "string"
						}
					}
				],
				"requestBody": {
					"content": {
						"application/json": {
							"schema": {
								"type": "object",
								"properties": []
							}
						}
					}
				},
				"responses": []
			}
		},
		"/api/v1/products": {
			"get": {
				"tags": [
					"Products"
				],
				"summary": "List Products",
				"parameters": [
					{
						"name": "limit",
						"in": "query",
						"description": "Data limit",
						"required": false,
						"schema": {
							"type": "integer"
						}
					},
					{
						"name": "offset",
						"in": "query",
						"description": "Data offset",
						"required": false,
						"schema": {
							"type": "integer"
						}
					}
				],
				"responses": []
			}
		},
		"/api/v1/users/create": {
			"post": {
				"tags": [
					"Users"
				],
				"summary": "Create new user.",
				"requestBody": {
					"content": {
						"application/json": {
							"schema": {
								"type": "object",
								"properties": {
									"email": {
										"type": "string"
									},
									"name": {
										"type": "string"
									},
									"surname": {
										"type": "string"
									},
									"username": {
										"type": "string"
									},
									"password": {
										"nullable": true,
										"type": "string"
									}
								}
							}
						}
					}
				},
				"responses": []
			}
		},
		"/api/v1/users/email": {
			"get": {
				"tags": [
					"Users"
				],
				"summary": "Get user by email",
				"parameters": [
					{
						"name": "email",
						"in": "query",
						"description": "User e-mail address",
						"required": true,
						"schema": {
							"type": "string"
						}
					}
				],
				"responses": []
			}
		},
		"/api/v1/users/update/{id}": {
			"patch": {
				"tags": [
					"Users"
				],
				"summary": "Update user",
				"parameters": [
					{
						"name": "id",
						"in": "path",
						"required": true,
						"schema": {
							"type": "string"
						}
					}
				],
				"requestBody": {
					"content": {
						"application/json": {
							"schema": {
								"type": "object",
								"properties": []
							}
						}
					}
				},
				"responses": []
			}
		},
		"/api/v1/users/{id}": {
			"get": {
				"tags": [
					"Users"
				],
				"summary": "Get user by id",
				"parameters": [
					{
						"name": "id",
						"in": "path",
						"description": "User ID",
						"required": true,
						"schema": {
							"type": "integer"
						}
					}
				],
				"responses": []
			}
		},
		"/api/v1/users": {
			"get": {
				"tags": [
					"Users"
				],
				"summary": "List users.",
				"parameters": [
					{
						"name": "limit",
						"in": "query",
						"description": "Data limit",
						"required": false,
						"schema": {
							"type": "integer"
						}
					},
					{
						"name": "offset",
						"in": "query",
						"description": "Data offset",
						"required": false,
						"schema": {
							"type": "integer"
						}
					}
				],
				"responses": []
			}
		}
	}
}
```

## Install with [docker compose](https://github.com/docker/compose)

1) get project from github

2) Modify `config/local.neon` and set host to `database`

   Default configuration should look like this. There is preconfigured database. Pick PostgreSQL or MariaDB.

   ```neon
   # Host Config
   parameters:

       # Database
       database:

           # Postgres
           driver: pdo_pgsql
           host: database
           dbname: contributte
           user: contributte
           password: contributte
           port: 5432
   ```

3) Run `docker-compose up`

4) Open http://localhost/api/public/v1/openapi/meta (Swagger format)

## Features

Here is a list of all features you can find in this project.

- PHP 8.1+
- :package: Packages
    - Nette 3+
    - Contributte
- :deciduous_tree: Structure
    - `app`
        - `config` - configuration files
            - `env` - prod/dev/test environments
            - `app` - application configs
            - `ext` - extensions configs
            - `local.neon` - local runtime config
            - `local.neon.dist` - template for local config
        - `domain` - business logic and domain specific classes
        - `model` - application backbone
        - `module` - API module
        - `resources` - static content for mails and others
        - `bootstrap.php` - Nette entrypoint
    - `bin` - console entrypoint (`bin/console`)
    - `db` - database files
        - `fixtures` - PHP fixtures
        - `migrations` - migrations files
    - `docs` - documentation
    - `vae`
        - `log` - runtime and error logs
        - `tmp` - temp files and cache
    - `tests` - test engine and many cases
        - `tests/cases/E2E` - PhpStorm's requests files (`api.http`)
        - `tests/cases/Integration`
        - `tests/cases/Unit`
    - `vendor` - composer's folder
    - `www` - public content
- :exclamation: Tracy
    - Cool error 500 page

### Composer packages

Take a detailed look :eyes: at each single package.

- [contributte/bootstrap](https://contributte.org/packages/contributte/bootstrap.html)
- [contributte/di](https://contributte.org/packages/contributte/di.html)
- [contributte/http](https://contributte.org/packages/contributte/http.html)
- [contributte/security](https://contributte.org/packages/contributte/security.html)
- [contributte/utils](https://contributte.org/packages/contributte/utils.html)
- [contributte/tracy](https://contributte.org/packages/contributte/tracy.html)
- [contributte/console](https://contributte.org/packages/contributte/console.html)
- [contributte/neonizer](https://contributte.org/packages/contributte/neonizer.html)
- [contributte/monolog](https://contributte.org/packages/contributte/monolog.html)
- [contributte/apitte](https://contributte.org/packages/contributte/apitte.html)

**Doctrine**

- [contributte/doctrine-orm](https://contributte.org/packages/contributte/doctrine-orm.html)
- [contributte/doctrine-dbal](https://contributte.org/packages/contributte/doctrine-dbal.html)
- [contributte/doctrine-migrations](https://contributte.org/packages/contributte/doctrine-migrations.html)
- [contributte/doctrine-fixtures](https://contributte.org/packages/contributte/doctrine-fixtures.html)

**Nette**

- [nette/finder](https://github.com/nette/finder)
- [nette/robot-loader](https://github.com/nette/robot-loader)

**Symfony**

- [symfony/serializer](https://github.com/symfony/serializer)
- [symfony/validator](https://github.com/symfony/validator)
