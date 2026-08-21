import SwiftUI

/// Clases que ya se pueden matricular: pendientes con todos sus requisitos
/// descartados. Es la respuesta a "¿qué me toca el próximo período?".
struct DisponiblesView: View {
    @Environment(PensumStore.self) private var store
    @State private var cursoSeleccionado: Course?

    var body: some View {
        NavigationStack {
            Group {
                if store.disponibles.isEmpty {
                    ContentUnavailableView(
                        "Nada disponible",
                        systemImage: "checkmark.seal",
                        description: Text("Descarta las clases que ya cursaste para ver qué puedes llevar ahora.")
                    )
                } else {
                    List {
                        Section {
                            Text("Estas \(store.disponibles.count) clases tienen todos sus requisitos descartados.")
                                .font(.footnote)
                                .foregroundStyle(.secondary)
                        }
                        ForEach(agrupadas, id: \.periodo) { grupo in
                            Section(Periodo.titulo(grupo.periodo)) {
                                ForEach(grupo.cursos) { curso in
                                    CourseRow(curso: curso)
                                        .contentShape(Rectangle())
                                        .onTapGesture { cursoSeleccionado = curso }
                                }
                            }
                        }
                    }
                }
            }
            .navigationTitle("Puedo llevar")
            .sheet(item: $cursoSeleccionado) { curso in
                CourseDetailView(curso: curso)
                    .presentationDetents([.medium, .large])
            }
        }
    }

    private var agrupadas: [(periodo: Int, cursos: [Course])] {
        Dictionary(grouping: store.disponibles, by: \.periodo)
            .map { (periodo: $0.key, cursos: $0.value) }
            .sorted { $0.periodo < $1.periodo }
    }
}
