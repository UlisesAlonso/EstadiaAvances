# Servidor de Chat - Socket.io

Servidor Node.js para el sistema de mensajería en tiempo real.

## Instalación

```bash
npm install
```

## Configuración

Asegúrate de que la base de datos esté configurada correctamente en `server.js`:
- host: '127.0.0.1'
- user: 'root'
- password: ''
- database: 'clinica_salud_total'

## Ejecución

### Desarrollo (con auto-reload)
```bash
npm run dev
```

### Producción
```bash
npm start
```

El servidor se ejecutará en el puerto 3001.

## Dependencias

- express: Framework web
- socket.io: Comunicación en tiempo real
- mysql2: Cliente MySQL para Node.js
- cors: Manejo de CORS

