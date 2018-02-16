@echo off
start vendor\bin\doctrine.bat orm:schema-tool:update --force
