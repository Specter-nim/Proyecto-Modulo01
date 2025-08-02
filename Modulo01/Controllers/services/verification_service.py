from rest_framework.response import Response
from rest_framework import status
from Auth.models import UserVerificationToken  # Ajusta si está en otro módulo

def verify_code(user, code):
    try:
        token_obj = UserVerificationToken.objects.get(user=user, code=code)

        if token_obj.is_expired():
            return Response({'message': 'El código ha expirado.'}, status=status.HTTP_400_BAD_REQUEST)

        user.is_verified = True
        user.save()
        token_obj.delete()

        return Response({'message': 'Código verificado correctamente.'}, status=status.HTTP_200_OK)

    except UserVerificationToken.DoesNotExist:
        return Response({'message': 'Código inválido.'}, status=status.HTTP_404_NOT_FOUND)
