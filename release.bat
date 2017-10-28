set TARGET_DIR=D:\Projects\github\dida-autoloader

copy /y  README.md      "%TARGET_DIR%\"
copy /y  LICENSE        "%TARGET_DIR%\"
copy /y  composer.json  "%TARGET_DIR%\"
copy /y  .gitignore     "%TARGET_DIR%\"

del /f /s /q            "%TARGET_DIR%\src\*.*"
xcopy /y /s  src        "%TARGET_DIR%\src\"

pause