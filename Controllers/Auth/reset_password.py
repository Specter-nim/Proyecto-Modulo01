from rest_framework.views import APIView
from rest_framework.response import Response
from rest_framework import status
from ..services.reset_password_service import verify_email  # Corregido: uso ruta relativa

class VerifyEmailView(APIView):
    """
    Verifica si un correo electrónico existe en la base de datos.
    """

    def get(self, request):
        email = request.query_params.get('email')

        if not email:
            return Response({'error': 'Se requiere el email.'}, status=status.HTTP_400_BAD_REQUEST)

        return verify_email(email)
