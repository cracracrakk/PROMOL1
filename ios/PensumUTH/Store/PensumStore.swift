import Foundation
import Observation
#if canImport(UIKit)
import UIKit
#endif

/// Estado del pensum del estudiante. Guarda en `UserDefaults` cada cambio,
/// así la app recuerda lo descartado entre sesiones.
@Observable
final class PensumStore {

    // MARK: - Estado persistido

    private(set) var estados: [String: CourseStatus] = [:]

    /// Filtro activo en la lista.
    var filtro: Filtro = .todas
    /// Texto de búsqueda.
    var busqueda: String = ""

    enum Filtro: String, CaseIterable, Identifiable {
        case todas = "Todas"
        case pendientes = "Pendientes"
        case cursando = "Cursando"
        case descartadas = "Descartadas"

        var id: String { rawValue }

        var icono: String {
            switch self {
            case .todas:       return "square.grid.2x2"
            case .pendientes:  return "circle"
            case .cursando:    return "clock"
            case .descartadas: return "checkmark.circle"
            }
        }
    }

    // MARK: - Ciclo de vida

    private let defaultsKey = "pensum.estados.v1"

    init() { cargar() }

    // MARK: - Consultas

    func estado(_ curso: Course) -> CourseStatus {
        estados[curso.codigo] ?? .pendiente
    }

    func estaDescartada(_ curso: Course) -> Bool {
        estado(curso) == .descartada
    }

    /// Requisitos que todavía no han sido descartados.
    func requisitosPendientes(de curso: Course) -> [Course] {
        curso.requisitos.compactMap { PensumData.porCodigo[$0] }
            .filter { estado($0) != .descartada }
    }

    /// Una clase está "bloqueada" si le falta descartar algún requisito.
    func estaBloqueada(_ curso: Course) -> Bool {
        estado(curso) != .descartada && !requisitosPendientes(de: curso).isEmpty
    }

    /// Clases que dependen directamente de ésta.
    func abrePaso(_ curso: Course) -> [Course] {
        PensumData.cursos.filter { $0.requisitos.contains(curso.codigo) }
    }

    // MARK: - Mutaciones

    func descartar(_ curso: Course) { fijar(.descartada, en: curso) }

    func restaurar(_ curso: Course) { fijar(.pendiente, en: curso) }

    /// Alterna entre descartada y pendiente — la acción principal de la app.
    func alternar(_ curso: Course) {
        fijar(estaDescartada(curso) ? .pendiente : .descartada, en: curso)
    }

    func fijar(_ nuevo: CourseStatus, en curso: Course) {
        guard estados[curso.codigo] != nuevo else { return }
        if nuevo == .pendiente {
            estados.removeValue(forKey: curso.codigo)
        } else {
            estados[curso.codigo] = nuevo
        }
        vibrar(nuevo == .descartada ? .success : .warning)
        guardar()
    }

    /// Descarta (o restaura) un período completo de una sola vez.
    func descartarPeriodo(_ periodo: Int, descartar: Bool = true) {
        for curso in PensumData.cursosDe(periodo: periodo) {
            if descartar {
                estados[curso.codigo] = .descartada
            } else {
                estados.removeValue(forKey: curso.codigo)
            }
        }
        vibrar(.success)
        guardar()
    }

    func periodoCompleto(_ periodo: Int) -> Bool {
        let cursos = PensumData.cursosDe(periodo: periodo)
        return !cursos.isEmpty && cursos.allSatisfy { estado($0) == .descartada }
    }

    func reiniciar() {
        estados.removeAll()
        vibrar(.warning)
        guardar()
    }

    // MARK: - Progreso

    struct Progreso {
        var uvDescartadas: Int
        var uvTotales: Int
        var clasesDescartadas: Int
        var clasesTotales: Int
        var clasesCursando: Int

