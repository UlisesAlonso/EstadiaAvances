const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const mysql = require('mysql2/promise');

// Pool de conexión a MySQL
const db = mysql.createPool({
  host: '127.0.0.1',
  user: 'root',
  password: '',
  database: 'clinica_salud_total'
});

const app = express();
const server = http.createServer(app);
const io = socketIo(server, {
  cors: {
    origin: '*',
  },
});

io.on('connection', (socket) => {
  console.log('Nuevo cliente conectado', socket.id);

  socket.on('join_chat', ({ id_chat, id_usuario }) => {
    socket.join(`chat_${id_chat}`);
    console.log(`Usuario ${id_usuario} se unió al chat ${id_chat}`);
    
    // Notificar a otros usuarios del chat (opcional)
    socket.to(`chat_${id_chat}`).emit('user_joined', { id_usuario });
  });

  socket.on('send_message', async ({ id_chat, id_usuario, mensaje }) => {
    try {
      console.log('Recibido send_message:', { id_chat, id_usuario, mensaje });
      
      // Validar datos
      if (!id_chat || !id_usuario || !mensaje) {
        console.error('Datos incompletos:', { id_chat, id_usuario, mensaje });
        socket.emit('error_message', { message: 'Datos incompletos para enviar el mensaje' });
        return;
      }

      const fecha_envio = new Date();
      
      // Insertar mensaje en la base de datos
      console.log('Insertando mensaje en BD...');
      const [result] = await db.execute(
        'INSERT INTO mensajes (id_chat, id_usuario, mensaje, fecha_envio, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)',
        [id_chat, id_usuario, mensaje, fecha_envio, fecha_envio, fecha_envio]
      );
      const id_mensaje = result.insertId;
      console.log('Mensaje insertado con ID:', id_mensaje);

      // Obtener mensaje con información del usuario
      const [rows] = await db.execute(
        `SELECT m.*, u.nombre, u.apPaterno, u.apMaterno, u.rol 
         FROM mensajes m 
         JOIN usuarios u ON m.id_usuario = u.id_usuario 
         WHERE m.id_mensaje = ?`,
        [id_mensaje]
      );
      
      if (rows.length === 0) {
        console.error('No se pudo recuperar el mensaje guardado');
        socket.emit('error_message', { message: 'No se pudo recuperar el mensaje guardado' });
        return;
      }
      
      const mensajeGuardado = rows[0];
      console.log('Mensaje guardado:', mensajeGuardado);

      // Emitir mensaje a todos los usuarios en el chat
      io.to(`chat_${id_chat}`).emit('new_message', mensajeGuardado);
      
      console.log(`Mensaje enviado exitosamente en chat ${id_chat} por usuario ${id_usuario}`);
    } catch (error) {
      console.error('Error al guardar mensaje:', error);
      console.error('Stack trace:', error.stack);
      socket.emit('error_message', { message: 'No se pudo enviar el mensaje: ' + error.message });
    }
  });

  socket.on('disconnect', () => {
    console.log('Cliente desconectado', socket.id);
  });

  socket.on('new_message', (data) => {
    console.log('Nuevo mensaje recibido:', data);

    // 🟦 1. Mostrar mensaje si estás dentro del chat
    if (data.id_chat == currentChatId) {
        agregarMensajeAlDOM(data);
    }

    // 🟧 2. Mostrar alerta siempre que lleguen mensajes que NO sean de ti
    if (data.id_usuario != idUsuario) {
        mostrarAlerta("Nuevo mensaje de " + data.nombre);
    }
});


});

const PORT = 3001;
server.listen(PORT, () => {
  console.log(`Servidor de chat escuchando en puerto ${PORT}`);
});



