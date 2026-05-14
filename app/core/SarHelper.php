<?php
/**
 * Helpers para facturación electrónica de Honduras (SAR).
 * - Numeración correlativa: 000-001-01-00000001
 * - Conversión número → letras (lempiras)
 * - Cálculo de ISV (15% / 18% / exento)
 * - Validación de RTN
 * - Alertas de CAI próximo a caducar / agotarse
 */
class SarHelper {

    /**
     * Obtiene la autorización CAI activa (la siguiente con folios disponibles).
     */
    public static function getActiveAuthorization(string $docType = 'factura'): ?array {
        return Database::fetch(
            "SELECT * FROM sar_authorizations
             WHERE document_type = ? AND active = 1
               AND next_number <= rango_final
               AND fecha_limite >= CURDATE()
             ORDER BY id ASC LIMIT 1",
            [$docType]
        );
    }

    /**
     * Asigna el siguiente número correlativo SAR e incrementa el contador.
     * Devuelve ['number' => '000-001-01-00000001', 'auth' => array].
     */
    public static function nextInvoiceNumber(string $docType = 'factura'): array {
        $auth = self::getActiveAuthorization($docType);
        if (!$auth) {
            throw new RuntimeException('No hay autorización CAI activa. Configure una en el panel SAR.');
        }
        $n = (int) $auth['next_number'];
        $number = sprintf('%s-%s-%s-%08d',
            $auth['establecimiento'],
            $auth['punto_emision'],
            $auth['tipo_documento'],
            $n
        );
        Database::update('sar_authorizations', ['next_number' => $n + 1], 'id = :id', ['id' => $auth['id']]);
        return ['number' => $number, 'auth' => $auth, 'consecutive' => $n];
    }

    /**
     * Rango autorizado formateado: "000-001-01-00000001 al 000-001-01-00001000"
     */
    public static function formatRange(array $auth): string {
        return sprintf('%s-%s-%s-%08d al %s-%s-%s-%08d',
            $auth['establecimiento'], $auth['punto_emision'], $auth['tipo_documento'], $auth['rango_inicial'],
            $auth['establecimiento'], $auth['punto_emision'], $auth['tipo_documento'], $auth['rango_final']
        );
    }

    /**
     * Convierte un monto a letras en español (formato hondureño).
     * Ej: 1250.50 → "MIL DOSCIENTOS CINCUENTA LEMPIRAS CON 50/100"
     */
    public static function numberToWords(float $amount, string $currency = 'LEMPIRAS'): string {
        $entero = (int) floor($amount);
        $decimal = (int) round(($amount - $entero) * 100);
        $letras = mb_strtoupper(self::convertNumber($entero), 'UTF-8');
        return "{$letras} {$currency} CON " . str_pad((string)$decimal, 2, '0', STR_PAD_LEFT) . '/100';
    }

    private static function convertNumber(int $n): string {
        if ($n === 0) return 'cero';
        if ($n < 0)  return 'menos ' . self::convertNumber(-$n);

        $unidades = ['','uno','dos','tres','cuatro','cinco','seis','siete','ocho','nueve','diez',
                     'once','doce','trece','catorce','quince','dieciséis','diecisiete','dieciocho','diecinueve','veinte',
                     'veintiuno','veintidós','veintitrés','veinticuatro','veinticinco','veintiséis','veintisiete','veintiocho','veintinueve'];
        $decenas = ['','','','treinta','cuarenta','cincuenta','sesenta','setenta','ochenta','noventa'];
        $centenas = ['','ciento','doscientos','trescientos','cuatrocientos','quinientos','seiscientos','setecientos','ochocientos','novecientos'];

        if ($n < 30) return $unidades[$n];
        if ($n < 100) {
            $d = intdiv($n, 10);
            $u = $n % 10;
            return $decenas[$d] . ($u ? ' y ' . $unidades[$u] : '');
        }
        if ($n === 100) return 'cien';
        if ($n < 1000) {
            $c = intdiv($n, 100);
            $r = $n % 100;
            return $centenas[$c] . ($r ? ' ' . self::convertNumber($r) : '');
        }
        if ($n < 1000000) {
            $miles = intdiv($n, 1000);
            $r = $n % 1000;
            $prefix = ($miles === 1) ? 'mil' : self::convertNumber($miles) . ' mil';
            return $prefix . ($r ? ' ' . self::convertNumber($r) : '');
        }
        $millones = intdiv($n, 1000000);
        $r = $n % 1000000;
        $prefix = ($millones === 1) ? 'un millón' : self::convertNumber($millones) . ' millones';
        return $prefix . ($r ? ' ' . self::convertNumber($r) : '');
    }

