import SwiftUI

/// Tarjeta superior con el anillo de avance y los contadores.
struct ProgresoCabecera: View {
    let progreso: PensumStore.Progreso

    var body: some View {
        HStack(spacing: 18) {
            AnilloProgreso(fraccion: progreso.fraccion, porcentaje: progreso.porcentaje)
                .frame(width: 84, height: 84)

            VStack(alignment: .leading, spacing: 6) {
                Text(PensumData.carrera)
                    .font(.headline)
                    .foregroundStyle(.white)
                Text("\(progreso.uvDescartadas) de \(progreso.uvTotales) UV descartadas")
                    .font(.subheadline)
                    .foregroundStyle(.white.opacity(0.9))
                Text("\(progreso.clasesDescartadas) clases listas · \(progreso.clasesPendientes) por llevar")
                    .font(.caption)
                    .foregroundStyle(.white.opacity(0.75))
            }
            Spacer(minLength: 0)
        }
        .padding(18)
        .background(Theme.degradado)
        .clipShape(RoundedRectangle(cornerRadius: 18, style: .continuous))
        .padding(.horizontal, 4)
        .padding(.vertical, 6)
    }
}

struct AnilloProgreso: View {
    let fraccion: Double
    let porcentaje: Int
    var grosor: CGFloat = 9

    var body: some View {
        ZStack {
            Circle()
                .stroke(Color.white.opacity(0.25), lineWidth: grosor)
            Circle()
                .trim(from: 0, to: max(0.001, fraccion))
                .stroke(Theme.dorado, style: StrokeStyle(lineWidth: grosor, lineCap: .round))
                .rotationEffect(.degrees(-90))
                .animation(.snappy, value: fraccion)
            Text("\(porcentaje)%")
                .font(.title3.weight(.bold).monospacedDigit())
                .foregroundStyle(.white)
        }
    }
}
