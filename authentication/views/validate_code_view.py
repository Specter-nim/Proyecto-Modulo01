from rest_framework.views import APIView
from rest_framework.response import Response
from rest_framework import status
from authentication.serializers import VerificationRequestSerializer
from authentication.models_email_verification import EmailVerificationCode
from django.contrib.auth import get_user_model

User = get_user_model()

class ValidateCodeView(APIView):
    def post(self, request):
        serializer = VerificationRequestSerializer(data=request.data)
        if serializer.is_valid():
            email = serializer.validated_data['email']
            code = serializer.validated_data['code']
            # Buscar el código en el modelo o API remota
            try:
                verification = EmailVerificationCode.objects.filter(email=email, code=code).order_by('-created_at').first()
                if verification and not verification.is_used:
                    # Marcar como usado y usuario verificado
                    verification.is_used = True
                    verification.save()
                    user = User.objects.filter(email=email).first()
                    if user:
                        user.is_verified = True
                        user.save()
                    return Response({'detail': 'Código verificado correctamente.'}, status=status.HTTP_200_OK)
                else:
                    return Response({'detail': 'Código inválido o expirado.'}, status=status.HTTP_400_BAD_REQUEST)
            except Exception:
                return Response({'detail': 'Error al verificar el código.'}, status=status.HTTP_500_INTERNAL_SERVER_ERROR)
        return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)
