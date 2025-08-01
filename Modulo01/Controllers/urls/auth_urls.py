from django.urls import path
from Controllers.Auth.authenticated import LoginView
from Controllers.Auth.register_user import RegisterView
from Controllers.Auth.reset_password import ResetPasswordEmailVerificationView
from Controllers.Auth.change_password import ChangePasswordView
from Controllers.Auth.verification import VerifyCodeView

urlpatterns = [
    # Inicio de sesión
    path('login/', LoginView.as_view(), name='login'),

    # Registro de usuario
    path('register/', RegisterView.as_view(), name='register'),

    # Verificación de email para recuperar contraseña
    path('reset-password-email/', ResetPasswordEmailVerificationView.as_view(), name='reset_password_email'),

    # Cambio de contraseña luego de verificación
    path('change-password/', ChangePasswordView.as_view(), name='change_password'),

    # Verificación del código enviado al usuario
    path('verify-code/', VerifyCodeView.as_view(), name='verify_code'),
]
