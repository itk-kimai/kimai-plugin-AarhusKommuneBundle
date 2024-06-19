# Aarhus kommune – a Kimai plugin

## Installation

Download [a release](https://github.com/itk-kimai/kimai-plugin-AarhusKommuneBundle/releases) and move it to `var/plugins/`.

```shell
bin/console kimai:reload --no-interaction
```

Edit your [`local.yaml`](https://www.kimai.org/documentation/local-yaml.html#localyaml):

``` yaml
aarhus_kommune:
    primary_project: 87
    primary_activity: 42
```

See [Install and update Kimai plugins](https://www.kimai.org/documentation/plugin-management.html) for details.

Go to `/da/aarhus-kommune/timesheet/create` and enjoy.

## Development

``` shell
git clone --branch develop https://github.com/itk-kimai/kimai-plugin-AarhusKommuneBundle var/plugins/AarhusKommuneBundle
bin/console kimai:reload --no-interaction
```

### Coding standards

``` shell
docker run --rm --volume ${PWD}:/app --workdir /app itkdev/php8.3-fpm composer install
docker run --rm --volume ${PWD}:/app --workdir /app itkdev/php8.3-fpm composer codestyle-fix
docker run --rm --volume ${PWD}:/app --workdir /app itkdev/php8.3-fpm composer codestyle-check
```

``` shell
docker run --rm --volume "$(pwd):/md" peterdavehello/markdownlint markdownlint --ignore LICENSE.md '**/*.md' --fix
docker run --rm --volume "$(pwd):/md" peterdavehello/markdownlint markdownlint --ignore LICENSE.md '**/*.md'
```

``` shell
docker run --rm --volume ${PWD}:/app --workdir /app itkdev/php8.3-fpm composer install
docker run --rm --volume ${PWD}:/app --workdir /app itkdev/php8.3-fpm composer phpstan
```

*Note*: During development you should remove the `vendor/` folder to not confuse Kimai's autoloading.
