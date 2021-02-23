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
        - MODO PRUEBAS ACTIVADO. Si realizas transacciones no se enviará referencia de pago al banco, y se enviará la referencia de pago de efectivo generada al correo electrónico que captures.
    </div>
<?php endif; ?>