<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Permissions for the Admin and Member roles
        // Appointments
        Permission::create(['name' => 'appointments.index', 'detail' => 'Muestra todos las citas'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'appointments.show', 'detail' => 'Muestra los detalles de una cita'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'appointments.store', 'detail' => 'Crea una nueva cita'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'appointments.update', 'detail' => 'Actualiza una cita existente'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'appointments.destroy', 'detail' => 'Elimina una cita'])->syncRoles('Admin', 'Member');

        // Appointments Search
        Permission::create(['name' => 'appointments.search', 'detail' => 'Buscar citas'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'appointments.search_completed', 'detail' => 'Buscar citas completadas'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'appointments.paginated_by_date', 'detail' => 'Obtener citas paginadas por fecha'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'appointments.completed_paginated_by_date', 'detail' => 'Obtener citas completadas paginadas por fecha'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'appointments.pending_calendar_by_date', 'detail' => 'Citas pendientes para el calendario por fecha'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'appointments.completed_calendar_by_date', 'detail' => 'Citas completadas para el calendario por fecha'])->syncRoles('Admin', 'Member');

        // Histories
        Permission::create(['name' => 'histories.index', 'detail' => 'Muestra todas historias'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'histories.show', 'detail' => 'Muestra los detalles de una historia'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'histories.store', 'detail' => 'Crea una nueva historia'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'histories.update', 'detail' => 'Actualiza una historia existente'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'histories.destroy', 'detail' => 'Elimina una historia'])->syncRoles('Admin', 'Member');
    
        Permission::create(['name' => 'histories.getByPatient', 'detail' => 'Muestra todas historias de un paciente'])->syncRoles('Admin', 'Member');

        // Patients
        Permission::create(['name' => 'patients.index', 'detail' => 'Muestra todos los pacientes'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'patients.show', 'detail' => 'Muestra los detalles de un paciente'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'patients.store', 'detail' => 'Crea un nuevo paciente'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'patients.update', 'detail' => 'Actualiza un paciente existente'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'patients.destroy', 'detail' => 'Elimina un paciente'])->syncRoles('Admin', 'Member');
        Permission::create(['name'=>'patients.appointments_by_patient', 'detail'=>'Obtener citas por paciente'])->syncRoles('Admin', 'Member');

        // Patients Search
        Permission::create(['name' => 'patients.search', 'detail' => 'Buscar pacientes'])->syncRoles('Admin', 'Member');

        // Therapists
        Permission::create(['name' => 'therapists.index', 'detail' => 'Muestra todos los terapeutas'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'therapists.show', 'detail' => 'Muestra los detalles de un terapeuta'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'therapists.store', 'detail' => 'Crea un nuevo terapeuta'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'therapists.update', 'detail' => 'Actualiza un terapeuta existente'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'therapists.destroy', 'detail' => 'Elimina un terapeuta'])->syncRoles('Admin', 'Member');

        // Therapists Search
        Permission::create(['name' => 'therapists.search', 'detail' => 'Buscar terapeutas'])->syncRoles('Admin', 'Member');

        // Tickets
        Permission::create(['name' => 'tickets.index', 'detail' => 'Ver listado de tickets'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'tickets.available', 'detail' => 'Ver tickets disponibles'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'tickets.nextRoom', 'detail' => 'Obtener siguiente número de sala disponible'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'tickets.nextTicket', 'detail' => 'Obtener siguiente número de ticket disponible'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'tickets.stats', 'detail' => 'Ver estadísticas de uso de tickets y salas'])->syncRoles('Admin', 'Member');

        // Statistics
        Permission::create(['name' => 'statistics.view', 'detail' => 'Ver estadísticas generales'])->syncRoles('Admin', 'Member');

        // Reports
        Permission::create(['name' => 'reports.getNumberAppointmentsPerTherapist', 'detail' => 'Obtiene el número de citas por terapeuta'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'reports.getPatientsByTherapist', 'detail' => 'Obtiene los pacientes por terapeuta'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'reports.getDailyCash', 'detail' => 'Obtiene el efectivo diario'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'reports.getAppointmentsBetweenDates', 'detail' => 'Obtiene las citas entre fechas'])->syncRoles('Admin', 'Member');

        // Change Password
        Permission::create(['name' => 'change-password.update', 'detail' => 'Actualiza la contraseña'])->syncRoles('Admin', 'Member');

        // Profile
        Permission::create(['name' => 'profile.show', 'detail' => 'Muestra el perfil del usuario logueado'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'profile.update', 'detail' => 'Actualiza el perfil del usuario logueado'])->syncRoles('Admin', 'Member');

        // Permissions for the Admin role only
        // Document Types
        Permission::create(['name' => 'document-types.index', 'detail' => 'Muestra todos los tipos de documentos'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'document-types.show', 'detail' => 'Muestra los detalles de un tipo de documento'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'document-types.store', 'detail' => 'Crea un nuevo tipo de documento'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'document-types.update', 'detail' => 'Actualiza un tipo de documento existente'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'document-types.destroy', 'detail' => 'Elimina un tipo de documento'])->syncRoles('Admin', 'Member');

        // Payment Types
        Permission::create(['name' => 'payment-types.index', 'detail' => 'Muestra todos los tipos de pago'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'payment-types.show', 'detail' => 'Muestra los detalles de un tipo de pago'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'payment-types.store', 'detail' => 'Crea un nuevo tipo de pago'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'payment-types.update', 'detail' => 'Actualiza un tipo de pago existente'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'payment-types.destroy', 'detail' => 'Elimina un tipo de pago'])->syncRoles('Admin', 'Member');

        // Predetermined Prices
        Permission::create(['name' => 'predetermined-prices.index', 'detail' => 'Muestra todos los precios predeterminados'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'predetermined-prices.show', 'detail' => 'Muestra los detalles de un precio predeterminado'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'predetermined-prices.store', 'detail' => 'Crea un nuevo precio predeterminado'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'predetermined-prices.update', 'detail' => 'Actualiza un precio predeterminado existente'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'predetermined-prices.destroy', 'detail' => 'Elimina un precio predeterminado'])->syncRoles('Admin', 'Member');

        // Appointment Statuses
        Permission::create(['name' => 'appointment-statuses.index', 'detail' => 'Muestra todos los estados de las citas'])->syncRoles('Admin');
        Permission::create(['name' => 'appointment-statuses.show', 'detail' => 'Muestra los detalles de un estado de cita'])->syncRoles('Admin');
        Permission::create(['name' => 'appointment-statuses.store', 'detail' => 'Crea un nuevo estado de cita'])->syncRoles('Admin');
        Permission::create(['name' => 'appointment-statuses.update', 'detail' => 'Actualiza un estado de cita existente'])->syncRoles('Admin');
        Permission::create(['name' => 'appointment-statuses.destroy', 'detail' => 'Elimina un estado de cita'])->syncRoles('Admin');

        // Users
        Permission::create(['name' => 'users.index', 'detail' => 'Muestra todos los usuarios'])->syncRoles('Admin');
        Permission::create(['name' => 'users.show', 'detail' => 'Muestra los detalles de un usuario'])->syncRoles('Admin');
        Permission::create(['name' => 'users.store', 'detail' => 'Crea un nuevo usuario'])->syncRoles('Admin');
        Permission::create(['name' => 'users.update', 'detail' => 'Actualiza un usuario existente'])->syncRoles('Admin');
        Permission::create(['name' => 'users.destroy', 'detail' => 'Elimina un usuario'])->syncRoles('Admin');
        //Users-Image
        Permission::create(['name' => 'users.photo.upload', 'detail' => 'Recibe foto de usuario'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'users.photo.show', 'detail' => 'Recibe foto de usuario'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'users.photo.delete', 'detail' => 'Recibe foto de usuario'])->syncRoles('Admin', 'Member');


        // Nuevos métodos exclusivos del Admin
        Permission::create(['name' => 'users.search', 'detail' => 'Buscar usuarios'])->syncRoles('Admin');

        //Company Data
        Permission::create(['name' => 'company.view', 'detail' => 'Ver datos de la empresa'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'company.create', 'detail' => 'Crear/Actualizar datos de la empresa'])->syncRoles('Admin');
        Permission::create(['name' => 'company.delete', 'detail' => 'Eliminar datos de la empresa'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'company.logo.upload', 'detail' => 'Subir logo de la empresa'])->syncRoles('Admin');
        Permission::create(['name' => 'company.logo.show', 'detail' => 'Ver logo de la empresa'])->syncRoles('Admin', 'Member');
        Permission::create(['name' => 'company.logo.delete', 'detail' => 'Eliminar logo de la empresa'])->syncRoles('Admin');
    }
}
