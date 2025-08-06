from rest_framework.views import APIView
from rest_framework.permissions import IsAuthenticated
from rest_framework.response import Response
from rest_framework import status
from authentication.serializers import ValidatePasswordRequest, ResetPasswordRequest
from django.contrib.auth import get_user_model
from authentication.utils import send_password_reset_email

User = get_user_model()

class ValidateCurrentPasswordView(APIView):
    permission_classes = [IsAuthenticated]
    def post(self, request):
        serializer = ValidatePasswordRequest(data=request.data)
        if serializer.is_valid():
            password = serializer.validated_data['password']
            user = request.user
            if user.check_password(password):
                return Response({'detail': 'Contraseña válida.'}, status=status.HTTP_200_OK)
            else:
                return Response({'detail': 'Contraseña incorrecta.'}, status=status.HTTP_400_BAD_REQUEST)
        return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)

class ResetPasswordView(APIView):
    def post(self, request):
        serializer = ResetPasswordRequest(data=request.data)
        if serializer.is_valid():
            email = serializer.validated_data['email']
            user = User.objects.filter(email=email).first()
            if user:
                send_password_reset_email(user, request)
            # Por seguridad, no revelar si el email existe
            return Response({'detail': 'Si el email existe, recibirás instrucciones.'}, status=status.HTTP_200_OK)
        return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)
