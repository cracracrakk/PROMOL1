import SwiftUI

/// Paleta inspirada en la identidad de la UTH: verde institucional y dorado.
enum Theme {
    static let verde   = Color(red: 0.13, green: 0.55, blue: 0.31)
    static let verdeOsc = Color(red: 0.08, green: 0.36, blue: 0.21)
    static let dorado  = Color(red: 0.90, green: 0.72, blue: 0.20)
    static let rojo    = Color(red: 0.85, green: 0.24, blue: 0.24)

    static let degradado = LinearGradient(
        colors: [verde, verdeOsc],
        startPoint: .topLeading,
        endPoint: .bottomTrailing
    )
}

extension Course.Area {
    /// Color por área temática, para leer la lista de un vistazo.
    var color: Color {
        switch self {
        case .exactas:     return Color(red: 0.93, green: 0.58, blue: 0.20)
        case .desarrollo:  return Color(red: 0.95, green: 0.80, blue: 0.22)
        case .redes:       return Color(red: 0.36, green: 0.66, blue: 0.93)
        case .humanas:     return Color(red: 0.71, green: 0.55, blue: 0.90)
        case .electronica: return Color(red: 0.42, green: 0.76, blue: 0.53)
        case .idiomas:     return Color(red: 0.40, green: 0.78, blue: 0.76)
        case .practica:    return Theme.verde
        }
    }
}

extension CourseStatus {
    var color: Color {
        switch self {
        case .pendiente:  return .secondary
        case .cursando:   return Theme.dorado
        case .descartada: return Theme.verde
        }
    }
}
