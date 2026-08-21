# Mi Pensum — versión web instalable

La misma app del pensum, hecha web, para instalarla en el iPhone **sin Mac y sin Xcode**.
Un solo archivo (`index.html`) sin dependencias: se agrega a la pantalla de inicio, abre
en pantalla completa con su ícono y guarda tu avance en el teléfono.

## Instalarla en el iPhone

1. Abrí la página en **Safari** (tiene que ser Safari, no Chrome).
2. Tocá el botón **Compartir** (el cuadrito con la flecha hacia arriba).
3. Elegí **Añadir a pantalla de inicio** → **Añadir**.

Queda como cualquier otra app: ícono propio, sin barra del navegador y funciona sin
internet después de la primera visita (gracias al *service worker*).

## Publicarla con GitHub Pages

Para tener un link propio y permanente, sin depender de nadie:

1. En GitHub: **Settings → Pages**.
2. *Source*: **Deploy from a branch**, rama `main`, carpeta `/ (root)` → **Save**.
3. En un par de minutos queda en
   `https://cracracrakk.github.io/PROMOL1/pensum-web/`.

Esa URL es la que abrís en Safari para instalarla.

## Archivos

```
pensum-web/
├── index.html            # la app completa: datos, estilos y lógica
├── manifest.webmanifest  # nombre, ícono y colores al instalarla
├── sw.js                 # caché offline
├── icon-192.png · icon-512.png · apple-touch-icon.png
```

## Editar el plan

Dentro de `index.html`, buscá la constante `CURSOS`. Cada clase es una línea:

```js
{c:"BDE-0606", n:"Base de Datos I", uv:4, p:4, a:"desarrollo", r:["EDE-0605"]},
```

`c` código · `n` nombre · `uv` unidades valorativas · `p` período · `a` área ·
`r` requisitos · `f` marcas (`i` investigación, `v` vinculación, `1`/`2` pasantías).

Los mismos datos, en Swift, están en `ios/PensumUTH/Models/PensumData.swift`. Si cambiás
uno, cambiá el otro para que las dos versiones coincidan.
