set TARGET_DIR=D:\Projects\github\dida-autoloader

copy /y  README.md      "%TARGET_DIR%\"
copy /y  LICENSE        "%TARGET_DIR%\"
copy /y  composer.json  "%TARGET_DIR%\"
copy /y  .gitignore     "%TARGET_DIR%\"

del /f /s /q            "%TARGET_DIR%\src\*.*"
rd /s /q                "%TARGET_DIR%\src\"
xcopy /y /s  src        "%TARGET_DIR%\src\"

echo.修改后，记得同步发布到 dida-project 项目里面去！

ping -n 10 127.0.0.1>nul