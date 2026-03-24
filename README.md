<div align="center">
  <img src="./assets/logo.png" alt="RutiTruck Logo" width="150"/>
  <h1>🚛 RutiTruck: Ecosistema Logístico y Navegación Inteligente</h1>
  <p><em>Ruteo especializado para transporte de carga pesada, previniendo siniestros e integrando el conocimiento empírico en tiempo real.</em></p>
</div>

---

## 🚀 El Problema y la Solución (Elevator Pitch)

El sector logístico de carga pesada (tráileres, doble remolque) se enfrenta diariamente a cuellos de botella generados por la dependencia a aplicaciones de navegación comerciales (Google Maps, Waze). Estos sistemas genéricos trazan rutas óptimas para automóviles, guiando a vehículos de grandes dimensiones por calles angostas o avenidas con infraestructura urbana fuera de norma (cables y mufas por debajo de los 5.40 metros). Esto resulta en severos daños a la infraestructura pública, atascamientos críticos, multas de hasta $15,000 MXN y un alto desgaste cognitivo para los operadores de nuevo ingreso, quienes carecen del conocimiento empírico de los conductores veteranos.

**RutiTruck** resuelve este problema a través de un ecosistema SaaS compuesto por un panel administrativo y una aplicación móvil especializada. La plataforma sustituye el ruteo genérico por algoritmos avanzados consumiendo la API cartográfica de TomTom, la cual evalúa restricciones de alto, largo y peso antes de trazar un polígono seguro. Además, introduce un sistema de _Crowdsourcing de Riesgos_, permitiendo a los operadores reportar alertas geoespaciales con precisión absoluta, transformando la memoria empírica de los veteranos en un activo digital que protege a toda la flota en tiempo real.

---

## ✨ Características Clave

*   **Ruteo por Dimensiones Físicas:** Integración B2B con la API de TomTom para calcular rutas dinámicas descartando vías prohibidas para dimensiones excedentes.
*   **Gestión de Viajes Multi-Destino:** Arquitectura de base de datos relacional para gestionar despachos logísticos compuestos por un Punto de Origen (Sede) y múltiples paradas dinámicas.
*   **Sistema de Alertas Geoespaciales:** Recolección nativa de datos GPS (Lat/Lng) vía hardware móvil para notificar emergencias (cables bajos, obras, accidentes) al panel central instantáneamente.
*   **UI Móvil de Baja Carga Cognitiva:** Interfaz móvil (Flutter) de alto contraste diseñada bajo principios de ergonomía visual para operadores de 30 a 50 años, previniendo distracciones al volante.
*   **Dashboard Administrativo Interactivo:** Panel de control web integrando mapas de Leaflet.js para designación precisa de coordinadas de sucursales empresariales y destinos personalizados.

---

## 🛠️ Stack Tecnológico

La elección de tecnologías responde a la necesidad de escalar una plataforma distribuida: **Express.js** maneja la asincronía y alta concurrencia de los cálculos cartográficos; **Flutter** garantiza compatibilidad multiplataforma nativa para terminales de operadores; y **MySQL** asegura propiedades ACID esenciales en transacciones logísticas y bitácoras de viaje.

### Móvil (Frontend Operativo)
![Flutter](https://img.shields.io/badge/Flutter-02569B?style=for-the-badge&logo=flutter&logoColor=white) 
![Dart](https://img.shields.io/badge/Dart-0175C2?style=for-the-badge&logo=dart&logoColor=white)

### Backend & API
![Node.js](https://img.shields.io/badge/Node.js-339933?style=for-the-badge&logo=nodedotjs&logoColor=white)
![Express](https://img.shields.io/badge/Express.js-000000?style=for-the-badge&logo=express&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)

### Base de Datos & Infraestructura
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)
![Koyeb](https://img.shields.io/badge/Deployed_on-Koyeb-121212?style=for-the-badge)

### APIs y Mapas
![TomTom](https://img.shields.io/badge/TomTom_API-E00000?style=for-the-badge&logo=tomtom&logoColor=white)
![Leaflet](https://img.shields.io/badge/Leaflet-199900?style=for-the-badge&logo=leaflet&logoColor=white)

---

## 🏗️ Arquitectura del Sistema

El ecosistema usa un modelo de **Arquitectura Desacoplada (Cliente-Servidor)**:
1.  **Panel Web (PHP):** Actúa como el cliente administrativo ERP/Backoffice donde dispatchers validan viajes y administran flotillas.
2.  **API RESTful (Node.js/Express):** Actúa como el *middleware* central de lógica de negocio, calculando distancias, comunicándose con servicios externos (TomTom) y resolviendo autenticaciones JWT.
3.  **App Operador (Flutter):** Consume la API de Node.js mediante peticiones asíncronas para el tracking en tiempo real y esquematización de viajes directos a la pantalla del conductor.



---

Consumido por: 
