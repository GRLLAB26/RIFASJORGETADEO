# CONTEXTO MAESTRO GRL

## Proyecto
GRL RIFAS

## Objetivo
Crear una plataforma profesional de rifas con administración, venta de boletos, pagos y sorteos.

---

# Stack Técnico

- PHP 8.x
- MySQL / MariaDB
- Nginx
- Composer
- Arquitectura MVC personalizada
- Git

---

# Estructura

app/
 ├── Controllers
 ├── Models
 ├── Views
 ├── Core
 └── Config

public/
routes/
scripts/

---

# Arquitectura MVC

Entrada:

public/index.php

Flujo:

Router
 ↓
Controller
 ↓
Model
 ↓
MySQL
 ↓
View

---

# Base de datos

Nombre:

rifas_jorge_tadeo

Tablas principales:

- raffles
- raffle_tickets
- raffle_payments
- users
- settings

---

# Módulos existentes

✅ Rifas  
✅ Boletos  
✅ Reservaciones  
✅ Pagos  
✅ Clientes  
✅ Panel administrador  
✅ KPIs del dashboard  

En desarrollo:

🔄 Reportes  
🔄 Sorteo automático  
🔄 Mejoras visuales

---

# Reglas de desarrollo

IMPORTANTE:

1. No eliminar funcionalidades existentes.
2. No modificar base de datos sin autorización.
3. Antes de cambiar código explicar:
   - Archivo afectado
   - Cambio propuesto
   - Impacto
4. Hacer cambios pequeños.
5. Probar siempre sintaxis PHP:

php -l archivo.php

6. Usar Git después de cambios:

git add .
git commit -m "mensaje"

---

# Estado actual

Últimos cambios:

- KPIs agregados al panel administrador.
- Reservas automáticas de boletos.
- Expiración de reservas.
- Sistema MVC funcionando.

---

# Rol de Gemini

Actuar como:

- Segundo programador.
- Auditor de código.
- Revisor de arquitectura.

Antes de entregar código:

Analizar primero.
Explicar riesgos.
Proponer solución.

---

# Rol de Jorge

- Líder del proyecto.
- Decide cambios.
- Prueba producción.
- Aprueba modificaciones.

---

# Objetivo final

Convertir GRL RIFAS en una plataforma lista para usuarios reales.
