# Instrucciones para Migración del Sistema RBAC

## Paso 1: Ejecutar el Script SQL

Ejecuta el archivo `create_rbac_system.sql` en tu base de datos MySQL:

```bash
# Opción 1: Desde línea de comandos
mysql -u root -p nacho < create_rbac_system.sql

# Opción 2: Desde phpMyAdmin
# 1. Abre phpMyAdmin
# 2. Selecciona la base de datos "nacho"
# 3. Ve a la pestaña "SQL"
# 4. Copia y pega el contenido de create_rbac_system.sql
# 5. Haz clic en "Continuar"
```

## Paso 2: Verificar la Migración

Después de ejecutar el script, verifica que:

1. **Tablas creadas:**
   - `roles` - Debe tener 2 registros (Administrador, Ventas)
   - `role_permissions` - Debe tener permisos configurados para ambos roles

2. **Tabla users actualizada:**
   - Debe tener la columna `role_id`
   - Los usuarios existentes deben tener `role_id` asignado (1 para admin, 2 para ventas)

3. **Consultas de verificación:**
```sql
-- Ver roles creados
SELECT * FROM roles;

-- Ver permisos del rol Administrador
SELECT * FROM role_permissions WHERE role_id = 1;

-- Ver usuarios con sus roles
SELECT id, username, email, role, role_id FROM users;
```

## Paso 3: Eliminar Columna Antigua (Opcional)

Una vez que verifiques que todo funciona correctamente con el nuevo sistema, puedes eliminar la columna `role` antigua:

```sql
ALTER TABLE users DROP COLUMN role;
```

**⚠️ IMPORTANTE:** Solo ejecuta esto después de confirmar que el sistema funciona correctamente con `role_id`.

## Paso 4: Probar el Sistema

1. **Cerrar sesión** de todos los usuarios activos
2. **Iniciar sesión** nuevamente para cargar los permisos en la sesión
3. **Acceder al módulo de Roles** desde Sistema → Roles
4. **Crear un rol de prueba** con permisos limitados
5. **Verificar** que los permisos se aplican correctamente

## Notas Importantes

- Los roles "Administrador" y "Ventas" son roles del sistema y no se pueden eliminar
- El rol "Administrador" tiene todos los permisos habilitados
- El rol "Ventas" tiene permisos limitados (principalmente consulta y operaciones de ventas)
- Los permisos se cargan en la sesión al iniciar sesión
- Si cambias los permisos de un rol, los usuarios con ese rol deben cerrar sesión y volver a iniciar para que los cambios surtan efecto