        var fraccion: Double {
            uvTotales == 0 ? 0 : Double(uvDescartadas) / Double(uvTotales)
        }
        var porcentaje: Int { Int((fraccion * 100).rounded()) }
        var clasesPendientes: Int { clasesTotales - clasesDescartadas }
        var uvPendientes: Int { uvTotales - uvDescartadas }
    }

    func progreso(en cursos: [Course] = PensumData.cursos) -> Progreso {
        var p = Progreso(uvDescartadas: 0, uvTotales: 0, clasesDescartadas: 0,
                         clasesTotales: cursos.count, clasesCursando: 0)
        for curso in cursos {
            p.uvTotales += curso.uv
            switch estado(curso) {
            case .descartada:
                p.uvDescartadas += curso.uv
                p.clasesDescartadas += 1
            case .cursando:
                p.clasesCursando += 1
            case .pendiente:
                break
            }
        }
        return p
    }

    func progreso(dePeriodo periodo: Int) -> Progreso {
        progreso(en: PensumData.cursosDe(periodo: periodo))
    }

    func progreso(deArea area: Course.Area) -> Progreso {
        progreso(en: PensumData.cursos.filter { $0.area == area })
    }

    /// Clases que ya se pueden llevar: pendientes y con todos sus requisitos listos.
    var disponibles: [Course] {
        PensumData.cursos.filter { estado($0) == .pendiente && requisitosPendientes(de: $0).isEmpty }
    }

    // MARK: - Lista filtrada

    /// Cursos de un período aplicando filtro y búsqueda.
    func cursosVisibles(dePeriodo periodo: Int) -> [Course] {
        PensumData.cursosDe(periodo: periodo).filter { coincide($0) }
    }

    private func coincide(_ curso: Course) -> Bool {
        switch filtro {
        case .todas:       break
        case .pendientes:  if estado(curso) != .pendiente { return false }
        case .cursando:    if estado(curso) != .cursando { return false }
        case .descartadas: if estado(curso) != .descartada { return false }
        }
        let texto = busqueda.trimmingCharacters(in: .whitespacesAndNewlines)
        guard !texto.isEmpty else { return true }
        return curso.nombre.localizedCaseInsensitiveContains(texto)
            || curso.codigo.localizedCaseInsensitiveContains(texto)
    }

    // MARK: - Persistencia

    private func guardar() {
        guard let data = try? JSONEncoder().encode(estados) else { return }
        UserDefaults.standard.set(data, forKey: defaultsKey)
    }

    private func cargar() {
        guard let data = UserDefaults.standard.data(forKey: defaultsKey),
              let guardado = try? JSONDecoder().decode([String: CourseStatus].self, from: data)
        else { return }
        estados = guardado
    }

    /// Exporta el avance como texto para compartir por WhatsApp, correo, etc.
    func resumenParaCompartir() -> String {
        let p = progreso()
        var lineas = [
            "\(PensumData.carrera) — \(PensumData.universidad)",
            "Avance: \(p.porcentaje)% · \(p.uvDescartadas)/\(p.uvTotales) UV · \(p.clasesDescartadas)/\(p.clasesTotales) clases",
            ""
        ]
        for periodo in PensumData.periodos {
            let cursos = PensumData.cursosDe(periodo: periodo)
            let hechas = cursos.filter { estado($0) == .descartada }
            guard !hechas.isEmpty else { continue }
            lineas.append("\(Periodo.titulo(periodo)):")
            lineas.append(contentsOf: hechas.map { "  ✓ \($0.codigo) \($0.nombre)" })
        }
        return lineas.joined(separator: "\n")
    }

    // MARK: - Háptica

    #if canImport(UIKit)
    private func vibrar(_ tipo: UINotificationFeedbackGenerator.FeedbackType) {
        UINotificationFeedbackGenerator().notificationOccurred(tipo)
    }
    #else
    private enum Feedback { case success, warning }
    private func vibrar(_ tipo: Feedback) {}
    #endif
}
