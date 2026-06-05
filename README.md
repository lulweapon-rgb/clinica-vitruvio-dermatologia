# Analizador Dermatológico - Clínica Vitruvio
# 🏥 Clínica Vitruvio - Sistema de Análisis Dermatológico y Seguimiento Evolutivo

Plataforma monolítica en Laravel contenerizada para la gestión, análisis mediante IA (CNN) y trazabilidad de lesiones cutáneas, incluyendo un esquema de seguridad robusto con autenticación de dos pasos (2FA).

## 📐 Diagrama de Arquitectura

```text
+-------------------+       HTTPS / Puerto 80        +-----------------------------+
|                   |  --------------------------->  | [Docker Container: Web]     |
|  Cliente (Navegador|                               |  - Laravel 11 / Apache      |
|  + Google Auth)   |  <---------------------------  |  - Vistas Blade / Auth 2FA  |
|                   |       HTML/CSS/JS + Cookies    +--------------+--------------+
+-------------------+                                               |
                                                                    | Red Interna (Docker Bridge)
                                                                    | Puerto 5432
                                                             +------v----------------------+
                                                             | [Docker Container: DB]      |
                                                             |  - PostgreSQL 15            |
                                                             |  - Volúmenes Persistentes   |
                                                             +-----------------------------+