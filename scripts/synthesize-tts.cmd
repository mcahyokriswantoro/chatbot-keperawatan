@echo off
setlocal EnableExtensions

set "NODE_EXE=C:\Program Files\nodejs\node.exe"
if defined NODE_BINARY set "NODE_EXE=%NODE_BINARY%"
if not exist "%NODE_EXE%" set "NODE_EXE=node"

"%NODE_EXE%" "%~dp0synthesize-tts.mjs" %*
exit /b %ERRORLEVEL%
