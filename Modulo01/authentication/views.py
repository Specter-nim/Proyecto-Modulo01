from rest_framework import status, generics
from rest_framework.decorators import api_view, permission_classes
from rest_framework.permissions import AllowAny, IsAuthenticated
from rest_framework.response import Response
from rest_framework_simplejwt.tokens import RefreshToken
from django.contrib.auth import logout
from guardian.shortcuts import assign_perm, get_perms, get_objects_for_user
from .serializers import UserRegistrationSerializer, UserLoginSerializer, UserSerializer
from .utils import send_welcome_email
from .models import User


class UserRegistrationView(generics.CreateAPIView):
    """
    Vista para el registro de usuarios
    """
    serializer_class = UserRegistrationSerializer
    permission_classes = [AllowAny]

    def create(self, request, *args, **kwargs):
        serializer = self.get_serializer(data=request.data)
        if serializer.is_valid():
            user = serializer.save()
            refresh = RefreshToken.for_user(user)
            
            # Enviar email de bienvenida
            try:
                send_welcome_email(user)
            except Exception as e:
                # Log del error pero no fallar el registro
                print(f"Error enviando email de bienvenida: {e}")
            
            return Response({
                'message': 'Usuario registrado exitosamente. Se ha enviado un email de bienvenida.',
                'user': UserSerializer(user).data,
                'tokens': {
                    'access': str(refresh.access_token),
                    'refresh': str(refresh),
                }
            }, status=status.HTTP_201_CREATED)
        return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)


@api_view(['POST'])
@permission_classes([AllowAny])
def user_login(request):
    """
    Vista para el login de usuarios
    """
    serializer = UserLoginSerializer(data=request.data)
    if serializer.is_valid():
        user = serializer.validated_data['user']
        refresh = RefreshToken.for_user(user)
        return Response({
            'message': 'Login exitoso',
            'user': UserSerializer(user).data,
            'tokens': {
                'access': str(refresh.access_token),
                'refresh': str(refresh),
            }
        }, status=status.HTTP_200_OK)
    return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)


@api_view(['POST'])
@permission_classes([IsAuthenticated])
def user_logout(request):
    """
    Vista para el logout de usuarios
    """
    try:
        refresh_token = request.data.get('refresh_token')
        if refresh_token:
            token = RefreshToken(refresh_token)
            token.blacklist()
        logout(request)
        return Response({
            'message': 'Logout exitoso'
        }, status=status.HTTP_200_OK)
    except Exception as e:
        return Response({
            'error': 'Error en el logout'
        }, status=status.HTTP_400_BAD_REQUEST)


class UserProfileView(generics.RetrieveUpdateAPIView):
    """
    Vista para obtener y actualizar el perfil del usuario
    """
    serializer_class = UserSerializer
    permission_classes = [IsAuthenticated]

    def get_object(self):
        return self.request.user


@api_view(['GET'])
@permission_classes([IsAuthenticated])
def user_permissions(request):
    """
    Vista para mostrar los permisos del usuario usando Django Guardian
    """
    user = request.user
    
    # Ejemplo: asignar un permiso al propio usuario
    assign_perm('authentication.change_user', user, user)
    
    # Obtener permisos del usuario sobre sí mismo
    permissions = get_perms(user, user)
    
    return Response({
        'user_id': user.id,
        'user_email': user.email,
        'permissions_on_self': permissions,
        'message': 'Ejemplo de uso de Django Guardian'
    })


@api_view(['GET'])
@permission_classes([AllowAny])
def api_documentation(request):
    """
    Documentación de la API de autenticación
    """
    return Response({
        'message': 'Documentación de la API de Autenticación',
        'endpoints': {
            'register': {
                'method': 'POST',
                'url': '/api/auth/register/',
                'description': 'Registrar un nuevo usuario',
                'required_fields': ['email', 'username', 'password', 'password_confirm'],
                'optional_fields': ['first_name', 'last_name', 'phone'],
                'example': {
                    'email': 'usuario@ejemplo.com',
                    'username': 'usuario',
                    'password': 'password123',
                    'password_confirm': 'password123',
                    'first_name': 'Juan',
                    'last_name': 'Pérez'
                }
            },
            'login': {
                'method': 'POST',
                'url': '/api/auth/login/',
                'description': 'Iniciar sesión',
                'required_fields': ['email', 'password'],
                'example': {
                    'email': 'usuario@ejemplo.com',
                    'password': 'password123'
                }
            },
            'profile': {
                'method': 'GET/PUT',
                'url': '/api/auth/profile/',
                'description': 'Obtener/actualizar perfil del usuario',
                'authentication': 'Required'
            },
            'password_reset': {
                'method': 'POST',
                'url': '/api/auth/password-reset/',
                'description': 'Solicitar restablecimiento de contraseña',
                'required_fields': ['email'],
                'example': {
                    'email': 'usuario@ejemplo.com'
                }
            }
        }
    })


@api_view(['POST'])
@permission_classes([AllowAny])
def password_reset_request(request):
    """
    Vista para solicitar restablecimiento de contraseña
    """
    email = request.data.get('email')
    
    if not email:
        return Response({
            'error': 'El campo email es requerido'
        }, status=status.HTTP_400_BAD_REQUEST)
    
    try:
        user = User.objects.get(email=email)
        
        # Enviar email de restablecimiento
        try:
            from .utils import send_password_reset_email
            send_password_reset_email(user, request)
            return Response({
                'message': 'Se ha enviado un email con las instrucciones para restablecer tu contraseña.'
            }, status=status.HTTP_200_OK)
        except Exception as e:
            return Response({
                'error': 'Error enviando el email. Intenta nuevamente.'
            }, status=status.HTTP_500_INTERNAL_SERVER_ERROR)
            
    except User.DoesNotExist:
        # Por seguridad, no revelamos si el email existe o no
        return Response({
            'message': 'Si el email existe en nuestra base de datos, recibirás las instrucciones.'
        }, status=status.HTTP_200_OK)
