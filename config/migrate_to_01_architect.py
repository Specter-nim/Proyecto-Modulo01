#!/usr/bin/env python
"""
Script de migración para 01_architect
Este script ayuda a migrar desde la estructura anterior a la nueva estructura 01_architect
"""

import os
import shutil
import subprocess
import sys
from pathlib import Path

def print_status(message, status="INFO"):
    """Imprime un mensaje con formato de estado"""
    colors = {
        "INFO": "\033[94m",    # Azul
        "SUCCESS": "\033[92m", # Verde
        "WARNING": "\033[93m", # Amarillo
        "ERROR": "\033[91m",   # Rojo
    }
    reset = "\033[0m"
    color = colors.get(status, colors["INFO"])
    print(f"{color}[{status}]{reset} {message}")

def backup_old_apps():
    """Hace backup de las aplicaciones antiguas"""
    print_status("Haciendo backup de aplicaciones antiguas...", "INFO")
    
    backup_dir = Path("backup_old_apps")
    if backup_dir.exists():
        shutil.rmtree(backup_dir)
    
    backup_dir.mkdir()
    
    old_apps = [
        "authentication",
        "Auth", 
        "permissions",
        "roles",
        "services",
        "Controllers"
    ]
    
    for app in old_apps:
        app_path = Path(app)
        if app_path.exists():
            backup_path = backup_dir / app
            shutil.copytree(app_path, backup_path)
            print_status(f"Backup creado para: {app}", "SUCCESS")
    
    return backup_dir

def check_django_installation():
    """Verifica que Django esté instalado y funcione"""
    print_status("Verificando instalación de Django...", "INFO")
    
    try:
        result = subprocess.run([
            sys.executable, "manage.py", "check"
        ], capture_output=True, text=True, cwd=".")
        
        if result.returncode == 0:
            print_status("Django está funcionando correctamente", "SUCCESS")
            return True
        else:
            print_status(f"Error en Django: {result.stderr}", "ERROR")
            return False
            
    except Exception as e:
        print_status(f"Error ejecutando Django: {e}", "ERROR")
        return False

def run_migrations():
    """Ejecuta las migraciones de 01_architect"""
    print_status("Ejecutando migraciones...", "INFO")
    
    try:
        # Crear migraciones
        result = subprocess.run([
            sys.executable, "manage.py", "makemigrations", "01_architect"
        ], capture_output=True, text=True, cwd=".")
        
        if result.returncode == 0:
            print_status("Migraciones creadas exitosamente", "SUCCESS")
            print(result.stdout)
        else:
            print_status(f"Error creando migraciones: {result.stderr}", "ERROR")
            return False
        
        # Aplicar migraciones
        result = subprocess.run([
            sys.executable, "manage.py", "migrate"
        ], capture_output=True, text=True, cwd=".")
        
        if result.returncode == 0:
            print_status("Migraciones aplicadas exitosamente", "SUCCESS")
            print(result.stdout)
            return True
        else:
            print_status(f"Error aplicando migraciones: {result.stderr}", "ERROR")
            return False
            
    except Exception as e:
        print_status(f"Error ejecutando migraciones: {e}", "ERROR")
        return False

def create_superuser():
    """Crea un superusuario si no existe"""
    print_status("Verificando superusuario...", "INFO")
    
    try:
        # Verificar si ya existe un superusuario
        result = subprocess.run([
            sys.executable, "manage.py", "shell", "-c",
            "from django.contrib.auth import get_user_model; User = get_user_model(); print(User.objects.filter(is_superuser=True).count())"
        ], capture_output=True, text=True, cwd=".")
        
        if result.returncode == 0 and result.stdout.strip() == "0":
            print_status("No hay superusuarios. Creando uno...", "WARNING")
            print_status("Ejecuta manualmente: python manage.py createsuperuser", "INFO")
        else:
            print_status("Ya existe un superusuario", "SUCCESS")
            
    except Exception as e:
        print_status(f"Error verificando superusuario: {e}", "WARNING")

def main():
    """Función principal del script de migración"""
    print_status("=== SCRIPT DE MIGRACIÓN A 01_ARCHITECT ===", "INFO")
    print_status("Este script te ayudará a migrar a la nueva estructura", "INFO")
    
    # Verificar que estemos en el directorio correcto
    if not Path("manage.py").exists():
        print_status("Error: No se encontró manage.py. Ejecuta este script desde el directorio raíz del proyecto.", "ERROR")
        return
    
    # Hacer backup
    backup_dir = backup_old_apps()
    print_status(f"Backup guardado en: {backup_dir}", "SUCCESS")
    
    # Verificar Django
    if not check_django_installation():
        print_status("No se puede continuar. Verifica la instalación de Django.", "ERROR")
        return
    
    # Ejecutar migraciones
    if not run_migrations():
        print_status("Error en las migraciones. Revisa los errores arriba.", "ERROR")
        return
    
    # Verificar superusuario
    create_superuser()
    
    print_status("=== MIGRACIÓN COMPLETADA ===", "SUCCESS")
    print_status("La nueva estructura 01_architect está lista", "SUCCESS")
    print_status(f"Backup de aplicaciones antiguas en: {backup_dir}", "INFO")
    print_status("Puedes eliminar las aplicaciones antiguas cuando estés seguro de que todo funciona", "WARNING")
    print_status("Para probar: python manage.py runserver", "INFO")

if __name__ == "__main__":
    main() 