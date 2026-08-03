# Changelog

## [1.1.2] - 2026-08-03
### Fixed
- Fix: `npm run dev` fallaba con error de `Progress Plugin` en entornos con `webpack-cli@5` — se fijó `webpack-cli` a `^4.10.0` en `devDependencies` (requerimiento de `laravel-mix@6`)

## [1.1.1] - 2026-08-03
### Fixed
- Fix: Compatibilidad con PHP 7.4 — se eliminó el tipo de retorno `static` en `onAfterStore()` (requería PHP 8.0+)
- Fix: Constraint de PHP en `composer.json` ampliado a `^7.4|^8.0|^8.1|^8.2|^8.3`
- Fix: Constraint de Laravel ampliado a `^8.0|^9.0|^10.0|^11.0|^12.0`

## [1.1.0] - 2026-07-20
### Security
- Fix: Path traversal en manejo de imágenes (directorio construido con input del cliente)
- Fix: XSS stored vía v-html sin sanitización (ahora usa DOMPurify)

### Fixed
- Fix: setAfterWhere escribía en $wheres en lugar de $afterWheres
- Fix: Bug de asignación = en v-else-if del checkbox (debía ser ===)
- Fix: if sin cuerpo en setCampo() causaba lógica incorrecta
- Fix: Acoplamiento con App\Events\* de la app consumidora reemplazado por callbacks

### Added
- Callbacks post-store via onAfterStore(callable $callback)
- Versión mínima de PHP (^8.0) y Laravel (^9.0) definida en composer.json
