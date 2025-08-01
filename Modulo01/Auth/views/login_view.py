from rest_framework.views import APIView
from rest_framework.response import Response
from rest_framework import status
from Auth.serializers.login_serializer import LoginSerializer

class LoginView(APIView):
    permission_classes = [] # Puedes agregar IsAuthenticated si lo deseas

    def post(self, request):


        serializer = LoginSerializer(data=request.data)
        if serializer.is_valid():
            return Response(serializer.validated_data, status=status.HTTP_200_OK)
        return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)
