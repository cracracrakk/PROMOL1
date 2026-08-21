import SwiftUI
import UIKit

struct AjustesView: View {
    @Environment(PensumStore.self) private var store
    @State private var confirmarReinicio = false
    @State private var compartir = false

    var body: some View {
        NavigationStack {
            List {
                Section("Mi avance") {
                    Button {
                        compartir = true
                    } label: {
                        Label("Compartir resumen", systemImage: "square.and.arrow.up")
                    }

                    Button(role: .destructive) {
                        confirmarReinicio = true
                    } label: {
                        Label("Reiniciar todo el pensum", systemImage: "arrow.counterclockwise")
                    }
                }

                Section("Cómo se usa") {
                    Ayuda(icono: "checkmark.circle", texto: "Toca el círculo de una clase para descartarla.")
                    Ayuda(icono: "hand.draw", texto: "Desliza a la izquierda para descartar o restaurar; a la derecha para marcarla como «cursando».")
                    Ayuda(icono: "checkmark.circle.fill", texto: "En el encabezado de cada período, el botón derecho descarta el período completo.")
                    Ayuda(icono: "lock.fill", texto: "El candado avisa cuando aún te falta un requisito.")
                }

                Section("El plan") {
                    LabeledContent("Carrera", value: PensumData.carrera)
                    LabeledContent("Universidad", value: PensumData.universidad)
                    LabeledContent("Plan", value: PensumData.plan)
                    LabeledContent("Clases", value: "\(PensumData.cursos.count)")
                    LabeledContent("UV totales", value: "\(PensumData.uvTotales)")
                }

                Section {
                    Text("Los códigos, nombres, UV y períodos provienen del mapa curricular del brochure. Las prelaciones son las cadenas visibles del diagrama; confirma siempre con tu coordinación académica antes de matricular.")
                        .font(.footnote)
                        .foregroundStyle(.secondary)
                }
            }
            .navigationTitle("Ajustes")
            .alert("¿Reiniciar el pensum?", isPresented: $confirmarReinicio) {
                Button("Cancelar", role: .cancel) {}
                Button("Reiniciar", role: .destructive) { store.reiniciar() }
            } message: {
                Text("Todas las clases volverán a estado pendiente. No se puede deshacer.")
            }
            .sheet(isPresented: $compartir) {
                ShareSheet(texto: store.resumenParaCompartir())
            }
        }
    }
}

private struct Ayuda: View {
    let icono: String
    let texto: String

    var body: some View {
        Label {
            Text(texto).font(.footnote)
        } icon: {
            Image(systemName: icono).foregroundStyle(Theme.verde)
        }
    }
}

/// Puente mínimo al share sheet del sistema.
private struct ShareSheet: UIViewControllerRepresentable {
    let texto: String

    func makeUIViewController(context: Context) -> UIActivityViewController {
        UIActivityViewController(activityItems: [texto], applicationActivities: nil)
    }

    func updateUIViewController(_ controller: UIActivityViewController, context: Context) {}
}
