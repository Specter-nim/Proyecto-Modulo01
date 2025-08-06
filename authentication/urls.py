from django.urls import path
from rest_framework_simplejwt.views import TokenRefreshView
from .views import LoginView, LogoutView, RegisterView
from .views.send_verify_code_view import SendVerifyCodeView
from .views.get_role_view import GetRoleView

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
]