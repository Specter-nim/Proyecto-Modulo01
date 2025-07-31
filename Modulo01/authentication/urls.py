from django.urls import path
from rest_framework_simplejwt.views import TokenRefreshView
from .views import UserRegistrationView, user_login, user_logout, UserProfileView, user_permissions, api_documentation, password_reset_request

app_name = 'authentication'

urlpatterns = [
    # Documentación
    path('docs/', api_documentation, name='docs'),
    
    # Rutas de autenticación
    path('register/', UserRegistrationView.as_view(), name='register'),
    path('login/', user_login, name='login'),
    path('logout/', user_logout, name='logout'),
    path('profile/', UserProfileView.as_view(), name='profile'),
    
    # Rutas de JWT
    path('token/refresh/', TokenRefreshView.as_view(), name='token_refresh'),
    
    # Rutas de Guardian
    path('permissions/', user_permissions, name='permissions'),
    
    # Rutas de email
    path('password-reset/', password_reset_request, name='password_reset'),
] 