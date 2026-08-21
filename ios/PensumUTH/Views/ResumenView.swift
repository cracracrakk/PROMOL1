import SwiftUI

/// Estadísticas del avance: por período y por área temática.
struct ResumenView: View {
    @Environment(PensumStore.self) private var store

    var body: some View {
        NavigationStack {
            List {
                Section {
                    ProgresoCabecera(progreso: store.progreso())
                        .listRowInsets(EdgeInsets())
                        .listRowBackground(Color.clear)
                }

                Section("De un vistazo") {
                    let p = store.progreso()
                    Fila(titulo: "Clases descartadas", valor: "\(p.clasesDescartadas)", color: Theme.verde)
                    Fila(titulo: "Cursando ahora", valor: "\(p.clasesCursando)", color: Theme.dorado)
                    Fila(titulo: "Clases pendientes", valor: "\(p.clasesPendientes)", color: .secondary)
                    Fila(titulo: "UV pendientes", valor: "\(p.uvPendientes)", color: .secondary)
                    Fila(titulo: "Períodos completos", valor: "\(periodosCompletos) de \(PensumData.periodos.count)", color: Theme.verde)
                }

                Section("Avance por período") {
                    ForEach(PensumData.periodos, id: \.self) { periodo in
                        let p = store.progreso(dePeriodo: periodo)
                        BarraProgreso(
                            titulo: Periodo.titulo(periodo),
                            detalle: "\(p.clasesDescartadas)/\(p.clasesTotales) clases",
                            fraccion: p.clasesTotales == 0 ? 0 : Double(p.clasesDescartadas) / Double(p.clasesTotales),
                            color: Theme.verde
                        )
                    }
                }

                Section("Avance por área") {
                    ForEach(Course.Area.allCases) { area in
                        let p = store.progreso(deArea: area)
                        if p.clasesTotales > 0 {
                            BarraProgreso(
                                titulo: area.rawValue,
                                detalle: "\(p.clasesDescartadas)/\(p.clasesTotales) clases",
                                fraccion: Double(p.clasesDescartadas) / Double(p.clasesTotales),
                                color: area.color,
                                icono: area.icono
                            )
                        }
                    }
                }

                Section("Simbología del plan") {
                    Label("Punto rojo — componente de investigación", systemImage: "circle.fill")
                        .foregroundStyle(Theme.rojo)
                        .font(.footnote)
                    Label("Estrella verde — componente de vinculación", systemImage: "star.fill")
                        .foregroundStyle(Theme.verde)
                        .font(.footnote)
                    Text(PensumData.notaPractica)
                        .font(.footnote)
                        .foregroundStyle(.secondary)
                }
            }
            .navigationTitle("Resumen")
        }
    }

    private var periodosCompletos: Int {
        PensumData.periodos.filter { store.periodoCompleto($0) }.count
    }
}

private struct Fila: View {
    let titulo: String
    let valor: String
    let color: Color

    var body: some View {
        HStack {
            Text(titulo)
            Spacer()
            Text(valor)
                .font(.body.weight(.semibold).monospacedDigit())
                .foregroundStyle(color)
        }
    }
}

private struct BarraProgreso: View {
    let titulo: String
    let detalle: String
    let fraccion: Double
    let color: Color
    var icono: String? = nil

    var body: some View {
        VStack(alignment: .leading, spacing: 6) {
            HStack {
                if let icono { Image(systemName: icono).foregroundStyle(color).font(.caption) }
                Text(titulo).font(.subheadline)
                Spacer()
                Text(detalle)
                    .font(.caption.monospacedDigit())
                    .foregroundStyle(.secondary)
            }
            ProgressView(value: fraccion)
                .tint(color)
        }
        .padding(.vertical, 2)
    }
}
