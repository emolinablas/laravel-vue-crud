# Laravel Vue CRUD

Un CRUD genérico para Laravel con Vue 2 y Bootstrap-Vue.

## Instalación

1. Instalar el paquete vía Composer:
```bash
composer require emolinablas/laravel-vue-crud
```

2. Publicar los assets:
```bash
php artisan vendor:publish --tag=laravel-vue-crud-assets
```

## Uso Básico

Crea un controlador que extienda de `Emolinablas\LaravelVueCrud\CrudController`:

```php
<?php

namespace App\Http\Controllers;

use Emolinablas\LaravelVueCrud\CrudController;

class UsuarioController extends CrudController
{
    public function __construct()
    {
        $this->setTabla('usuarios')
            ->setTablaId('id')
            ->setTitulo('Gestión de Usuarios')
            ->setCampo([
                'nombre' => 'nombre',
                'campo' => 'nombre',
                'tipo' => 'string',
                'type' => 'string',
                'rules' => 'required|min:3'
            ]);
    }
}
```

## Tipos de campos soportados
- `string`
- `numeric`
- `date`
- `select`
- `enum`
- `checkbox`
- `image`
- `textarea` (usa editor WYSIWYG)

## Eventos Post-Guardado
Si necesitas ejecutar lógica adicional después de guardar un registro, usa el método `onAfterStore`:

```php
$this->onAfterStore(function($resultado, $tabla) {
    // Tu lógica aquí
});
```
