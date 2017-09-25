<?php

require '../main.inc.php';
require_once '../core/class/canvas.class.php';
require_once '../product/class/product.class.php';
require_once '../product/class/html.formproduct.class.php';
require_once '../core/class/extrafields.class.php';
require_once '../core/lib/product.lib.php';
require_once '../core/lib/company.lib.php';

llxHeader();

//Validar formulario con javascript - Chequea que se ingrese un valor y que sea numérico en la cotización
print "<script>
function validarFormulario() 

{

                var a=document.forms['fexchangerate']['cotizacion'].value;
                if (a==null || a=='')
                {
                alert('Debe ingresar un valor');
                return false;
                }
				
				 if(!a.match(/^\d+/))
                {
                                alert('Por favor entre un valor numérico');
                                return false;
                }     

                

}
</script>
";

//Formulario de ingreso de cotización

print '<style> #padding {margin-top:10px; border:0px; padding:5px;}
#td {border:1px solid silver;padding:5px;}

</style><table width="70%" id="padding">';

print '<form method="get" onsubmit="validarFormulario();" action="insertrate.php" name="fexchangerate">'."\n";
print'<tr><td id="td"><strong>Nueva Cotización</strong></td><td id="td"><input type="text" name="cotizacion"></td></tr>';
print'<tr><td id="td"><strong>Moneda de Origen</strong></td><td id="td"><select name="currency"><option value="USD" name="currency">Dolar</option><option value="EUR">Euro</option></select></td></tr>';
print'<tr><td id="td" colspan="2"><input type="submit" value="Ingresar" class="butAction"</td></tr></table>';
print'</form>';

//date_default_timezone_set("America/Buenos_Aires");


llxFooter();



?>