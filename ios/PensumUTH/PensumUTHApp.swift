import SwiftUI

@main
struct PensumUTHApp: App {
    @State private var store = PensumStore()

    var body: some Scene {
        WindowGroup {
            RootView()
                .environment(store)
                .tint(Theme.verde)
        }
    }
}
