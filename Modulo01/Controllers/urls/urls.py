from django.contrib import admin
from django.urls import path, include
from Controllers.urls import auth_urls

urlpatterns = [
    path('admin/', admin.site.urls),
    path('api/auth/', include(auth_urls)),  # Todas las rutas estarán bajo /api/auth/
]
