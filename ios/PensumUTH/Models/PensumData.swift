import Foundation

/// Plan de estudio de Ingeniería en Computación (UTH Honduras, brochure 2021).
///
/// Los códigos, nombres, UV y período están tomados del mapa curricular oficial.
/// Los `requisitos` reproducen las cadenas de prelación evidentes del diagrama
/// (secuencias I → II → III → IV y la cadena de matemáticas); si tu campus
/// maneja otra prelación, edita únicamente este archivo.
enum PensumData {

    static let carrera = "Ingeniería en Computación"
    static let universidad = "Universidad Tecnológica de Honduras"
    static let plan = "Plan de estudio 2021"

    static let cursos: [Course] = [

        // ───────────────────────── I Período ─────────────────────────
        Course("MAE-0501", "Matemática I", uv: 4, periodo: 1, area: .exactas),
        Course("ADE-0901", "Administración I", uv: 4, periodo: 1, area: .humanas),
        Course("IIC-0600", "Introducción a la Ingeniería Computacional", uv: 4, periodo: 1, area: .desarrollo),
        Course("ESG-9201", "Español I", uv: 3, periodo: 1, area: .humanas),
        Course("ADE-0601", "Análisis y Diseño de Algoritmos", uv: 4, periodo: 1, area: .desarrollo),

        // ──────────────────────── II Período ─────────────────────────
        Course("GTE-0513", "Geometría y Trigonometría", uv: 4, periodo: 2, area: .exactas,
               requisitos: ["MAE-0501"]),
        Course("CCE-0801", "Contabilidad General", uv: 4, periodo: 2, area: .humanas,
               requisitos: ["ADE-0901"]),
        Course("HHG-0401", "Historia de Honduras", uv: 3, periodo: 2, area: .humanas),
        Course("SCG-0303", "Sociología", uv: 4, periodo: 2, area: .humanas),
        Course("PEE-0603", "Programación Estructurada", uv: 4, periodo: 2, area: .desarrollo,
               requisitos: ["ADE-0601"]),

        // ─────────────────────── III Período ─────────────────────────
        Course("MAE-0514", "Pre Cálculo", uv: 4, periodo: 3, area: .exactas,
               requisitos: ["GTE-0513"]),
        Course("INE-0204", "Inglés I", uv: 3, periodo: 3, area: .idiomas),
        Course("EDE-0605", "Estructura de Datos", uv: 4, periodo: 3, area: .desarrollo,
               requisitos: ["PEE-0603"]),
        Course("FIG-0301", "Filosofía", uv: 4, periodo: 3, area: .humanas),
        Course("POE-0604", "Programación Orientada a Objetos", uv: 4, periodo: 3, area: .desarrollo,
               requisitos: ["PEE-0603"]),

        // ──────────────────────── IV Período ─────────────────────────
        Course("CAE-0504", "Cálculo I", uv: 4, periodo: 4, area: .exactas,
               requisitos: ["MAE-0514"]),
        Course("INE-0205", "Inglés II", uv: 3, periodo: 4, area: .idiomas,
               requisitos: ["INE-0204"]),
        Course("SOE-0608", "Sistemas Operativos I", uv: 4, periodo: 4, area: .redes,
               requisitos: ["EDE-0605"]),
        Course("BDE-0606", "Base de Datos I", uv: 4, periodo: 4, area: .desarrollo,
               requisitos: ["EDE-0605"]),
        Course("PAE-0606", "Programación Avanzada I", uv: 4, periodo: 4, area: .desarrollo,
               requisitos: ["POE-0604"]),

        // ───────────────────────── V Período ─────────────────────────
        Course("ALE-0511", "Álgebra Lineal", uv: 4, periodo: 5, area: .exactas,
               requisitos: ["CAE-0504"]),
        Course("CAE-0505", "Cálculo II", uv: 4, periodo: 5, area: .exactas,
               requisitos: ["CAE-0504"]),
        Course("SOE-0610", "Sistemas Operativos II", uv: 4, periodo: 5, area: .redes,
               requisitos: ["SOE-0608"]),
        Course("BDE-0609", "Base de Datos II", uv: 4, periodo: 5, area: .desarrollo,
               requisitos: ["BDE-0606"]),
        Course("PAE-0607", "Programación Avanzada II", uv: 4, periodo: 5, area: .desarrollo,
               requisitos: ["PAE-0606"], flags: [.vinculacion]),

        // ──────────────────────── VI Período ─────────────────────────
        Course("FIE-1901", "Física I", uv: 4, periodo: 6, area: .exactas,
               requisitos: ["ALE-0511"]),
        Course("EDE-0512", "Ecuaciones Diferenciales", uv: 4, periodo: 6, area: .exactas,
               requisitos: ["CAE-0505"]),
        Course("RCE-0613", "Redes I", uv: 4, periodo: 6, area: .redes,
               requisitos: ["SOE-0610"]),
        Course("PWE-0604", "Programación Web I", uv: 4, periodo: 6, area: .desarrollo,
               requisitos: ["BDE-0609"]),
        Course("ISE-0614", "Ingeniería de Software I", uv: 4, periodo: 6, area: .desarrollo,
               requisitos: ["PAE-0607"]),

        // ─────────────────────── VII Período ─────────────────────────
        Course("FIE-1902", "Física II", uv: 4, periodo: 7, area: .exactas,
               requisitos: ["FIE-1901"]),
        Course("RMH-0603", "Reparación y Mantenimiento de Hardware y Software", uv: 4, periodo: 7, area: .electronica,
               requisitos: ["EDE-0512"]),
        Course("RCE-0614", "Redes II", uv: 4, periodo: 7, area: .redes,
               requisitos: ["RCE-0613"]),
        Course("PWE-0606", "Programación Web II", uv: 4, periodo: 7, area: .desarrollo,
               requisitos: ["PWE-0604"], flags: [.vinculacion]),
        Course("ISE-0617", "Ingeniería de Software II", uv: 4, periodo: 7, area: .desarrollo,
               requisitos: ["ISE-0614"], flags: [.investigacion, .pasantiaI]),

        // ─────────────────────── VIII Período ────────────────────────
        Course("ETE-0507", "Estadística I", uv: 4, periodo: 8, area: .exactas,
               requisitos: ["FIE-1902"]),
        Course("ACE-0623", "Arquitectura de Computadoras", uv: 4, periodo: 8, area: .electronica,
               requisitos: ["RMH-0603"]),
        Course("RCE-0615", "Redes III", uv: 4, periodo: 8, area: .redes,
               requisitos: ["RCE-0614"]),
        Course("PTI-0620", "Computación en la Nube", uv: 4, periodo: 8, area: .redes,
               requisitos: ["PWE-0606"]),
        Course("PMO-0602", "Programación Móvil I", uv: 4, periodo: 8, area: .desarrollo,
               requisitos: ["ISE-0617"]),

        // ──────────────────────── IX Período ─────────────────────────
        Course("PEM-0610", "Programación Embebida", uv: 4, periodo: 9, area: .electronica,
               requisitos: ["ETE-0507"]),
        Course("E-I", "Electiva I", uv: 3, periodo: 9, area: .humanas),
        Course("RCE-0618", "Redes IV", uv: 4, periodo: 9, area: .redes,
               requisitos: ["RCE-0615"], flags: [.investigacion, .pasantiaII]),
        Course("CGV-0601", "Computación Gráfica y Visual", uv: 4, periodo: 9, area: .desarrollo,
               requisitos: ["PTI-0620"]),
        Course("PMO-0604", "Programación Móvil II", uv: 4, periodo: 9, area: .desarrollo,
               requisitos: ["PMO-0602"], flags: [.vinculacion]),

        // ───────────────────────── X Período ─────────────────────────
        Course("IAE-0611", "Inteligencia Artificial", uv: 4, periodo: 10, area: .desarrollo,
               requisitos: ["PEM-0610"], flags: [.investigacion]),
        Course("E-II", "Electiva II", uv: 3, periodo: 10, area: .humanas,
               requisitos: ["E-I"]),
        Course("IOT-0607", "Internet de las Cosas (IOT)", uv: 4, periodo: 10, area: .electronica,
               requisitos: ["RCE-0618"], flags: [.investigacion]),
        Course("ARE-0807", "Automatización y Robótica Industrial", uv: 4, periodo: 10, area: .electronica,
               requisitos: ["CGV-0601"]),
        Course("PSA-0603", "Programación para Sistemas Abiertos I", uv: 4, periodo: 10, area: .desarrollo,
               requisitos: ["PMO-0604"], flags: [.vinculacion]),

        // ──────────────────────── XI Período ─────────────────────────
        Course("OCE-0620", "Organización de Centros de Cómputo", uv: 4, periodo: 11, area: .humanas,
               requisitos: ["IAE-0611"]),
        Course("SIP-0620", "Telefonía y Seguridad IP", uv: 4, periodo: 11, area: .redes,
               requisitos: ["E-II"], flags: [.investigacion]),
        Course("ASE-0619", "Auditoría y Seguridad de Sistemas", uv: 4, periodo: 11, area: .redes,
               requisitos: ["IOT-0607"]),
        Course("E-III", "Electiva III (Inteligencia de Negocios)", uv: 4, periodo: 11, area: .humanas,
               requisitos: ["ARE-0807"]),
        Course("PSA-0605", "Programación para Sistemas Abiertos II", uv: 4, periodo: 11, area: .desarrollo,
               requisitos: ["PSA-0603"], flags: [.vinculacion]),

        // ─────────────────────── XII Período ─────────────────────────
        Course("PPS-1223", "Práctica Profesional Supervisada", uv: 0, periodo: 12, area: .practica,
               requisitos: ["ISE-0617", "RCE-0618"])
    ]

    /// Nota del brochure sobre la Práctica Profesional Supervisada.
    static let notaPractica = """
    Para cursar la Práctica Profesional Supervisada debes haber cursado las 2 \
    pasantías y estar llevando las dos últimas clases, o haber cursado todo el \
    plan de estudio.
    """

    // MARK: - Índices derivados

    static let porCodigo: [String: Course] = Dictionary(
        uniqueKeysWithValues: cursos.map { ($0.codigo, $0) }
    )

    static let periodos: [Int] = Array(Set(cursos.map(\.periodo))).sorted()

    static func cursosDe(periodo: Int) -> [Course] {
        cursos.filter { $0.periodo == periodo }
    }

    static let uvTotales: Int = cursos.reduce(0) { $0 + $1.uv }

    static func nombre(de codigo: String) -> String {
        porCodigo[codigo]?.nombre ?? codigo
    }
}
