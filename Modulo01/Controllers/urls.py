from django.urls import path
from Controllers.Auth.change_password import ChangePasswordView, ValidatePasswordView

urlpatterns = [
    path('change-password/', ChangePasswordView.as_view(), name='change_password'),
    path('validate-password/', ValidatePasswordView.as_view(), name='validate_password'),
]
from django.urls import path
from Controllers.Auth.authenticated import LoginView, LogoutView

urlpatterns = [
    path('login/', LoginView.as_view(), name='login'),
    path('logout/', LogoutView.as_view(), name='logout'),
]
