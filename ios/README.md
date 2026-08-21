# Mi Pensum — Ingeniería en Computación (UTH)

App nativa de iOS (SwiftUI) para llevar el control de tu plan de estudio: **descarta
las clases que ya cursaste** y mira al instante cuánto te falta y qué puedes matricular.

## Qué hace

| Pantalla | Para qué sirve |
|---|---|
| **Pensum** | Las 56 asignaturas del plan agrupadas por período. Toca el círculo para descartar una clase, o desliza la fila. |
| **Puedo llevar** | Las clases pendientes que ya tienen todos sus requisitos descartados: tu lista de matrícula. |
| **Resumen** | Avance en UV y en clases, por período y por área (desarrollo, redes, exactas, humanas, electrónica, idiomas). |
| **Ajustes** | Compartir el avance por WhatsApp/correo, reiniciar el pensum y ver la ficha del plan. |

Detalles:

- **Descartar** es la acción principal: círculo de la fila, deslizar a la izquierda,
  el botón del detalle o el botón del encabezado para descartar un período completo.
- Estado intermedio **Cursando** (desliza a la derecha) para lo que llevas este período.
- **Candado** en la fila cuando aún te falta un requisito, con el código de la clase que falta.
- El detalle de cada clase muestra sus **requisitos** y **qué habilita** al descartarla.
- Marcas del brochure: punto rojo = componente de investigación, estrella verde =
  componente de vinculación, y los avisos de Pasantía Profesional Supervisada I y II.
- Todo se guarda en el dispositivo (`UserDefaults`), sin cuenta ni internet.

## ¿No tenés Mac?

Entonces esta versión no te sirve todavía: compilar un app de iOS solo se puede en macOS
con Xcode. Usá [`pensum-web/`](../pensum-web/README.md) — es la misma app en web y se
instala en la pantalla de inicio del iPhone desde Safari.

## Cómo abrirla

```bash
open ios/PensumUTH.xcodeproj
```

1. En Xcode selecciona el target **PensumUTH**.
2. En *Signing & Capabilities* elige tu equipo (con una cuenta gratuita de Apple ID basta
   para instalarla en tu iPhone) y cambia el *Bundle Identifier* si te lo pide.
3. Elige tu iPhone o un simulador y presiona ▶︎.

Requisitos: **Xcode 16+** e **iOS 17+** (usa `@Observable`, `ContentUnavailableView` y
grupos sincronizados de archivos, así que los archivos nuevos que agregues a
`PensumUTH/` entran solos al target).

## Estructura

```
ios/
├── PensumUTH.xcodeproj/
└── PensumUTH/
    ├── PensumUTHApp.swift        # punto de entrada
    ├── Models/
    │   ├── Course.swift          # asignatura, estado, marcas, períodos
    │   └── PensumData.swift      # ← el plan de estudio completo
    ├── Store/
    │   └── PensumStore.swift     # estado, progreso y persistencia
    ├── Theme/Theme.swift         # colores UTH y por área
    ├── Views/                    # pantallas
    └── Assets.xcassets/
```

## Editar el plan

Todo el pensum vive en un solo archivo: **`PensumUTH/Models/PensumData.swift`**.
Cada asignatura es una línea:

```swift
Course("BDE-0606", "Base de Datos I", uv: 4, periodo: 4, area: .desarrollo,
       requisitos: ["EDE-0605"])
```

Cambia el nombre de una electiva, agrega una clase o ajusta un requisito ahí mismo y
el resto de la app (progreso, filtros, "puedo llevar") se recalcula solo.

## Sobre los datos

Códigos, nombres, UV y períodos están tomados del mapa curricular del brochure
*Ingeniería en Computación – Plan de Estudio 2021* de la UTH (55 asignaturas + la
Práctica Profesional Supervisada, 214 UV).

Las **prelaciones (requisitos) son las cadenas visibles del diagrama** — las secuencias
I → II → III → IV y la cadena de matemáticas. El brochure no las lista en texto, así que
sirven como guía y no sustituyen lo que te indique tu coordinación académica: confírmalas
antes de matricular y ajústalas en `PensumData.swift` si tu campus maneja otra.
