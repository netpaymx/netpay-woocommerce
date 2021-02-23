<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright 2018 NetPay. All rights reserved.
 */

	return array(
        'admin_title' => 'Pago con tarjeta de crédito o débito',
        'thank_you_default' => 'Gracias por comprar con nosotros.',
        'receipt_page_title' => 'Gracias por su compra, por favor llena la información que se encuentra abajo para procesar el pago.',
        'method_description' => 'Acepta pagos con cargos directos o en meses sin intereses con tarjetas Visa, MasterCard y American Express.',
        'description_payment' => 'Para continuar con el procesamiento de pago de clic en Realizar el pedido.',
        'form_fields' => array(
            'enabled' => array(
                'title' => 'Habilitar/Deshabilitar',
                'label' => 'Habilitar pagos con NetPay',
            ),
            'title' => array(
                'title' => 'Título',
                'description' => 'Esto controla el título que el usuario verá durante el checkout.',
                'default' => 'Pago con tarjeta de crédito con NetPay'
            ),
            'trans_type' => array(
                'title' => 'Tipo de transacción',
                'description' => 'Selecciona el tipo de transacción.',
                'description' => 'MID = Retail',
            ),
            'mid' => array(
                'title' => 'MID',
                'description' => 'Select the MID.',
                'agencias' => 'Agencia de viajes',
                'donaciones' => 'Donaciones',
                'escuelas' => 'Escuelas',
                'tickets' => 'Eventos tickets',
                'restaurant' => 'Restaurant con entrega a domicilio',
                'retail' => 'Retail',
                'generales' => 'Servicios generales',
                'profesionales' => 'Servicios profesionales',
            ),
            'store_customer' => array(
                'title' => 'Id comercio',
            ),
            'store_user' => array(
                'title' => 'Usuario',
            ),
            'store_password' => array(
                'title' => 'Contraseña',
            ),
            'method_of_delivery' => array(
                'title' => 'Método de entrega',
            ),
            'category' => array(
                'title' => 'Categoría de producto',
                'description' => 'MID = Retail, Restaurant, Escuelas, Agencia de viajes, Servicios Profesionales y Eventos Tickets',
            ),
            'store_city' => array(
                'title' => 'Ciudad de la tienda',
                'description' => 'MID = Restaurant',
            ),
            'store_postcode' => array(
                'title' => 'Código postal de la tienda',
                'description' => 'MID = Restaurant',
            ),
            'store_primary_type_food' => array(
                'title' => 'Tipo de comida primaria',
                'description' => 'MID = Restaurant',
            ),
            'store_secundary_type_food' => array(
                'title' => 'Tipo de comida secundaria',
                'description' => 'MID = Restaurant',
            ),
            'store_level' => array(
                'title' => 'Nivel escolar',
                'description' => 'MID = Escuelas',
            ),
            'store_service_type' => array(
                'title' => 'Tipo de servicio prestado',
                'description' => 'MID = Servicios generales',
            ),
            'promotion' => array(
                'title' => 'Meses sin intereses',
                'label' => 'Activa meses sin intereses y podrás pagar de forma diferida',
                'number_months' => 'Número de meses',
                'months_without_interest_0' => 'Un solo pago',
                'months_without_interest_3' => '3 meses sin intereses',
                'months_without_interest_6' => '6 meses sin intereses',
                'months_without_interest_9' => '9 meses sin intereses',
                'months_without_interest_12' => '12 meses sin intereses',
                'months_without_interest_18' => '18 meses sin intereses',
                'months_without_interest' => ' meses sin intereses',
            ),
        ),
        'http_error' => 'Error al hacer la petición con NetPay',
        'http_codes' => array(
            0   => 'URL incorrecta.',
            400 => 'Solicitud incorrecta.',
            401 => 'No autorizado.',
            403 => 'Prohibido.',
            404 => 'No encontrado.',
            500 => 'Error de servidor interno.',
        ),
        'bank_error' => 'Error al procesar la transacción con el banco',
        'bank_codes' => array(
            '00' => 'Aprobado.',
            '01' => 'El emisor quiere contactarnos.',
            '02' => 'El emisor quiere contactarnos.',
            '03' => 'Problemas con la afiliación.',
            '04' => 'Tarjeta bloqueada.',
            '05' => 'Error General.',
            '06' => 'Problema de comunicación.',
            '07' => 'Tarjeta bloqueada.',
            '12' => 'Problemas con el tipo de operación.',
            '13' => 'Problemas con el tipo de operación.',
            '14' => 'Tarjeta con problemas.',
            '15' => 'Problemas con el emisor.',
            '30' => 'Problemas con la afiliación.',
            '31' => 'Problemas con el tipo de operación.',
            '33' => 'La tarjeta expiró.',
            '34' => 'Regla de fraude.',
            '36' => 'Tarjeta bloqueada.',
            '41' => 'Tarjeta bloqueada.',
            '43' => 'Tarjeta bloqueada.',
            '51' => 'Fondos insuficientes.',
            '54' => 'La tarjeta expiró.',
            '55' => 'Problemas con pin.',
            '56' => 'Tarjeta con problemas.',
            '57' => 'Problemas con el tipo de operación.',
            '58' => 'Problemas con el tipo de operación.',
            '61' => 'Fondos insuficientes.',
            '62' => 'Tarjeta con problemas.',
            '63' => 'Premisos insuficientes.',
            '68' => 'Problemas de comunicación.',
            '75' => 'Problemas con pin.',
            '82' => 'Tarjeta con problemas.',
            '83' => 'Tarjeta con problemas.',
            '87' => 'Tarjeta con problemas.',
            '91' => 'Procesador no disponible.',
        ),
        'checkout' => array(
            'error' => 'Error al procesar el carrito de compras.',
        ),
        'transaction' => array(
            'complete' => 'La transacción ha sido completada.',
            'error' => array(
                'callback' => 'Error de callback',
                'empty_order' => 'La transacción no coincide con ninguna orden de compra.',
                'error_bank' => 'Error de la transacción con el banco.',
                'is_complete' => 'La orden de compra %s ha sido completada.'
            ),
            'payment_complete' => 'Pago realizado correctamente [Orderid: %s, Orderid NetPay - %s, Tarjeta - **** **** **** %s], Banco - %s, Tipo de Tarjeta - %s, Meses sin intereses: %s',
        ),
        'cancelled' => array(
            'complete' => 'La orden de compra #%s se cancelo correctamente.',
            'error_bank' => 'Error al hacer la cancelación con el banco.',
            'stock_restored' => 'El producto #%s incremento de %s a %s.',
            'cannot_cancelled' => 'El día en que se pago la orden es diferente al día de hoy.',
        ),
        'client_form_fields' => array(
            'regimen_fical' => array(
                'label' => 'Régimen fiscal',
                'placeholder' => 'Régimen fiscal',
                'option1' => 'Persona física',
                'option2' => 'Persona física con actividad empresarial',
                'option3' => 'Persona moral',
            ),
            'legend' => array(
                'label' => 'Mi viaje',
            ),
            'type_travel' => array(
                'label' => 'Tipo de viaje',
                'option1' => 'Viaje sencillo',
                'option2' => 'Viaje redondo',
            ),
            'depart' => array(
                'label' => 'Ida',
                'placeholder' => 'Ida',
            ),
            'return' => array(
                'label' => 'Vuelta (Opcional)',
                'placeholder' => 'Vuelta',
            ),
            'passegers_number' => array(
                'label' => 'Cantidad de pasajeros',
            ),
            'frequency_number' => array(
                'label' => 'Número de viajero frecuente',
            ),
            'name_passenger1' => array(
                'label' => 'Nombre de pasajero 1',
            ),
            'phone_passenger1' => array(
                'label' => 'Teléfono de pasajero 1',
            ),
            'name_passenger2' => array(
                'label' => 'Nombre de pasajero 2',
            ),
            'phone_passenger2' => array(
                'label' => 'Teléfono de pasajero 2',
            ),
            'name_passenger3' => array(
                'label' => 'Nombre de pasajero 3',
            ),
            'phone_passenger3' => array(
                'label' => 'Teléfono de pasajero 3',
            ),
            'name_passenger4' => array(
                'label' => 'Nombre de pasajero 4',
            ),
            'phone_passenger4' => array(
                'label' => 'Teléfono de pasajero 4',
            ),
        ),
        'charge' => array(
            'error' => 'Error al realizar el cargo.',
            'double_charge' => 'El cargo ya se ha realizado anteriormente.',
            'type_error' => 'La transacción no es de tipo Pre/PostAuth.',
        ),
        'change_status' => array(
            'to_postauth' => 'Cambiar estatus a PostAuth',
        ),
    );
