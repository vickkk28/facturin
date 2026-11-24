# Corre Pint, PHPStan y los tests antes de hacer un commit
Write-Host "1/3 Formateando con Pint..." -ForegroundColor Cyan
php vendor/bin/pint
if ($LASTEXITCODE -ne 0) { Write-Host "Falló Pint" -ForegroundColor Red; exit 1 }

Write-Host "2/3 Analizando con PHPStan..." -ForegroundColor Cyan
php vendor/bin/phpstan analyse --memory-limit=1G
if ($LASTEXITCODE -ne 0) { Write-Host "Falló PHPStan" -ForegroundColor Red; exit 1 }

Write-Host "3/3 Corriendo tests..." -ForegroundColor Cyan
php artisan test
if ($LASTEXITCODE -ne 0) { Write-Host "Fallaron los tests" -ForegroundColor Red; exit 1 }

Write-Host "Todo OK, ya podés hacer el commit" -ForegroundColor Green