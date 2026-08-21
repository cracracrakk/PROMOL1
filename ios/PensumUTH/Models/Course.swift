import Foundation

/// Estado de una asignatura dentro del pensum del estudiante.
enum CourseStatus: String, Codable, CaseIterable, Identifiable {
    case pendiente
    case cursando
    case descartada

    var id: String { rawValue }

    var titulo: String {
        switch self {
        case .pendiente:  return "Pendiente"
        case .cursando:   return "Cursando"
        case .descartada: return "Descartada"
        }
    }

    var icono: String {
        switch self {
        case .pendiente:  return "circle"
        case .cursando:   return "clock.fill"
        case .descartada: return "checkmark.circle.fill"
        }
    }
}

/// Marcas especiales que el brochure del plan de estudio dibuja sobre algunas
/// asignaturas (punto rojo, estrella verde y asteriscos de pasantía).
struct CourseFlags: OptionSet, Codable, Hashable {
    let rawValue: Int

    static let investigacion = CourseFlags(rawValue: 1 << 0)   // punto rojo
    static let vinculacion   = CourseFlags(rawValue: 1 << 1)   // estrella verde
    static let pasantiaI     = CourseFlags(rawValue: 1 << 2)   // *  80 hrs
    static let pasantiaII    = CourseFlags(rawValue: 1 << 3)   // ** 80 hrs

    var etiquetas: [(texto: String, icono: String)] {
        var out: [(texto: String, icono: String)] = []
        if contains(.investigacion) { out.append(("Componente de investigación", "magnifyingglass.circle.fill")) }
        if contains(.vinculacion)   { out.append(("Componente de vinculación", "star.fill")) }
        if contains(.pasantiaI)     { out.append(("Al finalizarla cursa la Pasantía Profesional Supervisada I (80 hrs)", "briefcase.fill")) }
        if contains(.pasantiaII)    { out.append(("Al finalizarla cursa la Pasantía Profesional Supervisada II (80 hrs)", "briefcase.fill")) }
        return out
    }
}

/// Una asignatura del plan de estudio.
struct Course: Identifiable, Hashable, Codable {
    /// El código oficial (MAE-0501, PEE-0603, …) es el identificador estable.
    var id: String { codigo }

    let codigo: String
    let nombre: String
    let uv: Int
    let periodo: Int
    let requisitos: [String]
    let flags: CourseFlags
    /// Área temática, usada para agrupar y colorear.
    let area: Area

    init(_ codigo: String,
         _ nombre: String,
         uv: Int,
         periodo: Int,
         area: Area,
         requisitos: [String] = [],
         flags: CourseFlags = []) {
        self.codigo = codigo
        self.nombre = nombre
        self.uv = uv
        self.periodo = periodo
        self.area = area
        self.requisitos = requisitos
        self.flags = flags
    }
}

extension Course {
    enum Area: String, Codable, CaseIterable, Identifiable {
        case exactas       = "Habilidades exactas"
        case desarrollo    = "Desarrollo"
        case redes         = "Redes y telecomunicaciones"
        case humanas       = "Habilidades humanas"
        case electronica   = "Electrónica"
        case idiomas       = "Idiomas"
        case practica      = "Práctica profesional"

        var id: String { rawValue }

        var icono: String {
            switch self {
            case .exactas:     return "function"
            case .desarrollo:  return "chevron.left.forwardslash.chevron.right"
            case .redes:       return "network"
            case .humanas:     return "person.2.fill"
            case .electronica: return "cpu"
            case .idiomas:     return "character.book.closed.fill"
            case .practica:    return "briefcase.fill"
            }
        }
    }
}

/// Los períodos del plan (I … XII).
enum Periodo {
    static let romanos = ["", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"]

    static func romano(_ n: Int) -> String {
        guard n > 0, n < romanos.count else { return "\(n)" }
        return romanos[n]
    }

    static func titulo(_ n: Int) -> String { "\(romano(n)) Período" }
}