    /**
     * Calcula impuestos para una factura.
     * @param array $items array de líneas: ['quantity','unit_price','tax_rate']
     * @return array totales
     */
    public static function calculateTotals(array $items, float $discount = 0): array {
        $exento = 0; $gravado15 = 0; $gravado18 = 0;
        $isv15 = 0; $isv18 = 0;
        $subtotal = 0;
        foreach ($items as $it) {
            $line = (float)$it['quantity'] * (float)$it['unit_price'];
            $subtotal += $line;
            $rate = (float)($it['tax_rate'] ?? 15);
            if ($rate == 0) { $exento += $line; }
            elseif ($rate == 18) { $gravado18 += $line; $isv18 += $line * 0.18; }
            else { $gravado15 += $line; $isv15 += $line * 0.15; }
        }
        // Aplicar descuento proporcional sobre base gravada
        if ($discount > 0 && $subtotal > 0) {
            $factor = ($subtotal - $discount) / $subtotal;
            $gravado15 *= $factor; $gravado18 *= $factor; $exento *= $factor;
            $isv15 = $gravado15 * 0.15;
            $isv18 = $gravado18 * 0.18;
        }
        $total = $gravado15 + $gravado18 + $exento + $isv15 + $isv18;
        return [
            'subtotal'           => round($subtotal, 2),
            'discount'           => round($discount, 2),
            'importe_exento'     => round($exento, 2),
            'importe_exonerado'  => 0.00,
            'importe_gravado_15' => round($gravado15, 2),
            'importe_gravado_18' => round($gravado18, 2),
            'isv_15'             => round($isv15, 2),
            'isv_18'             => round($isv18, 2),
            'tax_amount'         => round($isv15 + $isv18, 2),
            'total'              => round($total, 2),
        ];
    }

    /**
     * Valida un RTN hondureño (14 dígitos).
     */
    public static function isValidRTN(string $rtn): bool {
        $rtn = preg_replace('/\D/', '', $rtn);
        return strlen($rtn) === 14;
    }

    /**
     * Devuelve un array de alertas si el CAI está por vencer o agotarse.
     */
    public static function getAlerts(): array {
        $alerts = [];
        $auths = Database::fetchAll('SELECT * FROM sar_authorizations WHERE active = 1');
        foreach ($auths as $a) {
            $remaining = (int)$a['rango_final'] - (int)$a['next_number'] + 1;
            $daysLeft = (strtotime($a['fecha_limite']) - time()) / 86400;
            if ($remaining <= 50 && $remaining > 0) {
                $alerts[] = "Solo quedan {$remaining} folios disponibles en el CAI {$a['cai']}.";
            }
            if ($remaining <= 0) {
                $alerts[] = "El CAI {$a['cai']} ha agotado su rango. Solicita uno nuevo a SAR.";
            }
            if ($daysLeft <= 30 && $daysLeft > 0) {
                $alerts[] = "El CAI {$a['cai']} caduca en " . (int)$daysLeft . " días.";
            }
            if ($daysLeft <= 0) {
                $alerts[] = "El CAI {$a['cai']} ha caducado el {$a['fecha_limite']}.";
            }
        }
        return $alerts;
    }
}
