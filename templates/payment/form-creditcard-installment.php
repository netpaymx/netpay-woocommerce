<style>
#netpay-installment-woocommerce-warning {
    background-color: #ffce00;
}
/*#netpay-installment-woocommerce-message {
    background-color: #e2401c;
}
#netpay-installment-woocommerce-error {
    background-color: #e2401c;
}*/
.netpay-installment-woocommerce-error {
    color: #FF0000;
}
</style>
<?php if ( $viewData['is_test'] ) : ?>
	<div id="netpay-installment-woocommerce-warning">
		- MODO PRUEBAS ACTIVADO. Si realizas transacciones no se enviarán al banco emisor, utiliza la tarjeta 4000 0000 0000 0002, una fecha de expiración válida, cualquier cvv y la cuenta de correo accept@netpay.com.mx para una transacción aprobada.
	</div>
<?php endif; ?>

<?php if ( $viewData['amount'] >= 600 ) : ?>
<div id="netpay-installment-woocommerce-message" style="display:none"></div>
<div id="netpay-installment-woocommerce-error" style="display:none"></div>

<p class="form-row form-row-wide netpay-required-field">
	<label for="netpay_installment_number"><?php _e( 'Card number', 'netpay' ); ?></label>
	<input id="netpay_installment_number" class="input-text" type="text"
		maxlength="19" autocomplete="off" placeholder="•••• •••• •••• ••••"
		name="netpay_installment_number" required>
</p>

<p class="form-row form-row-wide netpay-required-field">
	<label for="netpay_installment_name"><?php _e( 'Name on card', 'netpay' ); ?></label>
	<input id="netpay_installment_name" class="input-text" type="text"
		maxlength="50" autocomplete="off" placeholder="<?php _e( 'FULL NAME', 'netpay' ); ?>"
		name="netpay_installment_name" required>
</p>
<p class="form-row form-row-wide netpay-required-field">
	<label for="netpay_installment_expiration_card">Fecha de vencimiento</label>
	<input id="netpay_installment_expiration_card" class="input-text" type="text" maxlength="5"
		autocomplete="off" placeholder="<?php _e( 'MM/AA', 'netpay' ); ?>"
		name="netpay_installment_expiration_card" required>
</p>
<p class="form-row form-row-wide netpay-required-field">
	<label for="netpay_installment_security_code"><?php _e( 'Security code', 'netpay' ); ?></label>
	<input id="netpay_installment_security_code"
		class="input-text" type="password" autocomplete="off" maxlength="4"
		placeholder="•••" name="netpay_installment_security_code" required>
</p>

<?php if ( count($viewData['installment_promotions']) > 1 ) : ?>
<div id="netpay_promotion_div">
<p class="form-row form-row-wide netpay-required-field">
	<label for="netpay_installment_promotion"><?php _e( 'Promoción', 'netpay' ); ?></label>
	<select class="input-text" id="netpay_installment_promotion" name="netpay_installment_promotion">
	<?php foreach ( $viewData['installment_promotions'] as $promotions ) : ?>
		<option value="<?php echo  $promotions['number']; ?>"><?php echo  $promotions['lang']; ?></option>
	<?php endforeach; ?>
	</select>
</p>
</div>
<?php endif; ?>

<input id="netpay_installment_devicefingerprint"
	class="input-text" type="hidden" name="netpay_installment_devicefingerprint">

<input id="netpay_installment_promotion_hidden"
	class="input-text" type="hidden" name="netpay_installment_promotion_hidden" value="1">

<?php else: ?>
<br>
	<div id="netpay-installment-woocommerce-warning">
		La cantidad debe de ser mayor o igual a 600 para aplicar a meses sin intereses
	</div>
<?php endif; ?>