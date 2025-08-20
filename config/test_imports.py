#!/usr/bin/env python3
"""
Archivo de prueba para verificar que todas las importaciones funcionen correctamente
"""
import os
import sys
import django

# Configurar Django
os.environ.setdefault('DJANGO_SETTINGS_MODULE', 'config.settings')
django.setup()

# Probar importaciones

try:
    print("Probando importaciones de views...")
    
    # Importar vistas
    from views.Auth.authenticated import LoginView, LogoutView
    print("✓ LoginView y LogoutView importados correctamente")
    
    from views.Auth.register_user import RegisterView
    print("✓ RegisterView importado correctamente")
    
    from views.Auth.change_password import ChangePasswordView, ValidatePasswordView
    print("✓ ChangePasswordView y ValidatePasswordView importados correctamente")
    
    from views.Auth.get_role import GetRoleView
    print("✓ GetRoleView importado correctamente")
    
    from views.Auth.reset_password import VerifyEmailView
    print("✓ VerifyEmailView importado correctamente")
    
    from views.Auth.verification import VerifyCodeView
    print("✓ VerifyCodeView importado correctamente")
    
    # Importar servicios
    from views.services.first_login_service import verify_first_login
    print("✓ verify_first_login importado correctamente")
    
    from views.services.change_password_service import change_user_password
    print("✓ change_user_password importado correctamente")
    
    from views.services.get_role_service import get_user_role_and_permissions
    print("✓ get_user_role_and_permissions importado correctamente")
    
    from views.services.reset_password_service import verify_email
    print("✓ verify_email importado correctamente")
    
    from views.services.verification_service import verify_code
    print("✓ verify_code importado correctamente")
    
    # Importar modelos
    from views.models import UserVerificationToken
    print("✓ UserVerificationToken importado correctamente")
    
    print("\n🎉 ¡Todas las importaciones funcionan correctamente!")
    
except ImportError as e:
    print(f"❌ Error de importación: {e}")
    sys.exit(1)
except Exception as e:
    print(f"❌ Error inesperado: {e}")
    sys.exit(1) 