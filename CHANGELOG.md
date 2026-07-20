# Changelog

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
