# Facturín

![tests](https://github.com/vickkk28/facturin/actions/workflows/tests.yml/badge.svg)

Gestor de clientes y comprobantes (Factura A, B y C) desarrollado en **Laravel**.
Aplica las reglas de facturación argentinas: el sistema determina automáticamente
qué tipo de comprobante corresponde según la condición frente al IVA del cliente
y numera cada comprobante de forma correlativa por tipo y punto de venta, con el
formato que usa ARCA (`A 0001-00000001`).

## Funcionalidades

- **Autenticación** con registro, login y verificación en dos pasos (2FA).
- **ABM de clientes**: búsqueda por razón social o CUIT, paginado y validaciones
  (CUIT de 11 dígitos y único, condición de IVA, email).
- **Emisión de comprobantes**:
  - Tipo A / B / C calculado automáticamente según la condición de IVA.
  - Numeración automática dentro de una transacción con bloqueo, para evitar
    números duplicados si dos usuarios emiten al mismo tiempo.
  - Los comprobantes emitidos no se editan ni se eliminan, igual que en ARCA.
- **Integridad de datos**: un cliente con comprobantes no se puede eliminar
  (clave foránea con `restrictOnDelete` y validación en el controlador).
- **Dashboard**: total facturado del mes, gráfico de los últimos 6 meses,
  resumen por tipo de factura y ranking de clientes (consultas con `JOIN`,
  `SUM`, `COUNT` y `GROUP BY`).

## Tecnologías

- PHP 8.5 y Laravel 13
- MySQL / MariaDB (en desarrollo) y SQLite (en pruebas y CI)
- Blade, Livewire, Tailwind CSS y Alpine.js
- Pest (pruebas automatizadas)
- Pint (estilo de código) y PHPStan / Larastan (análisis estático)
- GitHub Actions (integración continua)

## Decisiones de diseño

- **Importes con `decimal(12, 2)`** en lugar de `float`, para evitar errores de redondeo con dinero.
- **El tipo de comprobante no lo elige el usuario**: lo calcula el método
  `Comprobante::tipoPara()`, que concentra la regla de negocio y está cubierto por pruebas.
- **Agrupación mensual en PHP** en el dashboard, para que las consultas funcionen
  igual en MySQL y en SQLite.
- **Formularios reutilizados** entre alta y edición, y reglas de validación compartidas.

## Instalación

```bash
git clone https://github.com/vickkk28/facturin.git
cd facturin
composer install
cp .env.example .env
php artisan key:generate
```

Configurar la base de datos en `.env` (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) y luego:

```bash
php artisan migrate
npm install
npm run build
composer run dev
```

## Calidad de código

```bash
php vendor/bin/pint                              # formato
php vendor/bin/phpstan analyse --memory-limit=1G # análisis estático
php artisan test                                 # pruebas
```

Cada push a `main` ejecuta estas verificaciones en GitHub Actions.

## Uso de IA en el desarrollo

El proyecto se desarrolló con asistencia de IA (Claude) para planificar la
estructura, consultar buenas prácticas de Laravel e interpretar errores.
Incluye configuración de **Laravel Boost** para Claude Code y GitHub Copilot,
de modo que los asistentes respeten las convenciones del proyecto.
Todo el código fue revisado, probado y validado con Pint, PHPStan y Pest antes de integrarse.
