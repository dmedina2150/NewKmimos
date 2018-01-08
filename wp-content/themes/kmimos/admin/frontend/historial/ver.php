<?php
    
    global $wpdb;

    $orden = vlz_get_page();

    $data_reserva = kmimos_desglose_reserva_data($orden);

    $email = $wpdb->get_var("SELECT user_email FROM wp_users WHERE ID='{$data_reserva["cuidador"]}'");
    $telefonos = get_user_meta($data_reserva["cuidador"], "user_phone", true)." / ".get_user_meta($data_reserva["cuidador"], "user_mobile", true);
    $direccion = $wpdb->get_var("SELECT direccion FROM cuidadores WHERE user_id='{$data_reserva["cuidador"]}'");

    $info = '
        <div class="desglose_box">
            <div>
                <div class="sub_titulo">RESERVA</div>
                <span>'.$data_reserva["id_reserva"].'</span>
            </div>
            <div>
                <div class="sub_titulo">MEDIO DE PAGO</div>
                <span>Pago por '.$data_reserva["metodo_pago"].'</span>
            </div>
        </div>
        <div class="desglose_box datos_cuidador">
            
            <strong>CUIDADOR</strong>
            <div class="item">
                <div>Nombre</div>
                <span>
                    '.$wpdb->get_var("SELECT post_title FROM wp_posts WHERE post_author='{$data_reserva["cuidador"]}' AND post_type = 'petsitters'").'
                </span>
            </div>
            <div class="item">
                <div>Email</div>
                <span>
                    '.$email.'
                </span>
            </div>
            <div class="item">
                <div>Tel&eacute;fono</div>
                <span>
                    '.$telefonos.'
                </span>
            </div>
            <div class="item">
                <div>Direcci&oacute;n</div>
                <span>
                    '.$direccion.'
                </span>
            </div>
        </div>
    ';

    $variaciones = "";
    foreach ($data_reserva["variaciones"] as $value) {
        $variaciones .= '
            <div class="item">
                <div>'.$value[0].' '.$value[1].' x '.$value[2].' x '.get_region('mon_der').' '.$value[3].'</div>
                <span>'.get_region('mon_der').' '.$value[4].'</span>
            </div>
        ';
    }
    $variaciones = "
        <div class='desglose_box'>
            <strong>Servicio</strong>
            <div class='item'>
                <div>".$data_reserva["servicio_titulo"]."</div>
                <span>
                    <span>".$data_reserva["inicio"]."</span>
                        &nbsp; &gt; &nbsp;
                    <span>".$data_reserva["fin"]."</span>
                </span>
            </div>
        </div>
        <div class='desglose_box'>
            <strong>Mascotas</strong>
            ".$variaciones."
        </div>
    ";

    $adicionales = "";
    if( count($data_reserva["transporte"]) > 0 ){
        foreach ($data_reserva["adicionales"] as $value) {
            $adicionales .= '
                <div class="item">
                    <div>'.$value[0].' - '.$value[1].' x '.get_region('mon_der').' '.$value[2].'</div>
                    <span>'.get_region('mon_der').' '.$value[3].'</span>
                </div>
            ';
        }
        $adicionales = "
            <div class='desglose_box'>
                <strong>Servicios Adicionales</strong>
                ".$adicionales."
            </div>
        ";
    }

    $transporte = "";
    if( count($data_reserva["transporte"]) > 0 ){
        foreach ($data_reserva["transporte"] as $value) {
            $transporte .= '
                <div class="item">
                    <div>'.$value[0].'</div>
                    <span>'.get_region('mon_der').' '.$value[2].'</span>
                </div>
            ';
        }
        $transporte = "
            <div class='desglose_box'>
                <strong>Transportaci&oacute;n</strong>
                ".$transporte."
            </div>
        ";
    }

    $totales = ""; $descuento = "";

    if( $data_reserva["descuento"]+0 > 0 ){
        $descuento = "
            <div class='item'>
                <div>Descuento</div>
                <span>".get_region('mon_der').' '.number_format( $data_reserva["descuento"], 2, ',', '.')."</span>
            </div>
        ";
    }

    if( $data_reserva["desglose"]["enable"] == "yes" ){
        
        $totales = "
            <div class='desglose_box totales'>
                <strong>Totales</strong>
                <div class='item'>
                    <div class='pago_en_efectivo'>Monto a pagar en EFECTIVO al cuidador</div>
                    <span>".get_region('mon_der').' '.number_format( ($data_reserva["desglose"]["remaining"]-$data_reserva["descuento"]), 2, ',', '.')."</span>
                </div>
                <div class='item'>
                    <div>Pagado</div>
                    <span>".get_region('mon_der').' '.number_format( $data_reserva["desglose"]["deposit"], 2, ',', '.')."</span>
                </div>
                ".$descuento."
                <div class='item total'>
                    <div>Total</div>
                    <span>".get_region('mon_der').' '.number_format( $data_reserva["desglose"]["total"], 2, ',', '.')."</span>
                </div>
            </div>
        ";
    }else{
        
        $totales = "
            <div class='desglose_box totales'>
                <strong>Totales</strong>
                <div class='item'>
                    <div>Pagado</div>
                    <span>".get_region('mon_der').' '.number_format( $data_reserva["desglose"]["deposit"]-$data_reserva["descuento"], 2, ',', '.')."</span>
                </div>
                ".$descuento."
                <div class='item total'>
                    <div>Total</div>
                    <span>".get_region('mon_der').' '.number_format( $data_reserva["desglose"]["deposit"], 2, ',', '.')."</span>
                </div>
            </div>
        ";
    }

    $CONTENIDO .= 
        "
        <div class='volver'>
            <a href='".get_home_url()."/perfil-usuario/historial/'>Volver</a>
        </div>
        <div class='desglose_container'>".
            $info.
            $variaciones.
            $adicionales.
            $transporte.
            $totales.
        "</div>"
    ;

?>