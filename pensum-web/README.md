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

Para tener un link propio y permanente, sin depender de nadie. El repo es público,
así que Pages funciona con el plan gratuito.

1. En GitHub, entrá a **Settings → Pages**.
2. En *Source* elegí **Deploy from a branch**.
3. *Branch*: `claude/ios-app-descartar-clases-i2xzwj` — la rama donde están estos
   archivos. Carpeta: `/ (root)`. Dale **Save**.
4. Esperá un par de minutos y la app queda en
   **https://cracracrakk.github.io/PROMOL1/pensum-web/**

Esa URL es la que abrís en Safari para instalarla.

Ojo con dos cosas:

- La rama por defecto de este repo no es `main`, es `claude/programming-capabilities-txmzN`.
  Si preferís servir desde ahí, primero mergeá esta rama.
- Pages publica **toda** la rama que elijas, no solo `pensum-web/`. El repo ya es público,
  así que no expone nada nuevo, pero tenelo en cuenta si algún día lo volvés privado.

## Archivos

```
pensum-web/
├── index.html            # la app completa: datos, estilos y lógica
├── manifest.webmanifest  # nombre, ícono y colores al instalarla
├── sw.js                 # caché offline
├── icon-192.png · icon-512.png · apple-touch-icon.png
```

## Qué hace

- **Cinco estados por clase**, y tocar una clase ya no la marca de una: abre un
  selector con Descartada (aprobada), Cursando, Repitiendo, Retirada y Pendiente.
  Quien prefiera el toque directo lo activa en *Ajustes → Modo rápido*.
- **Período académico por clase.** Tres períodos al año más el intensivo. Cada clase
  guarda en cuál la llevaste y el resumen arma el historial por período.
- **Varios pénsum.** Viene con Ingeniería en Computación y podés crear otras carreras
  desde *Ajustes → Crear un pensum nuevo*, agregando las clases a mano. Cada pensum
  lleva su propio avance.
- **Puedo llevar**: las clases pendientes o retiradas con todos sus requisitos aprobados.
- **Respaldo**: copia un código con todo y lo pegás en otro teléfono.

## Editar el plan que viene de fábrica

Se puede desde la app misma (*Ajustes → Editar el pensum activo*). Si preferís tocar el
código, dentro de `index.html` está la constante `FABRICA`. Cada clase es una línea:

```js
{c:"BDE-0606", n:"Base de Datos I", uv:4, p:4, a:"desarrollo", r:["EDE-0605"]},
```

`c` código · `n` nombre · `uv` unidades valorativas · `p` período del plan · `a` área ·
`r` requisitos · `f` marcas (`i` investigación, `v` vinculación, `1`/`2` pasantías).

Ojo: `FABRICA` solo se usa la primera vez que alguien abre la app. Después trabaja sobre
lo que ya tiene guardado en su teléfono.

## Nota sobre la app nativa de iOS

La versión Swift en `ios/` quedó en la primera entrega: tiene tres estados y un solo
pensum. Esta versión web es la que está al día.
