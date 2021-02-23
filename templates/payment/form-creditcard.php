<style>
#netpay-card-woocommerce-warning {
    background-color: #ffce00;
}
.netpay-card-woocommerce-error {
    color: #FF0000;
}
</style>
<?php if ( $viewData['is_test'] ) : ?>
    <div id="netpay-card-woocommerce-warning">
        - MODO PRUEBAS ACTIVADO. Si realizas transacciones no se enviarán al banco emisor, utiliza la tarjeta 4000 0000 0000 0002, una fecha de expiración válida, cualquier cvv y la cuenta de correo accept@netpay.com.mx para una transacción aprobada.
    </div>
<?php endif; ?>
<div id="netpay-card-woocommerce-message" style="display:none"></div>
<div id="netpay-card-woocommerce-error" style="display:none"></div>

<p class="form-row form-row-wide netpay-required-field">
    <label for="netpay_card_number"><?php _e( 'Card number', 'netpay' ); ?></label>
    <input id="netpay_card_number" class="input-text" type="text"
        maxlength="19" autocomplete="off" placeholder="•••• •••• •••• ••••"
        name="netpay_card_number" required>
</p>

<p class="form-row form-row-wide netpay-required-field">
    <label for="netpay_card_name"><?php _e( 'Name on card', 'netpay' ); ?></label>
    <input id="netpay_card_name" class="input-text" type="text"
        maxlength="50" autocomplete="off" placeholder="<?php _e( 'FULL NAME', 'netpay' ); ?>"
        name="netpay_card_name" required>
</p>
<p class="form-row form-row-wide netpay-required-field">
    <label for="netpay_card_expiration_card">Fecha de vencimiento</label>
    <input id="netpay_card_expiration_card" class="input-text" type="text" maxlength="5"
        autocomplete="off" placeholder="<?php _e( 'MM/AA', 'netpay' ); ?>"
        name="netpay_card_expiration_card" required>
</p>
<p class="form-row form-row-wide netpay-required-field">
    <label for="netpay_card_security_code"><?php _e( 'Security code', 'netpay' ); ?></label>
    <input id="netpay_card_security_code"
        class="input-text" type="password" autocomplete="off" maxlength="4"
        placeholder="•••" name="netpay_card_security_code" required>
</p>

<input id="netpay_card_devicefingerprint"
    class="input-text" type="hidden" name="netpay_card_devicefingerprint">