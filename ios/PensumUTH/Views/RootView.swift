import SwiftUI

struct RootView: View {
    var body: some View {
        TabView {
            PensumView()
                .tabItem { Label("Pensum", systemImage: "list.bullet.rectangle.portrait") }

            DisponiblesView()
                .tabItem { Label("Puedo llevar", systemImage: "checkmark.seal") }

            ResumenView()
                .tabItem { Label("Resumen", systemImage: "chart.pie") }

            AjustesView()
                .tabItem { Label("Ajustes", systemImage: "gearshape") }
        }
    }
}

#Preview {
    RootView().environment(PensumStore())
}
