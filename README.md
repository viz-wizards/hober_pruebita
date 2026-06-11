# Sistema de agendas - Barberia Corte Urbano

Sistema web en PHP y MySQL para gestionar citas de una barberia existente/simulada: clientes, empleados, servicios, agenda diaria y pagos.

## Requisitos de la imagen

- Modelado de base de datos en DIA: `docs/modelado_dia.dia`
- Diseno de interfaces tipo Figma: `docs/diseno_interfaces_figma.md` y `prototipo_figma.html`
- Codigo de la base de datos: `database.sql`
- Modelo fisico de la base de datos: `docs/modelo_fisico.md`
- Base de datos con 5 tablas sin contar usuarios: `clientes`, `empleados`, `servicios`, `citas`, `pagos`
- Negocio existente: Barberia Corte Urbano
- Codigo fuente del sistema: archivos PHP, CSS y JS incluidos
- Colores segun la logica del negocio: tonos sobrios de barberia, acentos dorados y verdes de estado

## Instalacion en XAMPP

1. Inicia Apache y MySQL en XAMPP.
2. Abre phpMyAdmin e importa `database.sql`.
3. Entra a `http://localhost/Agendas/`.
4. Usuario de prueba: `admin@corteurbano.com`
5. Contrasena: `admin123`

## Funciones

- Inicio de sesion.
- Dashboard con indicadores.
- Crear citas validando disponibilidad por empleado, fecha y hora.
- Listar agenda por fecha.
- Cambiar estados de citas.
- Registrar pagos asociados a citas.
- Gestion basica de clientes y servicios.
