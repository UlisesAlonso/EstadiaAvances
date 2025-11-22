# Guía de Pruebas - Módulo Seguimiento del Paciente

## 📋 Índice de Pruebas

1. [Acceso y Permisos](#1-acceso-y-permisos)
2. [Selección de Pacientes](#2-selección-de-pacientes)
3. [Vista Principal y Consolidación de Datos](#3-vista-principal-y-consolidación-de-datos)
4. [Sistema de Filtros](#4-sistema-de-filtros)
5. [Gráficas y Visualizaciones](#5-gráficas-y-visualizaciones)
6. [CRUD de Observaciones Médicas](#6-crud-de-observaciones-médicas)
7. [Reportes PDF](#7-reportes-pdf)
8. [Reportes Excel](#8-reportes-excel)
9. [Vista de Pacientes](#9-vista-de-pacientes)
10. [Sistema de Alertas](#10-sistema-de-alertas)
11. [Validaciones y Casos Edge](#11-validaciones-y-casos-edge)

---

## 1. Acceso y Permisos

### ✅ Prueba 1.1: Acceso como Administrador
- [ ] Iniciar sesión como Administrador
- [ ] Navegar a "Seguimiento del Paciente" desde el menú
- [ ] Verificar que se muestra la lista de pacientes
- [ ] Verificar que no hay errores de permisos

### ✅ Prueba 1.2: Acceso como Médico
- [ ] Iniciar sesión como Médico
- [ ] Navegar a "Seguimiento del Paciente" desde el menú
- [ ] Verificar que se muestra la lista de pacientes
- [ ] Verificar que no hay errores de permisos

### ✅ Prueba 1.3: Acceso como Paciente
- [ ] Iniciar sesión como Paciente
- [ ] Navegar a "Seguimiento del Paciente" desde el menú
- [ ] Verificar que se muestra directamente su propio seguimiento
- [ ] Verificar que NO puede acceder a otros pacientes

### ✅ Prueba 1.4: Restricción de Acceso
- [ ] Como Paciente, intentar acceder a seguimiento de otro paciente (URL directa)
- [ ] Verificar que se muestra mensaje de error y redirige
- [ ] Verificar que no se muestran datos de otros pacientes

---

## 2. Selección de Pacientes

### ✅ Prueba 2.1: Lista de Pacientes (Admin/Médico)
- [ ] Acceder como Admin/Médico sin especificar paciente
- [ ] Verificar que se muestra lista de pacientes
- [ ] Verificar que se muestran: nombre, apellidos, correo, edad
- [ ] Verificar que hay botón "Ver Seguimiento" en cada tarjeta

### ✅ Prueba 2.2: Búsqueda de Pacientes
- [ ] En la lista de pacientes, buscar por nombre
- [ ] Verificar que filtra correctamente
- [ ] Buscar por apellido
- [ ] Buscar por correo
- [ ] Verificar que el botón "Limpiar" funciona

### ✅ Prueba 2.3: Paginación
- [ ] Si hay más de 15 pacientes, verificar paginación
- [ ] Navegar entre páginas
- [ ] Verificar que la búsqueda se mantiene en todas las páginas

### ✅ Prueba 2.4: Seleccionar Paciente
- [ ] Hacer clic en "Ver Seguimiento" de un paciente
- [ ] Verificar que redirige a la vista de seguimiento
- [ ] Verificar que se muestra la información del paciente correcto

---

## 3. Vista Principal y Consolidación de Datos

### ✅ Prueba 3.1: Header del Paciente
- [ ] Verificar que se muestra nombre completo del paciente
- [ ] Verificar que se muestra correo
- [ ] Verificar que se muestra edad (si tiene fecha de nacimiento)
- [ ] Verificar botón "Volver" funciona

### ✅ Prueba 3.2: Panel de Estadísticas
- [ ] Verificar que se muestran las 5 estadísticas:
  - [ ] Total de Citas
  - [ ] Tratamientos Activos
  - [ ] Actividades Pendientes
  - [ ] Análisis Recientes
  - [ ] Diagnósticos Totales
- [ ] Verificar que los números son correctos
- [ ] Verificar barras de progreso de cumplimiento

### ✅ Prueba 3.3: Timeline de Eventos
- [ ] Verificar que se muestra timeline con eventos consolidados
- [ ] Verificar que incluye:
  - [ ] Citas
  - [ ] Diagnósticos
  - [ ] Tratamientos
  - [ ] Actividades
  - [ ] Análisis
- [ ] Verificar que están ordenados por fecha (más recientes primero)
- [ ] Verificar que cada evento muestra:
  - [ ] Tipo de evento
  - [ ] Fecha
  - [ ] Descripción
  - [ ] Médico responsable

### ✅ Prueba 3.4: Datos Vacíos
- [ ] Seleccionar un paciente sin datos (nuevo paciente)
- [ ] Verificar que no hay errores
- [ ] Verificar que se muestran mensajes apropiados o "Sin datos"
- [ ] Verificar que las estadísticas muestran 0

---

## 4. Sistema de Filtros

### ✅ Prueba 4.1: Filtro por Fecha
- [ ] Seleccionar "Fecha Desde"
- [ ] Seleccionar "Fecha Hasta"
- [ ] Aplicar filtro
- [ ] Verificar que solo se muestran eventos en ese rango
- [ ] Verificar que el timeline se actualiza
- [ ] Verificar que las estadísticas se recalculan

### ✅ Prueba 4.2: Filtro por Tipo de Información
- [ ] Seleccionar "Citas" en tipo de información
- [ ] Aplicar filtro
- [ ] Verificar que solo se muestran citas en el timeline
- [ ] Probar con cada tipo: Diagnósticos, Tratamientos, Actividades, Análisis

### ✅ Prueba 4.3: Filtro por Diagnóstico
- [ ] Si el paciente tiene diagnósticos, seleccionar uno
- [ ] Aplicar filtro
- [ ] Verificar que se filtran eventos relacionados
- [ ] Verificar que las estadísticas se actualizan

### ✅ Prueba 4.4: Combinación de Filtros
- [ ] Aplicar múltiples filtros simultáneamente
- [ ] Verificar que todos se aplican correctamente
- [ ] Verificar que el botón "Limpiar Filtros" funciona

### ✅ Prueba 4.5: Persistencia de Filtros
- [ ] Aplicar filtros
- [ ] Navegar a otra sección y volver
- [ ] Verificar que los filtros se mantienen (si está implementado)

---

## 5. Gráficas y Visualizaciones

### ✅ Prueba 5.1: Gráfica de Barras - Citas por Mes
- [ ] Verificar que se muestra la gráfica
- [ ] Verificar que tiene datos (si hay citas)
- [ ] Verificar que muestra: Total, Completadas, Canceladas
- [ ] Verificar colores diferentes para cada serie
- [ ] Verificar que el tooltip funciona al pasar el mouse

### ✅ Prueba 5.2: Gráfica de Líneas - Evolución de Actividades
- [ ] Verificar que se muestra la gráfica
- [ ] Verificar que muestra: Total vs Completadas
- [ ] Verificar que las líneas son visibles y diferentes
- [ ] Verificar que el tooltip funciona

### ✅ Prueba 5.3: Gráfica Circular - Distribución de Eventos
- [ ] Verificar que se muestra la gráfica
- [ ] Verificar que muestra porcentajes por tipo de evento
- [ ] Verificar que tiene leyenda
- [ ] Verificar que el tooltip muestra información correcta

### ✅ Prueba 5.4: Gráfica de Barras - Actividades por Tipo
- [ ] Verificar que se muestra la gráfica
- [ ] Verificar que muestra cumplimiento por tipo de actividad
- [ ] Verificar que los colores son apropiados

### ✅ Prueba 5.5: Gráficas sin Datos
- [ ] Seleccionar paciente sin datos suficientes
- [ ] Verificar que las gráficas no causan errores
- [ ] Verificar que muestran mensaje apropiado o gráfica vacía

---

## 6. CRUD de Observaciones Médicas

### ✅ Prueba 6.1: Crear Observación
- [ ] Como Médico/Admin, hacer clic en "Nueva Observación"
- [ ] Verificar que se muestra formulario
- [ ] Llenar todos los campos:
  - [ ] Observación (texto)
  - [ ] Fecha de observación
  - [ ] Tipo (opcional)
- [ ] Guardar
- [ ] Verificar mensaje de éxito
- [ ] Verificar que aparece en la lista de observaciones
- [ ] Verificar que aparece en el timeline

### ✅ Prueba 6.2: Validación de Crear Observación
- [ ] Intentar crear sin llenar campos obligatorios
- [ ] Verificar mensajes de error
- [ ] Verificar que no se guarda

### ✅ Prueba 6.3: Editar Observación
- [ ] Hacer clic en "Editar" de una observación
- [ ] Verificar que se pre-llenan los datos
- [ ] Modificar algún campo
- [ ] Guardar
- [ ] Verificar mensaje de éxito
- [ ] Verificar que los cambios se reflejan

### ✅ Prueba 6.4: Eliminar Observación
- [ ] Hacer clic en "Eliminar" de una observación
- [ ] Confirmar eliminación
- [ ] Verificar mensaje de éxito
- [ ] Verificar que desaparece de la lista
- [ ] Verificar que desaparece del timeline

### ✅ Prueba 6.5: Permisos de Observaciones
- [ ] Como Médico, crear observación
- [ ] Verificar que solo ese médico puede editarla/eliminarla
- [ ] Como otro Médico, verificar que no puede editarla
- [ ] Como Admin, verificar que puede ver todas

### ✅ Prueba 6.6: Lista de Observaciones
- [ ] Verificar que se muestran todas las observaciones
- [ ] Verificar que se muestran: fecha, tipo, observación, médico
- [ ] Verificar orden (más recientes primero)
- [ ] Verificar que los filtros afectan las observaciones

---

## 7. Reportes PDF

### ✅ Prueba 7.1: Generar Reporte PDF
- [ ] Hacer clic en "Exportar PDF"
- [ ] Verificar que se descarga el archivo
- [ ] Verificar nombre del archivo (formato correcto)
- [ ] Abrir el PDF

### ✅ Prueba 7.2: Contenido del PDF
- [ ] Verificar que incluye información del paciente
- [ ] Verificar que incluye estadísticas
- [ ] Verificar que incluye timeline de eventos
- [ ] Verificar que incluye observaciones médicas
- [ ] Verificar formato y legibilidad

### ✅ Prueba 7.3: PDF con Filtros
- [ ] Aplicar filtros
- [ ] Generar PDF
- [ ] Verificar que el PDF respeta los filtros aplicados
- [ ] Verificar que solo muestra datos filtrados

### ✅ Prueba 7.4: PDF sin Datos
- [ ] Seleccionar paciente sin datos
- [ ] Generar PDF
- [ ] Verificar que no hay errores
- [ ] Verificar que muestra mensajes apropiados

---

## 8. Reportes Excel

### ✅ Prueba 8.1: Generar Reporte Excel
- [ ] Hacer clic en "Exportar Excel"
- [ ] Verificar que se descarga el archivo
- [ ] Verificar nombre del archivo (formato correcto)
- [ ] Abrir el archivo Excel

### ✅ Prueba 8.2: Hojas del Excel
- [ ] Verificar que tiene 3 hojas:
  - [ ] Hoja 1: Resumen
  - [ ] Hoja 2: Timeline
  - [ ] Hoja 3: Observaciones
- [ ] Verificar que cada hoja tiene datos correctos

### ✅ Prueba 8.3: Contenido de las Hojas
- [ ] **Hoja Resumen**: Verificar datos del paciente y estadísticas
- [ ] **Hoja Timeline**: Verificar eventos consolidados
- [ ] **Hoja Observaciones**: Verificar observaciones médicas
- [ ] Verificar formato y columnas

### ✅ Prueba 8.4: Excel con Filtros
- [ ] Aplicar filtros
- [ ] Generar Excel
- [ ] Verificar que respeta los filtros
- [ ] Verificar que solo muestra datos filtrados

---

## 9. Vista de Pacientes

### ✅ Prueba 9.1: Acceso como Paciente
- [ ] Iniciar sesión como Paciente
- [ ] Navegar a "Mi Seguimiento"
- [ ] Verificar que se muestra su información
- [ ] Verificar que NO puede ver otros pacientes

### ✅ Prueba 9.2: Funcionalidades de Paciente
- [ ] Verificar que puede ver:
  - [ ] Estadísticas
  - [ ] Timeline
  - [ ] Gráficas
  - [ ] Alertas
  - [ ] Observaciones (solo lectura)
- [ ] Verificar que NO puede:
  - [ ] Crear observaciones
  - [ ] Editar observaciones
  - [ ] Eliminar observaciones
  - [ ] Exportar reportes

### ✅ Prueba 9.3: Filtros para Paciente
- [ ] Verificar que puede usar filtros
- [ ] Verificar que los filtros funcionan correctamente
- [ ] Verificar que solo ve sus propios datos

---

## 10. Sistema de Alertas

### ✅ Prueba 10.1: Alertas de Citas
- [ ] Crear cita próxima (dentro de 7 días)
- [ ] Verificar que aparece alerta "Cita programada"
- [ ] Verificar nivel de alerta (info/warning según días)
- [ ] Crear cita vencida sin completar
- [ ] Verificar que aparece alerta "Cita vencida" (danger)

### ✅ Prueba 10.2: Alertas de Actividades
- [ ] Crear actividad por vencer (dentro de 3 días)
- [ ] Verificar que aparece alerta "Actividad por vencer"
- [ ] Verificar nivel (warning/danger según días)
- [ ] Crear actividad vencida sin completar
- [ ] Verificar que aparece alerta "Actividad vencida" (danger)

### ✅ Prueba 10.3: Alertas de Tratamientos
- [ ] Crear tratamiento activo
- [ ] Verificar que aparece alerta "Tratamiento(s) activo(s)"
- [ ] Verificar nivel (info)

### ✅ Prueba 10.4: Alertas de Preguntas
- [ ] Asignar pregunta a paciente
- [ ] Verificar que aparece alerta "Pregunta(s) pendiente(s)"
- [ ] Responder pregunta
- [ ] Verificar que desaparece la alerta

### ✅ Prueba 10.5: Alertas de Análisis
- [ ] Crear análisis reciente (últimos 7 días)
- [ ] Verificar que aparece alerta "Análisis reciente(s)"
- [ ] Verificar nivel (info)

### ✅ Prueba 10.6: Priorización de Alertas
- [ ] Verificar que las alertas se ordenan por prioridad:
  - [ ] danger primero
  - [ ] warning segundo
  - [ ] info último
- [ ] Verificar que dentro del mismo nivel, más recientes primero

### ✅ Prueba 10.7: Contador de Alertas
- [ ] Verificar que se muestra contador de alertas
- [ ] Verificar que el color cambia según el nivel más alto
- [ ] Verificar que el número es correcto

---

## 11. Validaciones y Casos Edge

### ✅ Prueba 11.1: Paciente sin Datos
- [ ] Seleccionar paciente completamente nuevo (sin ningún registro)
- [ ] Verificar que no hay errores
- [ ] Verificar que se muestran 0 en estadísticas
- [ ] Verificar que timeline está vacío pero funcional
- [ ] Verificar que gráficas no causan errores

### ✅ Prueba 11.2: Datos con Relaciones Nulas
- [ ] Verificar que si un evento tiene médico nulo, no causa error
- [ ] Verificar que muestra "N/A" apropiadamente
- [ ] Verificar que si falta descripción, muestra "Sin descripción"

### ✅ Prueba 11.3: Fechas Inválidas
- [ ] Probar filtros con fechas inválidas
- [ ] Verificar que se muestran mensajes de error apropiados
- [ ] Verificar que no se rompe la aplicación

### ✅ Prueba 11.4: Búsqueda de Pacientes
- [ ] Buscar con texto que no existe
- [ ] Verificar que muestra mensaje "No se encontraron pacientes"
- [ ] Buscar con caracteres especiales
- [ ] Verificar que no causa errores

### ✅ Prueba 11.5: Navegación
- [ ] Navegar entre diferentes secciones
- [ ] Volver al seguimiento
- [ ] Verificar que los datos se cargan correctamente
- [ ] Verificar que no hay errores de sesión

### ✅ Prueba 11.6: Rendimiento
- [ ] Seleccionar paciente con muchos datos (100+ eventos)
- [ ] Verificar que la página carga en tiempo razonable (< 3 segundos)
- [ ] Verificar que las gráficas se renderizan correctamente
- [ ] Verificar que los filtros funcionan rápidamente

### ✅ Prueba 11.7: Responsive Design
- [ ] Probar en diferentes tamaños de pantalla:
  - [ ] Desktop (1920x1080)
  - [ ] Tablet (768x1024)
  - [ ] Mobile (375x667)
- [ ] Verificar que todo es legible y funcional
- [ ] Verificar que las gráficas se adaptan

---

## ✅ Checklist Final

Antes de liberar, verificar:

- [ ] Todas las pruebas anteriores pasaron
- [ ] No hay errores en la consola del navegador
- [ ] No hay errores en los logs de Laravel
- [ ] Los mensajes de éxito/error se muestran correctamente
- [ ] Las validaciones funcionan en todos los formularios
- [ ] Los permisos están correctamente implementados
- [ ] Los reportes se generan sin errores
- [ ] Las gráficas se muestran correctamente
- [ ] El sistema de alertas funciona
- [ ] La búsqueda de pacientes funciona
- [ ] Los filtros funcionan correctamente
- [ ] El timeline muestra todos los eventos
- [ ] Las estadísticas son precisas

---

## 📝 Notas de Pruebas

**Fecha de Pruebas:** _______________

**Probado por:** _______________

**Resultados:**
- Total de pruebas: 80+
- Pruebas pasadas: _____
- Pruebas fallidas: _____
- Observaciones: _______________

---

## 🐛 Reporte de Errores

Si encuentras algún error durante las pruebas, documenta:

1. **Descripción del error**
2. **Pasos para reproducir**
3. **Comportamiento esperado**
4. **Comportamiento actual**
5. **Capturas de pantalla (si aplica)**
6. **Navegador y versión**
7. **Rol de usuario**

---

¡Buena suerte con las pruebas! 🚀

