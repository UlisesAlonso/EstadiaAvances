# Guía de Pruebas - Módulo Foro de Experiencias

## 📋 Índice
1. [Preparación](#preparación)
2. [Pruebas como Paciente](#pruebas-como-paciente)
3. [Pruebas como Administrador](#pruebas-como-administrador)
4. [Pruebas de Funcionalidades Avanzadas](#pruebas-de-funcionalidades-avanzadas)
5. [Verificación de Validaciones](#verificación-de-validaciones)
6. [Checklist Final](#checklist-final)

---

## 🔧 Preparación

### 1. Verificar que las migraciones estén ejecutadas
```bash
php artisan migrate:status
```
Debes ver las 4 migraciones del foro:
- ✅ `create_publicaciones_foro_table`
- ✅ `create_comentarios_foro_table`
- ✅ `create_reacciones_foro_table`
- ✅ `create_favoritos_foro_table`

### 2. Verificar que el servidor esté corriendo
```bash
php artisan serve
```

### 3. Tener usuarios de prueba
- **Paciente 1**: Para crear publicaciones
- **Paciente 2**: Para comentar y reaccionar
- **Administrador**: Para moderar

---

## 👤 Pruebas como Paciente

### ✅ Prueba 1: Acceso al Foro

**Pasos:**
1. Inicia sesión como paciente
2. En el menú lateral, haz clic en "Foro de Experiencias"
3. Debe mostrar la página principal del foro

**Resultado esperado:**
- ✅ Se muestra el listado de publicaciones (si hay alguna aprobada)
- ✅ Se ven las estadísticas del foro
- ✅ Aparece el botón "Nueva Publicación"
- ✅ Aparecen los botones "Mis Publicaciones" y "Favoritos"

---

### ✅ Prueba 2: Crear una Publicación

**Pasos:**
1. Haz clic en "Nueva Publicación"
2. Completa el formulario:
   - **Título**: "Mi experiencia con la dieta recomendada"
   - **Contenido**: "Hace 3 meses empecé a seguir la dieta que me recomendó mi médico y he notado mejoras significativas..."
   - **Fecha de la experiencia**: Selecciona una fecha
   - **Etiquetas**: "nutrición, dieta, motivación" (separadas por comas)
3. Haz clic en "Publicar"

**Resultado esperado:**
- ✅ Mensaje de éxito: "Publicación creada exitosamente. Estará visible después de ser aprobada por un administrador."
- ✅ Redirección al listado del foro
- ✅ La publicación NO aparece en el listado (está pendiente)

**Verificar en base de datos:**
```sql
SELECT * FROM publicaciones_foro WHERE estado = 'pendiente';
```

---

### ✅ Prueba 3: Ver Mis Publicaciones

**Pasos:**
1. Haz clic en "Mis Publicaciones"
2. Debe mostrar todas tus publicaciones (pendientes, aprobadas, ocultas)

**Resultado esperado:**
- ✅ Se muestra la publicación creada con estado "Pendiente"
- ✅ Puedes ver el número de reacciones y comentarios (0 inicialmente)
- ✅ Aparecen botones "Ver" y "Editar"

---

### ✅ Prueba 4: Editar una Publicación

**Pasos:**
1. En "Mis Publicaciones", haz clic en "Editar" de una publicación pendiente
2. Modifica el título o contenido
3. Haz clic en "Guardar Cambios"

**Resultado esperado:**
- ✅ Mensaje de advertencia sobre re-moderación
- ✅ La publicación se actualiza
- ✅ El estado vuelve a "pendiente" (verificar en BD)

---

### ✅ Prueba 5: Ver una Publicación Individual (Pendiente)

**Pasos:**
1. En "Mis Publicaciones", haz clic en "Ver"
2. O ve directamente a la publicación desde el listado

**Resultado esperado:**
- ✅ Se muestra el título, contenido, autor, fecha
- ✅ Se muestran las etiquetas
- ✅ Si tiene actividad/tratamiento relacionado, se muestra
- ✅ Aparece el badge "Pendiente de aprobación"
- ✅ NO aparecen botones de reacción/favorito (solo en aprobadas)

---

### ✅ Prueba 6: Eliminar una Publicación

**Pasos:**
1. En "Mis Publicaciones" o en la vista individual, haz clic en "Eliminar"
2. Confirma la eliminación

**Resultado esperado:**
- ✅ Mensaje de confirmación
- ✅ La publicación se elimina
- ✅ Ya no aparece en "Mis Publicaciones"

---

## 🔐 Pruebas como Administrador

### ✅ Prueba 7: Acceso a Moderación

**Pasos:**
1. Inicia sesión como administrador
2. En el menú lateral, haz clic en "Moderar Foro"
3. O desde el foro principal, haz clic en "Moderación"

**Resultado esperado:**
- ✅ Se muestra el panel de moderación
- ✅ Estadísticas: Pendientes, Aprobadas, Ocultas, Total
- ✅ Por defecto muestra publicaciones "Pendientes"

---

### ✅ Prueba 8: Aprobar una Publicación

**Pasos:**
1. En el panel de moderación, encuentra una publicación pendiente
2. Haz clic en "Aprobar"

**Resultado esperado:**
- ✅ Mensaje de éxito
- ✅ La publicación cambia de estado a "aprobada"
- ✅ Ya no aparece en "Pendientes"
- ✅ Aparece en el filtro "Aprobadas"
- ✅ **IMPORTANTE**: Ahora los pacientes pueden verla en el foro principal

**Verificar:**
- Cierra sesión como admin
- Inicia sesión como paciente
- Ve al foro principal
- ✅ La publicación aprobada debe aparecer

---

### ✅ Prueba 9: Ocultar una Publicación

**Pasos:**
1. Como administrador, en moderación, filtra por "Aprobadas"
2. Encuentra una publicación aprobada
3. Haz clic en "Ocultar"

**Resultado esperado:**
- ✅ Mensaje de éxito
- ✅ La publicación cambia a estado "oculta"
- ✅ Los pacientes ya NO pueden verla en el foro
- ✅ Aparece en el filtro "Ocultas"

---

### ✅ Prueba 10: Eliminar una Publicación (Admin)

**Pasos:**
1. Como administrador, encuentra cualquier publicación
2. Haz clic en "Eliminar"
3. Confirma la eliminación

**Resultado esperado:**
- ✅ Mensaje de confirmación
- ✅ La publicación se elimina permanentemente
- ✅ También se eliminan sus comentarios, reacciones y favoritos (cascade)

---

## 💬 Pruebas de Funcionalidades Avanzadas

### ✅ Prueba 11: Comentar una Publicación

**Pasos:**
1. Como paciente, ve al foro principal
2. Abre una publicación aprobada
3. En la sección de comentarios, escribe un comentario
4. Haz clic en "Comentar"

**Resultado esperado:**
- ✅ El comentario aparece inmediatamente
- ✅ Se muestra el nombre del autor y la fecha
- ✅ El contador de comentarios se actualiza
- ✅ Solo puedes comentar en publicaciones aprobadas

---

### ✅ Prueba 12: Editar un Comentario

**Pasos:**
1. En una publicación, encuentra un comentario tuyo
2. Haz clic en el icono de editar (si está disponible)
3. Modifica el contenido
4. Guarda los cambios

**Nota:** Si no hay botón de editar en la vista, puedes probar directamente la ruta:
```
PUT /paciente/foro/{id}/comentarios/{idComentario}
```

**Resultado esperado:**
- ✅ El comentario se actualiza
- ✅ Se mantiene la fecha original

---

### ✅ Prueba 13: Eliminar un Comentario

**Pasos:**
1. En una publicación, encuentra un comentario tuyo
2. Haz clic en el icono de eliminar
3. Confirma la eliminación

**Resultado esperado:**
- ✅ El comentario desaparece
- ✅ El contador de comentarios se actualiza
- ✅ Solo puedes eliminar tus propios comentarios (o admin puede eliminar cualquiera)

---

### ✅ Prueba 14: Reaccionar a una Publicación

**Pasos:**
1. Como paciente, abre una publicación aprobada
2. Haz clic en el botón "Me gusta" (corazón)
3. Observa el cambio

**Resultado esperado:**
- ✅ El botón cambia de color (se llena)
- ✅ El contador aumenta
- ✅ Si vuelves a hacer clic, se quita la reacción (toggle)
- ✅ El contador disminuye

**Verificar en BD:**
```sql
SELECT * FROM reacciones_foro WHERE id_publicacion = [ID];
```

---

### ✅ Prueba 15: Marcar como Favorito

**Pasos:**
1. Como paciente, abre una publicación aprobada
2. Haz clic en "Agregar a favoritos" (estrella)
3. Observa el cambio

**Resultado esperado:**
- ✅ El botón cambia a "En favoritos" (amarillo)
- ✅ La estrella se llena
- ✅ Si vuelves a hacer clic, se quita de favoritos (toggle)

---

### ✅ Prueba 16: Ver Mis Favoritos

**Pasos:**
1. Como paciente, haz clic en "Favoritos" en el menú
2. Debe mostrar todas las publicaciones que marcaste como favoritas

**Resultado esperado:**
- ✅ Se muestran solo las publicaciones favoritas
- ✅ Estadísticas: Total favoritos, Total publicaciones
- ✅ Puedes filtrar y ordenar

---

## 🔍 Pruebas de Búsqueda y Filtros

### ✅ Prueba 17: Búsqueda por Palabra Clave

**Pasos:**
1. En el foro principal, usa el campo "Buscar"
2. Escribe una palabra que esté en el título o contenido de una publicación
3. Haz clic en "Filtrar"

**Resultado esperado:**
- ✅ Solo aparecen publicaciones que contengan esa palabra
- ✅ La búsqueda busca en título, contenido y etiquetas

---

### ✅ Prueba 18: Filtrar por Etiqueta

**Pasos:**
1. En el foro principal, selecciona una etiqueta del dropdown
2. Haz clic en "Filtrar"

**Resultado esperado:**
- ✅ Solo aparecen publicaciones con esa etiqueta
- ✅ Las etiquetas se muestran como badges en cada publicación

---

### ✅ Prueba 19: Filtrar por Fecha

**Pasos:**
1. En el foro principal, selecciona "Fecha Desde" y "Fecha Hasta"
2. Haz clic en "Filtrar"

**Resultado esperado:**
- ✅ Solo aparecen publicaciones en ese rango de fechas
- ✅ Las fechas se muestran correctamente

---

### ✅ Prueba 20: Ordenar Publicaciones

**Pasos:**
1. En el foro principal, cambia el ordenamiento:
   - Por Fecha (más recientes)
   - Por Relevancia
   - Por Popularidad
   - Por Comentarios
2. Observa cómo cambia el orden

**Resultado esperado:**
- ✅ Cada opción ordena correctamente
- ✅ Por defecto es "Fecha (más recientes)"

---

## ⚠️ Verificación de Validaciones

### ✅ Prueba 21: Validación de Campos Requeridos

**Pasos:**
1. Intenta crear una publicación sin título
2. Intenta crear una publicación sin contenido
3. Intenta crear una publicación sin fecha

**Resultado esperado:**
- ✅ Mensajes de error específicos para cada campo
- ✅ El formulario no se envía
- ✅ Los campos con error se resaltan en rojo

---

### ✅ Prueba 22: Validación de Permisos

**Pasos:**
1. Como paciente, intenta acceder directamente a:
   - `/admin/foro/moderacion`
   - Intentar aprobar una publicación (POST directo)

**Resultado esperado:**
- ✅ Redirección con mensaje de error
- ✅ Solo administradores pueden moderar

---

### ✅ Prueba 23: Validación de Propiedad

**Pasos:**
1. Como Paciente 1, crea una publicación
2. Inicia sesión como Paciente 2
3. Intenta editar o eliminar la publicación del Paciente 1

**Resultado esperado:**
- ✅ No puedes editar/eliminar publicaciones de otros
- ✅ Solo puedes editar/eliminar tus propias publicaciones

---

### ✅ Prueba 24: Validación de Estado

**Pasos:**
1. Como paciente, intenta comentar o reaccionar a una publicación pendiente (que no sea tuya)

**Resultado esperado:**
- ✅ No puedes comentar/reaccionar en publicaciones pendientes
- ✅ Solo puedes interactuar con publicaciones aprobadas

---

## 🔗 Pruebas de Enlaces Opcionales

### ✅ Prueba 25: Vincular con Actividad

**Pasos:**
1. Crea una publicación
2. En el formulario, selecciona una actividad completada del dropdown
3. Guarda la publicación
4. Ve a la publicación aprobada

**Resultado esperado:**
- ✅ Se muestra un badge/panel indicando la actividad relacionada
- ✅ El nombre de la actividad aparece

---

### ✅ Prueba 26: Vincular con Tratamiento

**Pasos:**
1. Crea una publicación
2. En el formulario, selecciona un tratamiento activo del dropdown
3. Guarda la publicación
4. Ve a la publicación aprobada

**Resultado esperado:**
- ✅ Se muestra un badge/panel indicando el tratamiento relacionado
- ✅ El nombre del tratamiento aparece

---

## ✅ Checklist Final

### Funcionalidades Básicas
- [ ] Crear publicación
- [ ] Editar publicación
- [ ] Eliminar publicación
- [ ] Ver publicación individual
- [ ] Ver mis publicaciones

### Interacciones
- [ ] Comentar publicación
- [ ] Editar comentario
- [ ] Eliminar comentario
- [ ] Reaccionar (me gusta)
- [ ] Marcar como favorito
- [ ] Ver favoritos

### Moderación
- [ ] Acceder a panel de moderación
- [ ] Aprobar publicación
- [ ] Ocultar publicación
- [ ] Eliminar publicación (admin)
- [ ] Ver estadísticas de moderación

### Búsqueda y Filtros
- [ ] Buscar por palabra clave
- [ ] Filtrar por etiqueta
- [ ] Filtrar por fecha
- [ ] Ordenar por fecha
- [ ] Ordenar por relevancia
- [ ] Ordenar por popularidad
- [ ] Ordenar por comentarios

### Validaciones
- [ ] Campos requeridos
- [ ] Permisos de acceso
- [ ] Propiedad de publicaciones
- [ ] Estado de publicaciones
- [ ] Enlaces opcionales (actividad/tratamiento)

### UI/UX
- [ ] Diseño responsive
- [ ] Mensajes de éxito/error
- [ ] Navegación en menú
- [ ] Paginación funciona
- [ ] Estados visuales (pendiente/aprobada/oculta)

---

## 🐛 Problemas Comunes y Soluciones

### Problema: Las publicaciones no aparecen
**Solución:** Verifica que estén en estado "aprobada". Los pacientes solo ven publicaciones aprobadas.

### Problema: No puedo comentar/reaccionar
**Solución:** Verifica que la publicación esté en estado "aprobada" y que estés logueado como paciente.

### Problema: Error al crear publicación
**Solución:** Verifica que el paciente tenga `id_paciente` correcto en la tabla `pacientes`.

### Problema: No aparecen actividades/tratamientos en el dropdown
**Solución:** Solo aparecen actividades completadas y tratamientos activos del paciente.

---

## 📝 Notas Adicionales

- **Estado inicial**: Todas las publicaciones nuevas empiezan como "pendiente"
- **Moderación**: Solo administradores pueden aprobar/ocultar
- **Visibilidad**: Pacientes solo ven publicaciones aprobadas (excepto las suyas)
- **Cascada**: Al eliminar una publicación, se eliminan sus comentarios, reacciones y favoritos

---

## 🎯 Pruebas de Rendimiento (Opcional)

1. Crear 50+ publicaciones y verificar paginación
2. Agregar 100+ comentarios a una publicación
3. Verificar que los filtros funcionen con muchos datos
4. Probar la búsqueda con texto largo

---

¡Listo! Con esta guía puedes probar todas las funcionalidades del módulo Foro de Experiencias. 🚀

