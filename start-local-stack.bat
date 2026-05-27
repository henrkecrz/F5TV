@echo off
setlocal

cd /d "%~dp0"

echo ==============================================
echo   F5TV - Inicializacao da stack local
echo ==============================================
echo.

where npm >nul 2>&1
if errorlevel 1 (
  echo [ERRO] npm nao encontrado. Instale Node.js 18+ e tente novamente.
  pause
  exit /b 1
)

if not exist node_modules (
  echo [INFO] node_modules nao encontrado. Instalando dependencias...
  call npm install
  if errorlevel 1 (
    echo [ERRO] Falha ao instalar dependencias.
    pause
    exit /b 1
  )
)

if not exist .env if exist .env.example (
  echo [INFO] Criando .env a partir de .env.example...
  copy /Y .env.example .env >nul
)

echo [INFO] Subindo frontend + API local...
call npm run dev:full

if errorlevel 1 (
  echo.
  echo [ERRO] A stack encerrou com falha.
  pause
  exit /b 1
)

endlocal