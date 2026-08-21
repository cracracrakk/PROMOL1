import SwiftUI

/// Pantalla principal: todo el plan de estudio por período, con la acción de
/// descartar cada clase.
struct PensumView: View {
    @Environment(PensumStore.self) private var store
    @State private var cursoSeleccionado: Course?
    @State private var periodosColapsados: Set<Int> = []

    var body: some View {
        @Bindable var enlace = store

        NavigationStack {
            List {
                Section {
                    ProgresoCabecera(progreso: store.progreso())
                        .listRowInsets(EdgeInsets())
                        .listRowBackground(Color.clear)
                }

                ForEach(PensumData.periodos, id: \.self) { periodo in
                    let cursos = store.cursosVisibles(dePeriodo: periodo)
                    if !cursos.isEmpty {
                        Section {
                            if !periodosColapsados.contains(periodo) {
                                ForEach(cursos) { curso in
                                    CourseRow(curso: curso)
                                        .contentShape(Rectangle())
                                        .onTapGesture { cursoSeleccionado = curso }
                                        .swipeActions(edge: .trailing, allowsFullSwipe: true) {
                                            botonDescartar(curso)
                                        }
                                        .swipeActions(edge: .leading) {
                                            botonCursando(curso)
                                        }
                                        .contextMenu { menu(curso) }
                                }
                            }
                        } header: {
                            PeriodoHeader(
                                periodo: periodo,
                                progreso: store.progreso(dePeriodo: periodo),
                                colapsado: periodosColapsados.contains(periodo),
                                onToggleColapso: { alternarColapso(periodo) },
                                onDescartarTodo: { store.descartarPeriodo(periodo, descartar: !store.periodoCompleto(periodo)) }
                            )
                        }
                    }
                }

                Section {
                    Text(PensumData.notaPractica)
                        .font(.footnote)
                        .foregroundStyle(.secondary)
                }
            }
            .listStyle(.insetGrouped)
            .navigationTitle("Mi Pensum")
            .navigationBarTitleDisplayMode(.large)
            .searchable(text: $enlace.busqueda, prompt: "Buscar clase o código")
            .toolbar {
                ToolbarItem(placement: .topBarTrailing) {
                    Menu {
                        Picker("Filtro", selection: $enlace.filtro) {
                            ForEach(PensumStore.Filtro.allCases) { f in
                                Label(f.rawValue, systemImage: f.icono).tag(f)
                            }
                        }
                    } label: {
                        Label("Filtrar", systemImage: store.filtro == .todas
                              ? "line.3.horizontal.decrease.circle"
                              : "line.3.horizontal.decrease.circle.fill")
                    }
                }
            }
            .sheet(item: $cursoSeleccionado) { curso in
                CourseDetailView(curso: curso)
                    .presentationDetents([.medium, .large])
            }
            .overlay {
                if sinResultados {
                    ContentUnavailableView(
                        "Sin clases",
                        systemImage: "magnifyingglass",
                        description: Text("Ninguna clase coincide con el filtro «\(store.filtro.rawValue)».")
                    )
                }
            }
        }
    }

    private var sinResultados: Bool {
        PensumData.periodos.allSatisfy { store.cursosVisibles(dePeriodo: $0).isEmpty }
    }

    private func alternarColapso(_ periodo: Int) {
        withAnimation(.snappy) {
            if periodosColapsados.contains(periodo) {
                periodosColapsados.remove(periodo)
            } else {
                periodosColapsados.insert(periodo)
            }
        }
    }

    @ViewBuilder
    private func botonDescartar(_ curso: Course) -> some View {
        let hecha = store.estaDescartada(curso)
        Button {
            withAnimation(.snappy) { store.alternar(curso) }
        } label: {
            Label(hecha ? "Restaurar" : "Descartar",
                  systemImage: hecha ? "arrow.uturn.backward" : "checkmark")
        }
        .tint(hecha ? .orange : Theme.verde)
    }

    @ViewBuilder
    private func botonCursando(_ curso: Course) -> some View {
        Button {
            withAnimation(.snappy) {
                store.fijar(store.estado(curso) == .cursando ? .pendiente : .cursando, en: curso)
            }
        } label: {
            Label("Cursando", systemImage: "clock")
        }
        .tint(Theme.dorado)
    }

    @ViewBuilder
    private func menu(_ curso: Course) -> some View {
        ForEach(CourseStatus.allCases) { estado in
            Button {
                withAnimation(.snappy) { store.fijar(estado, en: curso) }
            } label: {
                Label(estado.titulo, systemImage: estado.icono)
            }
        }
        Divider()
        Button { cursoSeleccionado = curso } label: {
            Label("Ver detalle", systemImage: "info.circle")
        }
    }
}

/// Encabezado de cada período con su avance y el botón de "descartar todo".
private struct PeriodoHeader: View {
    let periodo: Int
    let progreso: PensumStore.Progreso
    let colapsado: Bool
    let onToggleColapso: () -> Void
    let onDescartarTodo: () -> Void

    private var completo: Bool { progreso.clasesDescartadas == progreso.clasesTotales }

    var body: some View {
        HStack(spacing: 10) {
            Button(action: onToggleColapso) {
                HStack(spacing: 6) {
                    Image(systemName: colapsado ? "chevron.right" : "chevron.down")
                        .font(.caption2.weight(.bold))
                    Text(Periodo.titulo(periodo))
                        .font(.subheadline.weight(.bold))
                }
            }
            .buttonStyle(.plain)
            .foregroundStyle(completo ? Theme.verde : .primary)

            Spacer()

            Text("\(progreso.clasesDescartadas)/\(progreso.clasesTotales)")
                .font(.caption.monospacedDigit())
                .foregroundStyle(.secondary)

            Button(action: onDescartarTodo) {
                Image(systemName: completo ? "checkmark.circle.fill" : "checkmark.circle")
                    .foregroundStyle(completo ? Theme.verde : .secondary)
            }
            .buttonStyle(.plain)
            .accessibilityLabel(completo ? "Restaurar período" : "Descartar todo el período")
        }
        .textCase(nil)
    }
}

#Preview {
    PensumView().environment(PensumStore())
}
