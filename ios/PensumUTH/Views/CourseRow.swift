import SwiftUI

/// Fila de una asignatura: círculo para descartar, código, nombre, UV y avisos.
struct CourseRow: View {
    @Environment(PensumStore.self) private var store
    let curso: Course

    private var estado: CourseStatus { store.estado(curso) }
    private var bloqueada: Bool { store.estaBloqueada(curso) }

    var body: some View {
        HStack(alignment: .center, spacing: 12) {
            Button {
                withAnimation(.snappy) { store.alternar(curso) }
            } label: {
                ZStack {
                    Circle()
                        .strokeBorder(estado.color.opacity(0.5), lineWidth: 2)
                        .frame(width: 26, height: 26)
                    if estado == .descartada {
                        Circle().fill(Theme.verde).frame(width: 26, height: 26)
                        Image(systemName: "checkmark")
                            .font(.caption.weight(.bold))
                            .foregroundStyle(.white)
                    } else if estado == .cursando {
                        Image(systemName: "clock.fill")
                            .font(.caption)
                            .foregroundStyle(Theme.dorado)
                    }
                }
            }
            .buttonStyle(.plain)
            .accessibilityLabel(estado == .descartada
                                ? "Restaurar \(curso.nombre)"
                                : "Descartar \(curso.nombre)")

            RoundedRectangle(cornerRadius: 2)
                .fill(curso.area.color)
                .frame(width: 4)
                .frame(maxHeight: .infinity)

            VStack(alignment: .leading, spacing: 3) {
                HStack(spacing: 6) {
                    Text(curso.codigo)
                        .font(.caption2.weight(.semibold).monospaced())
                        .foregroundStyle(.secondary)
                    if curso.flags.contains(.investigacion) {
                        Circle().fill(Theme.rojo).frame(width: 7, height: 7)
                    }
                    if curso.flags.contains(.vinculacion) {
                        Image(systemName: "star.fill")
                            .font(.system(size: 8))
                            .foregroundStyle(Theme.verde)
                    }
                }

                Text(curso.nombre)
                    .font(.subheadline.weight(.medium))
                    .strikethrough(estado == .descartada, color: .secondary)
                    .foregroundStyle(estado == .descartada ? .secondary : .primary)
                    .lineLimit(2)

                if bloqueada, let falta = store.requisitosPendientes(de: curso).first {
                    Label("Falta \(falta.codigo)", systemImage: "lock.fill")
                        .font(.caption2)
                        .foregroundStyle(.orange)
                }
            }

            Spacer(minLength: 4)

            VStack(alignment: .trailing, spacing: 2) {
                Text("\(curso.uv)")
                    .font(.callout.weight(.semibold).monospacedDigit())
                Text("UV")
                    .font(.system(size: 9).weight(.semibold))
                    .foregroundStyle(.secondary)
            }
            .opacity(curso.uv == 0 ? 0 : 1)
        }
        .padding(.vertical, 3)
    }
}
