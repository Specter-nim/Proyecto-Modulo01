# test_login.py
import pytest
from rest_framework.test import APIClient
from authentication.models import User  # ← tu modelo custom

@pytest.mark.django_db
def test_login_success():
    User.objects.create_user(
        username='testuser',
        email='test@example.com',
        password='strongpassword123'
    )

    client = APIClient()
    response = client.post('/api/auth/login/', {
        'email': 'test@example.com',
        'password': 'strongpassword123'
    })

    assert response.status_code == 200
    assert 'token' in response.data