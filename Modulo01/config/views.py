from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
from rest_framework.decorators import api_view, permission_classes
from rest_framework.permissions import AllowAny
from rest_framework.response import Response


@api_view(['GET'])
@permission_classes([AllowAny])
def api_root(request):
    """
    Vista raíz de la API
    """
    return Response({
        'message': 'Bienvenido a la API de Django con Autenticación JWT',
        'endpoints': {
            'autenticación': {
                'registro': '/api/auth/register/',
                'login': '/api/auth/login/',
                'logout': '/api/auth/logout/',
                'perfil': '/api/auth/profile/',
                'refresh_token': '/api/auth/token/refresh/',
                'permisos': '/api/auth/permissions/',
            },
            'admin': '/admin/',
        }
    })


@csrf_exempt
def home(request):
    """
    Vista de inicio simple
    """
    return JsonResponse({
        'message': 'Django Entorno - Sistema de Autenticación JWT',
        'status': 'running',
        'endpoints': '/api/'
    }) 