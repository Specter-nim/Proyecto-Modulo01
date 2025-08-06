from rest_framework.views import APIView
from rest_framework.permissions import IsAuthenticated
from rest_framework.response import Response
from rest_framework import status
from authentication.serializers import ChangePasswordRequest
from authentication.services.change_password_service import ChangePasswordService

class ChangePasswordView(APIView):
    permission_classes = [IsAuthenticated]

    def post(self, request):
        serializer = ChangePasswordRequest(data=request.data)
        if serializer.is_valid():
            old_password = serializer.validated_data['old_password']
            new_password = serializer.validated_data['new_password']
            user = request.user
            result = ChangePasswordService.change_password(user, old_password, new_password)
            if result['success']:
                return Response({'detail': 'Contraseña cambiada correctamente.'}, status=status.HTTP_200_OK)
            else:
                return Response({'detail': result['error']}, status=status.HTTP_400_BAD_REQUEST)
        return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)
