# LuminaAdvisor

> Asesor inteligente de iluminación para el hogar — España 🇪🇸

## ¿Qué es?

LuminaAdvisor calcula qué tipo y cantidad de luminarias necesita cada habitación de tu hogar.  
Basado en la norma **UNE-EN 12464-1** (estándar europeo de iluminación).

Ingresás el tipo de habitación, el color de las paredes, los metros cuadrados, la altura del techo y las ventanas → obtenés una recomendación precisa y el coste estimado en €/mes.

## Stack

- **Backend:** PHP 8.4+ — Clean Architecture (sin frameworks)
- **Frontend:** HTML5 + CSS3 + Vanilla JS
- **DB:** SQLite (local) / MySQL (Hostinger)
- **Servidor:** Hostinger

## Arrancar en local

```bash
# 1. Clonar el repo
git clone https://github.com/20Luisma/LuminaAdvisor.git
cd LuminaAdvisor

# 2. Configurar entorno
cp .env.example .env
# Editar .env con tus valores

# 3. Levantar con PHP built-in server
php -S localhost:8080 -t public/
```

## Deploy a Hostinger

```bash
bash bin/deploy.sh
```

## Estructura

```
LuminaAdvisor/
├── src/            ← Backend PHP (Domain, Application, Infrastructure)
├── public/         ← Front Controller + Assets (CSS, JS)
├── templates/      ← Vistas HTML
├── docs/adr/       ← Decisiones arquitectónicas
├── bin/            ← Scripts de deploy y utilidades
└── storage/        ← Almacenamiento local (en .gitignore)
```

## Estándares de iluminación (UNE-EN 12464-1)

| Habitación | Lux mínimos | Temperatura recomendada |
|---|---|---|
| Dormitorio | 100–200 lux | 2700K–3000K (cálida) |
| Salón | 200–300 lux | 2700K–3000K (cálida) |
| Cocina | 300–500 lux | 3500K–4000K (neutra) |
| Baño | 200–500 lux | 3500K–4000K (neutra) |
| Despacho | 500–750 lux | 4000K–5000K (fría) |
| Pasillo | 100–150 lux | 3000K (neutra-cálida) |
