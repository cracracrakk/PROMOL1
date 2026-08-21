import SwiftUI

/// Detalle de una asignatura: estado, requisitos y qué desbloquea.
struct CourseDetailView: View {
    @Environment(PensumStore.self) private var store
    @Environment(\.dismiss) private var dismiss
    let curso: Course

    var body: some View {
        NavigationStack {
            List {
                Section {
                    VStack(alignment: .leading, spacing: 8) {
                        Text(curso.codigo)
                            .font(.caption.weight(.bold).monospaced())
                            .foregroundStyle(.secondary)
                        Text(curso.nombre)
                            .font(.title3.weight(.bold))
                        HStack(spacing: 12) {
                            Etiqueta(texto: Periodo.titulo(curso.periodo), icono: "calendar", color: Theme.verde)
                            if curso.uv > 0 {
                                Etiqueta(texto: "\(curso.uv) UV", icono: "number", color: Theme.dorado)
                            }
                            Etiqueta(texto: curso.area.rawValue, icono: curso.area.icono, color: curso.area.color)
                        }
                    }
                    .padding(.vertical, 4)
                }

                Section("Estado") {
                    Picker("Estado", selection: Binding(
                        get: { store.estado(curso) },
                        set: { store.fijar($0, en: curso) }
                    )) {
                        ForEach(CourseStatus.allCases) { estado in
                            Text(estado.titulo).tag(estado)
                        }
                    }
                    .pickerStyle(.segmented)
                    .listRowSeparator(.hidden)
                }

                if !curso.requisitos.isEmpty {
                    Section("Requisitos") {
                        ForEach(curso.requisitos, id: \.self) { codigo in
                            if let req = PensumData.porCodigo[codigo] {
                                FilaEnlace(curso: req, listo: store.estaDescartada(req))
                            }
                        }
                    }
                }

                let siguientes = store.abrePaso(curso)
                if !siguientes.isEmpty {
                    Section("Al descartarla habilitas") {
                        ForEach(siguientes) { sig in
                            FilaEnlace(curso: sig, listo: store.estaDescartada(sig))
                        }
                    }
                }

                if !curso.flags.etiquetas.isEmpty {
                    Section("Notas del plan") {
                        ForEach(curso.flags.etiquetas, id: \.texto) { etiqueta in
                            Label(etiqueta.texto, systemImage: etiqueta.icono)
                                .font(.footnote)
                        }
                    }
                }

                Section {
                    Button {
                        store.alternar(curso)
                        dismiss()
                    } label: {
                        Label(store.estaDescartada(curso) ? "Restaurar clase" : "Descartar clase",
                              systemImage: store.estaDescartada(curso) ? "arrow.uturn.backward" : "checkmark.circle.fill")
                            .frame(maxWidth: .infinity)
                    }
                    .buttonStyle(.borderedProminent)
                    .tint(store.estaDescartada(curso) ? .orange : Theme.verde)
                    .listRowBackground(Color.clear)
                }
            }
            .navigationTitle("Clase")
            .navigationBarTitleDisplayMode(.inline)
            .toolbar {
                ToolbarItem(placement: .topBarTrailing) {
                    Button("Listo") { dismiss() }
                }
            }
        }
    }
}

private struct Etiqueta: View {
    let texto: String
    let icono: String
    let color: Color

    var body: some View {
        Label(texto, systemImage: icono)
            .font(.caption2.weight(.semibold))
            .padding(.horizontal, 8)
            .padding(.vertical, 4)
            .background(color.opacity(0.16), in: Capsule())
            .foregroundStyle(color)
    }
}

private struct FilaEnlace: View {
    let curso: Course
    let listo: Bool

    var body: some View {
        HStack {
            Image(systemName: listo ? "checkmark.circle.fill" : "circle")
                .foregroundStyle(listo ? Theme.verde : .secondary)
            VStack(alignment: .leading, spacing: 1) {
                Text(curso.nombre).font(.subheadline)
                Text("\(curso.codigo) · \(Periodo.titulo(curso.periodo))")
                    .font(.caption2)
                    .foregroundStyle(.secondary)
            }
        }
    }
}
