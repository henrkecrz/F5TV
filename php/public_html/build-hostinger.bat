@echo off
setlocal

cd /d "%~dp0"

echo ==============================================
echo   F5TV - Build para Hostinger
echo ==============================================
echo.

where npm >nul 2>&1
if errorlevel 1 (
  echo [ERRO] npm nao encontrado. Instale Node.js 18+.
  pause
  exit /b 1
)

if not exist .env.production (
  echo [ERRO] .env.production nao encontrado.
  echo Crie esse arquivo antes do build.
  pause
  exit /b 1
)

echo [INFO] Instalando dependencias...
call npm install
if errorlevel 1 (
  echo [ERRO] Falha no npm install.
  pause
  exit /b 1
)

echo [INFO] Gerando build de producao...
call npm run build
if errorlevel 1 (
  echo [ERRO] Falha no npm run build.
  pause
  exit /b 1
)

echo.
echo [OK] Build gerado em: dist\
echo [PROXIMO PASSO] Envie apenas o conteudo de dist\ para public_html\
echo mantendo a pasta api\ no servidor.
pause

endlocal