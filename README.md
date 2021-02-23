=== NetPay Checkout ===

Contributors: NetPay
Tags: netpaymx, mexico, msi, cash, netpay, payment, payment gateway, woocommerce plugin, installment, woocommerce payment
Requires at least: 4.3.1
Tested up to: 5.6.1
Stable tag: 1.0.0.0
License: MIT
License URI: https://opensource.org/licenses/MIT

El plugin NetPay Checkout es la extensión de pago oficial que brinda soporte para la pasarela de pago NetPay para los constructores de tiendas que trabajan en la plataforma WooCommerce.

== Instalación ==

Después de obtener el plugin, ya sea descargándolo como un zip o clon de git, colóquelo en la carpeta de plugin de WordPress (es decir, mv netpay / wp-content / plugins / o cargue un zip a través de la sección de complementos de administración de WordPress, al igual que los otros complementos de WordPress).

Luego, el plugin de WordPress NetPay Checkout debería aparecer en la página de administración de WordPress, en el menú Plugins.
Desde allí:
1. Active el plugin
2. Vaya a WooCommerce -> Plugins
3. Seleccione la pestaña Pago en la parte superior.
4. Seleccione Pasarela de pago NetPay en la parte inferior de la página, en Pasarelas de pago.
5. Haga clic en el botón Configuración y ajuste las opciones.
6. Si utilizarás NetPay Cash, ve menú WooCommerce -> Ajustes -> Productos -> Investarios y coloca en el campo Mantener en inventario (en minutos) el valor de 14400, equivalente a 10 días que dura la referencia de pago, con el objetivo de que la órden no sea cancelada por falta de pago. (Si deseas cambiar el número de días en que expira la referencia de pago, ve a NetPay Manager).

== Screenshots ==

1. NetPay Checkout Dashboard
2. NetPay Checkout Setting Page
3. NetPay Checkout Checkout Form

== Cuenta de prueba ==
Correo: lmhqokwhffxhgrirll@miucce.com
Contraseña: w8mTa9hMmkT3pCGBg828cFMXRd.
Public key: pk_netpay_ryDNhWywMbMjqXbLzMUEeTMfW
Secret key: sk_netpay_lyNzonHFhwqoMHXfMFmOILqgZjAAjUVOjisfSkikPkrDA

Netpay Manager pruebas: https://manager-cert-term.netpaydev.com/ 

== Tarjetas de prueba ==

Débito

Visa

4000000000000002 con cualquier cvv y exp date válido (tarjeta enrolada a 3DS)

MasterCard

5200000000000007 con cualquier cvv y exp date válido (tarjeta enrolada a 3DS)

Crédito MSI

Visa

4456530000001096 con cualquier cvv y exp date válido (tarjeta enrolada a 3DS)

MasterCard

5200000000001096 con cualquier cvv y exp date válido (tarjeta enrolada a 3DS)

Amex

340000000003961 con cualquier cvv y exp date válido (tarjeta enrolada a 3DS)

Review-Reject

Visa

4000000000000010 con cualquier cvv y exp date válido (tarjeta enrolada a 3DS)

MasterCard

5200000000000015 con cualquier cvv y exp date válido (tarjeta enrolada a 3DS)

Amex

340000000006022 con cualquier cvv y exp date válido (tarjeta enrolada a 3DS)

== Correos de prueba ==

Colocar las siguientes cuentas de correo en los datos de la transacción (billing information) para obtener la respuesta mencionada.

accept@netpay.com.mx = la transacción siempre será aprobada.

review@netpay.com.mx = la transacción se mandará al 3ds para autenticar al cliente.

reject@netpay.com.mx = la transacción siempre será rechazada.

== Changelog ==

