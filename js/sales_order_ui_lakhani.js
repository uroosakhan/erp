function my_func() {
    var alt_uom = document.getElementById("alt_uom").value;
    var units = document.getElementById("units_id").value;
    var primary_unit = document.getElementById("primary_unit").value;
    var con_factor = document.getElementById("unit_factor").value;
    // var price = document.getElementById("price").value.replace(/,/g, '');
    var primary_price = document.getElementById("primary_price").value.replace(/,/g, '');
   
   
   
    var secondary_price = parseFloat(primary_price) / parseFloat(con_factor);

var n = secondary_price.toFixed(3);

    if (primary_unit != units && alt_uom == 1) {
        document.getElementById("price").value = n;
    }
   if (primary_unit == units && alt_uom == 1) {

        document.getElementById("price").value = primary_price;
    }
}

function cal_price() {

    var line_total = document.getElementById("line_total").value.replace(/,/g, '');
    var qty = document.getElementById("qty").value.replace(/,/g, '');
    var price_decimal = document.getElementById("price_decimal").value;
    var price = parseFloat(line_total) / parseFloat(qty);
    var n = price.toFixed(price_decimal);
    if (line_total != 0) {
        document.getElementById("price").value = n;
    }
    else {

        // document.getElementById("line_total").value = price2;
    }
}

function cal_price2() {

    var price = document.getElementById("price").value.replace(/,/g, '');
    var qty = document.getElementById("qty").value.replace(/,/g, '');
    var price_decimal = document.getElementById("price_decimal").value;
    var line_total = parseFloat(price) * parseFloat(qty);
    var n = line_total.toFixed(price_decimal);

    document.getElementById("line_total").value = n;
}
function cal_mmwidth()
{

    var amount1 = document.getElementById("amount1").value.replace(/,/g, '');
    // var price_decimal = document.getElementById("price_decimal").value;
    var amount2 = parseFloat(amount1) / parseFloat(25.4);
    var n = amount2.toFixed(2);
    var m = amount2.toFixed(0);

    document.getElementById("amount2").value = n;
    document.getElementById("amount5").value = m;


    var amount3 = document.getElementById("amount3").value.replace(/,/g, '');
    // var price_decimal = document.getElementById("price_decimal").value;
    var amount4 = parseFloat(amount3) / parseFloat(25.4);
    var o = amount4.toFixed(2);
    var p = amount4.toFixed(0);

    document.getElementById("amount4").value = o;
    document.getElementById("amount6").value = p;

    var text1 = (parseFloat(m) * parseFloat(p))/ parseFloat(144);
    var q = text1.toFixed(2);

    document.getElementById("text1").value = q;

    var text2 = document.getElementById("text2").value.replace(/,/g, '');
    var qty = parseFloat(text1) * parseFloat(text2);
    var r = qty.toFixed(2);

    document.getElementById("qty").value = r;

    var price = document.getElementById("price").value.replace(/,/g, '');
    var line_total = parseFloat(price) * parseFloat(r);
    var s = line_total.toFixed(2);

    document.getElementById("line_total").value = s;
}