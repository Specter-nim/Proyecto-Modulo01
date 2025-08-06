from django.urls import path
from rest_framework_simplejwt.views import TokenRefreshView
from .views import LoginView, LogoutView, RegisterView
from .views.send_verify_code_view import SendVerifyCodeView
from .views.get_role_view import GetRoleView
from .views.validate_code_view import ValidateCodeView
from .views.change_password_view import ChangePasswordView
from .views.password_views import ValidateCurrentPasswordView, ResetPasswordView

app_name = 'authentication'

urlpatterns = [
    # Rutas de autenticación
    path('register/', RegisterView.as_view(), name='register'),
    path('login/', LoginView.as_view(), name='login'),
    path('logout/', LogoutView.as_view(), name='logout'),
]

urlpatterns += [
    path('send-verify-code/', SendVerifyCodeView.as_view(), name='send-verify-code'),
    path('get-role/', GetRoleView.as_view(), name='get-role'),
    path('validate-code/', ValidateCodeView.as_view(), name='validate-code'),
    path('change-password/', ChangePasswordView.as_view(), name='change-password'),
    path('validate-current-password/', ValidateCurrentPasswordView.as_view(), name='validate-current-password'),
    path('reset-password/', ResetPasswordView.as_view(), name='reset-password'),
]