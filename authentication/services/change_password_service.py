class ChangePasswordService:
    @staticmethod
    def change_password(user, old_password, new_password):
        # Si la lógica debe ir por API, aquí se haría la llamada remota
        # Por defecto, usa el modelo local si está permitido
        if not user.check_password(old_password):
            return {'success': False, 'error': 'Contraseña actual incorrecta.'}
        user.set_password(new_password)
        user.save()
        return {'success': True}
